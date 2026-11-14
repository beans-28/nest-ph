<?php

namespace App\Http\Controllers;

use App\Mail\TenantAccountCreatedMail;
use App\Models\Bed;
use App\Models\BillingStatement;
use App\Models\LeaseContract;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Services\TextbeeService;

class TenantController extends Controller
{
    private const TENANT_TYPES = ['student', 'working_student', 'full_time_employee', 'part_time_employee'];

    /**
     * Tenant Manager admin page — Table 14 (Manage Tenant Records).
     * Whole tenant list rendered once, then searched/filtered/paginated
     * client-side in JS, same pattern as Lease Management and Delinquency.
     */
    public function page(Request $request)
    {
        BillingStatement::syncOverdueStatuses();

        $overdueTenantIds = BillingStatement::where('status', 'overdue')->pluck('tenant_id');

        // full_name is a computed accessor now (first_name + last_name),
        // not a real column, so it can't be used in orderBy() -- sort on
        // the real columns instead.
        // Lease statuses (expiring soon / expired) are worked out by the
        // lease controller; run the same update so the filter is current.
        app(\App\Http\Controllers\Api\LeaseContractController::class)->syncExpiringAndExpired();

        $tenants = Tenant::with(['activeContract.bed.room', 'latestContract'])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $rows = $tenants->map(fn (Tenant $tenant) => $this->transformRow($tenant, $overdueTenantIds))->values();

        // Matches DashboardController's "Total Tenants" definition exactly:
        // truly occupying a room right now. That means status is 'active'
        // or 'delinquent' (delinquent tenants are still living there, just
        // behind on payment) -- explicitly NOT 'pending_move_in_payment'
        // (no occupancy yet, hence "Not Assigned" rooms) and NOT
        // blacklisted (barred, no longer counted as an occupant).
        // Matches DashboardController's "Total Tenants" definition exactly:
        // real tenants.status is 'active' (not 'pending_move_in_payment',
        // not 'inactive') and not blacklisted.
        $activeCount = $rows->filter(
            fn ($row) => $row['raw_status'] === 'active' && ! $row['is_blacklisted']
        )->count();

        $leaseCounts = [
            'expiring_soon' => $rows->where('lease_status', 'expiring_soon')->count(),
            'expired' => $rows->where('lease_status', 'expired')->count(),
            'notarized_review' => $rows->where('notarized_status', 'submitted')->count(),
        ];

        $user = $request->user()?->loadMissing('privileges');

        return view('tenantmanager', [
            'tenants' => $rows,
            'totalCount' => $activeCount,
            'leaseCounts' => $leaseCounts,
            // Lease actions (renew, terminate, notarized copies...) need the
            // "manage contracts" privilege; billing links need "manage billing".
            'canManageLeases' => (bool) $user?->hasPrivilege('manage_contracts'),
            'canManageBilling' => (bool) $user?->hasPrivilege('manage_billing'),
        ]);
    }

    /**
     * Table 14, step 3 — full profile for the View drawer. Also used to
     * pre-fill the Edit modal, so there's one source of truth for what a
     * tenant's full record looks like.
     */
    /**
     * Security deposit block for the Tenant Manager drawer (v39): what's
     * held, any refund already recorded, and suggested deductions
     * (unpaid balance + active penalties not yet on a bill).
     */
    private function depositSummary(Tenant $tenant, float $outstanding): array
    {
        $refund = \App\Models\DepositRefund::where('tenant_id', $tenant->id)->with('recordedBy:id,name')->latest('id')->first();
        $unbilled = (float) \App\Models\Penalty::where('tenant_id', $tenant->id)->where('status', 'active')->whereNull('billing_id')->sum(\DB::raw(\App\Models\Penalty::CHARGED_SQL));

        return [
            // 'held' is what's LEFT after penalties paid from the deposit
            // (Pureza revision 2026-11-14); 'paid' is the original deposit.
            'paid' => \App\Models\DepositRefund::depositHeldFor($tenant),
            'used_for_penalties' => \App\Models\DepositRefund::usedForPenalties($tenant),
            'held' => max(0, round(\App\Models\DepositRefund::depositHeldFor($tenant) - \App\Models\DepositRefund::usedForPenalties($tenant), 2)),
            // Pureza rule: non-refundable before the minimum stay (6 months).
            'eligibility' => \App\Models\DepositRefund::eligibilityFor($tenant),
            'suggested_deductions' => round($outstanding + $unbilled, 2),
            // Rent paid ahead but never used (e.g. moving out early): returned to the tenant at move-out.
            'unused_pay_ahead' => ($contract = $tenant->contracts()->latest('id')->first())
                ? \App\Services\PayAhead::unusedCredit($contract) : 0.0,
            'refund' => $refund ? [
                'deposit_amount' => (float) $refund->deposit_amount,
                'deductions_amount' => (float) $refund->deductions_amount,
                'deductions_note' => $refund->deductions_note,
                'refund_amount' => (float) $refund->refund_amount,
                'forfeited' => (bool) $refund->forfeited,
                'refund_method' => $refund->refund_method,
                'reference_number' => $refund->reference_number,
                'refunded_at' => $refund->refunded_at->format('M j, Y'),
                'recorded_by' => $refund->recordedBy?->name,
            ] : null,
        ];
    }

    /**
     * POST /tenant-manager/{tenant}/deposit-refund (v39) — record the
     * security deposit given back at move-out. One per tenant.
     */
    public function recordDepositRefund(Request $request, Tenant $tenant): JsonResponse
    {
        $data = $request->validate([
            'deposit_amount' => ['required', 'numeric', 'min:0.01'],
            'deductions_amount' => ['required', 'numeric', 'min:0'],
            'deductions_note' => ['nullable', 'string', 'max:500', 'required_unless:deductions_amount,0'],
            'refund_method' => ['required', 'string', 'max:60'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'refunded_at' => ['required', 'date', 'before_or_equal:today'],
        ], [
            'deductions_note.required_unless' => 'Please say what the deductions are for.',
            'refunded_at.before_or_equal' => 'The refund date cannot be in the future.',
        ]);

        // Pureza rule: a tenant who stayed less than the required months
        // forfeits the whole deposit, whatever the admin typed.
        $eligibility = \App\Models\DepositRefund::eligibilityFor($tenant);
        $forfeited = ! $eligibility['refundable'];
        if ($forfeited) {
            $data['deductions_amount'] = $data['deposit_amount'];
            $data['deductions_note'] = "Forfeited: stayed {$eligibility['months_stayed']} "
                . \Illuminate\Support\Str::plural('month', $eligibility['months_stayed'])
                . " (deposit is non-refundable before {$eligibility['required_months']} months)";
        }

        if ((float) $data['deductions_amount'] > (float) $data['deposit_amount']) {
            return response()->json(['message' => 'Deductions cannot be more than the deposit.'], 422);
        }

        if (\App\Models\DepositRefund::where('tenant_id', $tenant->id)->exists()) {
            return response()->json(['message' => 'A deposit refund is already recorded for this tenant.'], 409);
        }

        $refund = \App\Models\DepositRefund::create($data + [
            'tenant_id' => $tenant->id,
            'refund_amount' => round((float) $data['deposit_amount'] - (float) $data['deductions_amount'], 2),
            'forfeited' => $forfeited,
            'recorded_by' => $request->user()?->id,
        ]);

        if ($forfeited) {
            \App\Models\TenantNotification::send($tenant->id, 'deposit_refunded',
                'Your security deposit was forfeited',
                'Deposits are non-refundable for stays shorter than ' . $eligibility['required_months'] . ' months. You stayed ' . $eligibility['months_stayed'] . '.',
                '/billing');
        } else
        \App\Models\TenantNotification::send($tenant->id, 'deposit_refunded',
            'Your security deposit refund of ₱' . number_format((float) $refund->refund_amount, 2) . ' was recorded',
            'Sent via ' . $refund->refund_method . ' on ' . $refund->refunded_at->format('F j, Y') . '.'
                . ((float) $refund->deductions_amount > 0 ? ' Deductions: ₱' . number_format((float) $refund->deductions_amount, 2) . ' (' . $refund->deductions_note . ').' : ''),
            '/billing');

        Log::info('[deposit] refund recorded', ['tenant_id' => $tenant->id, 'refund_id' => $refund->id, 'by' => $request->user()?->id]);

        return response()->json([
            'message' => 'Deposit refund recorded.',
            'deposit' => $this->depositSummary($tenant, (float) BillingStatement::where('tenant_id', $tenant->id)
                ->owed()->withApprovedPaid()->get()->sum(fn ($b) => $b->remainingBalance())),
        ], 201);
    }

    /** GET /tenant-manager/{tenant}/statement-of-account (v39) */
    public function statementOfAccountPdf(Tenant $tenant)
    {
        return \App\Services\StatementOfAccountPdf::download($tenant);
    }

    public function show(Tenant $tenant): JsonResponse
    {
        $tenant->load(['activeContract.bed.room']);

        $outstanding = $tenant->billingStatements()
            ->owed()
            ->sum('total_amount');

        $contract = $tenant->activeContract;

        // Details only the online application has (gender, school, etc.).
        $application = \App\Models\Application::where('tenant_id', $tenant->id)->latest('id')->first();
        $firstContract = $tenant->contracts()->whereNotNull('start_date')->orderBy('start_date')->first();
        $overdueIds = BillingStatement::where('status', 'overdue')->where('tenant_id', $tenant->id)->pluck('tenant_id');

        return response()->json([
            'id' => $tenant->id,
            'display_status' => $this->deriveStatus($tenant, $overdueIds),
            'tenant_since' => $this->formatDate($firstContract?->start_date, 'M j, Y'),
            'emergency_contact_email' => $tenant->emergency_contact_email,
            'application' => $application ? [
                'gender' => $application->gender,
                'nationality' => $application->nationality,
                'occupation' => $application->occupation,
                'school_company' => $application->school_company,
                'school_company_address' => $application->school_company_address,
                'medical_condition' => $application->medical_condition,
                'landline' => $application->landline,
                'emergency_contact_relation' => $application->emergency_contact_relation,
                'submitted_at' => $this->formatDate($application->created_at, 'M j, Y'),
            ] : null,
            'full_name' => $tenant->full_name,
            'first_name' => $tenant->first_name,
            'last_name' => $tenant->last_name,
            'email' => $tenant->email,
            'contact_number' => $tenant->contact_number,
            'date_of_birth' => $this->formatDate($tenant->date_of_birth),
            'home_address' => $tenant->home_address,
            'tenant_type' => $tenant->tenant_type,
            'emergency_contact_name' => $tenant->emergency_contact_name,
            'emergency_contact_number' => $tenant->emergency_contact_number,
            'emergency_billing_reminders' => (bool) $tenant->emergency_billing_reminders,
            'status' => $tenant->status,
            'is_blacklisted' => (bool) $tenant->is_blacklisted,
            'deactivation_reason' => $tenant->deactivation_reason,
            'deactivated_at' => $this->formatDate($tenant->deactivated_at, 'M j, Y g:ia'),
            'deactivated_by_name' => $tenant->deactivatedBy?->name,
            'id_document_url' => $this->publicUrlFor($tenant->id_document_path),
            'id_photo_url' => $tenant->id_photo_url,
            'initials' => $tenant->initials,
            'emergency_contact_id_url' => $this->publicUrlFor($tenant->emergency_contact_id_path),
            'signed_contract_url' => $this->publicUrlFor($tenant->signed_contract_path),
            'contract' => $contract ? [
                'id' => $contract->id,
                'room_no' => $contract->bed?->room?->room_no,
                'bed_label' => $contract->bed?->bed_label,
                'start_date' => $this->formatDate($contract->start_date, 'M j, Y'),
                'end_date' => $this->formatDate($contract->end_date, 'M j, Y'),
                'monthly_rate' => $contract->monthly_rate,
            ] : null,
            'outstanding_balance' => (float) $outstanding,
            'payments_count' => $tenant->payments()->count(),
            'deposit' => $this->depositSummary($tenant, (float) $outstanding),
        ]);
    }

    /**
     * GET /tenant-manager/{tenant}/payments (Pureza revision 2026-11-14):
     * the tenant's bills, every payment with its uploaded proof (the same
     * proof the tenant sees on their Billing page) and their penalties,
     * including how much of each penalty the security deposit covered.
     */
    public function paymentHistory(Tenant $tenant): JsonResponse
    {
        $bills = $tenant->billingStatements()->withApprovedPaid()
            ->orderByDesc('billing_period_start')->orderByDesc('id')->get()
            ->map(fn (BillingStatement $b) => [
                'id' => $b->id,
                'label' => match ($b->type) {
                    'move_in' => 'Move-in fee',
                    BillingStatement::PAY_AHEAD => 'Rent paid ahead',
                    default => 'Rent for ' . $b->billing_period_start?->format('F Y'),
                },
                'due_date' => $this->formatDate($b->due_date, 'M j, Y'),
                'total' => (float) $b->total_amount,
                'penalty' => (float) $b->penalty_amount,
                'balance' => $b->remainingBalance(),
                'status' => $b->status,
            ])->values();

        $billLabels = $bills->pluck('label', 'id');

        $payments = $tenant->payments()->with(['recordedBy:id,name'])
            ->orderByDesc('payment_date')->orderByDesc('id')->get()
            ->map(fn (\App\Models\Payment $p) => [
                'id' => $p->id,
                'bill' => $billLabels[$p->billing_id] ?? '—',
                'amount' => (float) $p->amount_paid,
                'method' => $p->payment_method_label ?: $p->payment_method,
                'reference' => $p->reference_number,
                'payment_date' => $this->formatDate($p->payment_date, 'M j, Y'),
                'submitted_at' => $this->formatDate($p->created_at, 'M j, Y g:ia'),
                'status' => $p->status,
                'proof_url' => $this->publicUrlFor($p->proof_path),
                'review_notes' => $p->review_notes,
                'reviewed_at' => $this->formatDate($p->reviewed_at, 'M j, Y g:ia'),
                'recorded_by' => $p->recordedBy?->name,
                'receipt_url' => $p->status === 'approved' ? url("/payments/{$p->id}/receipt") : null,
            ])->values();

        $penalties = $tenant->penalties()->orderByDesc('date_incurred')->orderByDesc('id')->get()
            ->map(fn (\App\Models\Penalty $p) => [
                'id' => $p->id,
                'description' => $p->description,
                'type' => $p->type,
                'amount' => (float) $p->amount,
                'from_deposit' => (float) $p->deposit_deducted,
                'charged' => $p->chargedAmount(),
                'status' => $p->status,
                'date' => $this->formatDate($p->date_incurred, 'M j, Y'),
                'billed' => (bool) $p->billing_id,
            ])->values();

        return response()->json([
            'bills' => $bills,
            'payments' => $payments,
            'penalties' => $penalties,
            'totals' => [
                'paid' => round($payments->where('status', 'approved')->sum('amount'), 2),
                'outstanding' => round($bills->whereIn('status', ['unpaid', 'partial', 'overdue'])->sum('balance'), 2),
                'pending_review' => $payments->where('status', 'pending')->count(),
            ],
        ]);
    }

    /**
     * GET /tenant-manager/{tenant}/activity (Pureza revision 2026-11-14):
     * one timeline of what happened on this account -- portal notifications
     * sent to the tenant, delinquency steps (SMS, restrictions, letters) and
     * billing emails -- newest first.
     */
    public function activity(Tenant $tenant): JsonResponse
    {
        $items = collect();

        foreach (\App\Models\TenantNotification::where('tenant_id', $tenant->id)->latest('id')->limit(150)->get() as $n) {
            $items->push(['at' => $n->created_at, 'kind' => 'notification', 'title' => $n->title, 'detail' => $n->body, 'status' => null]);
        }
        foreach ($tenant->escalationLogs()->with('performedBy:id,name')->latest('id')->limit(150)->get() as $log) {
            $type = (string) $log->action_type;
            $items->push([
                'at' => $log->created_at,
                'kind' => (str_starts_with($type, 'guardian_') || $type === 'emergency_contact_notified') ? 'guardian' : 'delinquency',
                'title' => 'Stage ' . $log->stage . ': ' . ucfirst(str_replace('_', ' ', $type)),
                'detail' => str_ends_with((string) $log->message_content, '.pdf') ? 'Demand letter PDF generated.' : $log->message_content,
                'status' => $log->status,
                'by' => $log->performedBy?->name,
            ]);
        }
        foreach (\App\Models\BillingEmailLog::where('tenant_id', $tenant->id)->latest('id')->limit(150)->get() as $e) {
            $items->push([
                'at' => $e->created_at,
                'kind' => 'email',
                'title' => 'Email to ' . ($e->recipient_type === 'guardian' ? 'guardian' : 'tenant') . ': ' . str_replace('_', ' ', $e->kind),
                'detail' => $e->email . ($e->error ? ' (error: ' . $e->error . ')' : ''),
                'status' => $e->status,
            ]);
        }

        return response()->json(
            $items->filter(fn ($i) => $i['at'])->sortByDesc('at')->take(200)
                ->map(fn ($i) => array_merge($i, ['at' => \Illuminate\Support\Carbon::parse($i['at'])->format('M j, Y g:ia')]))->values()
        );
    }

    /**
     * Table 15 — Add New Tenant. Covers walk-in registration: creates the
     * login account + tenant record + initial lease + assigns the bed, all
     * in one transaction, mirroring how ApplicationController::approve()
     * does the same thing for online applicants.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date'],
            'home_address' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:150', 'unique:tenants,email'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_number' => ['nullable', 'string', 'max:20'],
            'tenant_type' => ['nullable', Rule::in(self::TENANT_TYPES)],
            'id_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'id_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'signed_contract' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'bed_id' => ['required', 'integer', 'exists:beds,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'monthly_rate' => ['nullable', 'numeric', 'min:0'],
        ], [
            'email.unique' => 'A tenant record with this email address already exists.',
            'end_date.after' => 'Lease end date must be after the start date.',
        ]);

        $bed = Bed::with('room')->findOrFail($data['bed_id']);

        if ($bed->status !== 'vacant') {
            return response()->json([
                'message' => 'That bedspace is not available and cannot be assigned.',
            ], 409);
        }

        $idDocPath = $request->file('id_document')->store('tenant-documents', 'public');
        $idPhotoPath = $request->hasFile('id_photo') ? $request->file('id_photo')->store('id-photos', 'public') : null;
        $signedContractPath = $request->hasFile('signed_contract')
            ? $request->file('signed_contract')->store('tenant-documents', 'public')
            : null;

        [$tenant, $temporaryPassword] = DB::transaction(function () use ($data, $bed, $idDocPath, $idPhotoPath, $signedContractPath, $request) {
            $tenantRole = Role::firstOrCreate(['role_name' => 'tenant']);
            $temporaryPassword = \App\Support\TemporaryPassword::generate();

            // The `users` table only has a single `name` column -- there is
            // no first_name/last_name there, so we combine them here rather
            // than passing fields User doesn't have (Eloquent would have
            // silently dropped them anyway since they're not in $fillable).
            $user = User::forceCreate([
                'name' => trim($data['first_name'].' '.$data['last_name']),
                'email' => $data['email'],
                'password' => Hash::make($temporaryPassword),
                'role_id' => $tenantRole->id,
                'is_active' => true,
            ]);

            $tenant = Tenant::create([
                'user_id' => $user->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'contact_number' => $data['contact_number'] ?? null,
                'email' => $data['email'],
                'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                'emergency_contact_number' => $data['emergency_contact_number'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'home_address' => $data['home_address'] ?? null,
                'tenant_type' => $data['tenant_type'] ?? null,
                'id_document_path' => $idDocPath,
                'id_photo_path' => $idPhotoPath,
                'signed_contract_path' => $signedContractPath,
                // A walk-in registration is completed entirely by the
                // admin, bypassing the online Pay Move-In Fees flow that
                // pending_move_in_payment tenants go through -- so the
                // tenant is active immediately (Table 15). Worth a
                // deliberate call from BAGUI if a walk-in flow ever needs
                // its own payment step too.
                'status' => 'active',
            ]);

            $baseRate = $data['monthly_rate'] ?? $bed->room->perBedRate();

            LeaseContract::create([
                'tenant_id' => $tenant->id,
                'bed_id' => $bed->id,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'monthly_rate' => $baseRate,
                'esign_status' => $signedContractPath ? 'signed' : 'not_applicable',
                'signed_document_url' => $signedContractPath,
                'signed_at' => $signedContractPath ? now() : null,
                'status' => 'active',
                'created_by' => $request->user()?->id,
                'approved_by' => $request->user()?->id,
            ]);

            $bed->update(['status' => 'occupied']);
            $bed->room->syncStatusFromBeds();

            return [$tenant, $temporaryPassword];
        });

        try {
            Mail::to($tenant->email)->send(new TenantAccountCreatedMail($tenant, $temporaryPassword));
        } catch (\Throwable $e) {
            Log::warning('Tenant account creation email failed to send.', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'message' => "Tenant registered successfully. Login credentials sent to {$tenant->email}.",
            'tenant' => $this->transformRow(
                $tenant->fresh(['activeContract.bed.room']),
                BillingStatement::where('status', 'overdue')->pluck('tenant_id')
            ),
        ], 201);
    }

    /**
     * Table 14, steps 4-7 — Edit tenant record.
     */
    public function update(Request $request, Tenant $tenant): JsonResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date'],
            'home_address' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:150', Rule::unique('tenants', 'email')->ignore($tenant->id)],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_number' => ['nullable', 'string', 'max:20'],
            'emergency_contact_email' => ['nullable', 'email', 'max:150'],
            // Tenant Agreement 9.3/9.4: on only with the emergency contact's
            // written consent; turned off when they ask to opt out.
            'emergency_billing_reminders' => ['sometimes', 'boolean'],
            'tenant_type' => ['nullable', Rule::in(self::TENANT_TYPES)],
            'id_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'id_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'signed_contract' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ], [
            'email.unique' => 'A tenant record with this email address already exists.',
        ]);

        if ($request->hasFile('id_photo')) {
            $data['id_photo_path'] = $request->file('id_photo')->store('id-photos', 'public');
        } elseif ($request->boolean('remove_id_photo')) {
            $data['id_photo_path'] = null;
        }
        if ($request->hasFile('id_document')) {
            $data['id_document_path'] = $request->file('id_document')->store('tenant-documents', 'public');
        }
        if ($request->hasFile('signed_contract')) {
            $data['signed_contract_path'] = $request->file('signed_contract')->store('tenant-documents', 'public');
        }
        unset($data['id_document'], $data['id_photo'], $data['signed_contract']);

        $tenant->update($data);

        // Keeps the login account's name/email in sync with the tenant
        // record, so the portal login and this page never drift apart.
        // `users` only has a single `name` column, so combine first/last
        // here rather than writing fields that don't exist on User.
        if ($tenant->user) {
            $tenant->user->update([
                'name' => trim($data['first_name'].' '.$data['last_name']),
                'email' => $data['email'],
            ]);
        }

        return response()->json([
            'message' => 'Record updated successfully.',
            'tenant' => $this->transformRow(
                $tenant->fresh(['activeContract.bed.room']),
                BillingStatement::where('status', 'overdue')->pluck('tenant_id')
            ),
        ]);
    }

    /**
     * Table 37 — Deactivate Tenant Account (and its reverse, Reactivate,
     * which the manuscript doesn't name separately but which the "Set
     * Status" action needs to be reversible).
     *
     * Note: there is no separate move-out flow (the Record Occupancy
     * Transaction use case was removed from the manuscript), so
     * deactivating here also releases the tenant's current bed/lease, so a
     * deactivated tenant is never left showing as still occupying a room.
     */
    public function setStatus(Request $request, Tenant $tenant): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        if ($data['status'] === 'inactive') {
            if ($tenant->status === 'inactive') {
                return response()->json(['message' => 'This tenant is already deactivated.'], 409);
            }

            if (empty($data['reason'])) {
                return response()->json(['message' => 'A reason is required to deactivate this account.'], 422);
            }

            $outstanding = $tenant->billingStatements()
                ->owed()
                ->sum('total_amount');

            if ($outstanding > 0) {
                return response()->json([
                    'message' => 'This tenant has an unpaid balance of ₱' . number_format($outstanding, 2) . '. Please settle before deactivating.',
                ], 409);
            }

            DB::transaction(function () use ($tenant, $data, $request) {
                $tenant->update([
                    'status' => 'inactive',
                    'deactivation_reason' => $data['reason'],
                    'deactivated_at' => now(),
                    'deactivated_by' => $request->user()?->id,
                ]);

                if ($tenant->user) {
                    $tenant->user->update(['is_active' => false]);
                }

                $contract = $tenant->activeContract;
                if ($contract) {
                    $contract->update([
                        'status' => 'terminated',
                        'termination_reason' => $data['reason'],
                        'terminated_at' => now(),
                    ]);
                    $bed = $contract->bed;
                    if ($bed) {
                        $bed->update(['status' => 'vacant']);
                        $bed->room?->syncStatusFromBeds();
                    }
                }
            });

            // Table 42 trigger: "the system marks a tenant's status as
            // Moved Out, which triggers an SMS/system notification
            // prompting the tenant to leave a review." Sent outside the
            // transaction so a slow/unreachable SMS gateway never holds
            // the DB lock -- same pattern as the eviction notice send.
            //
            // Deliberately skipped for blacklisted tenants: Stage 6
            // blacklisting never touches tenants.status on its own, so
            // reaching this line with is_blacklisted still true means an
            // admin evicted someone and is now separately deactivating
            // their record -- not a normal move-out, and not something
            // we want to thank them for.
            if (! $tenant->is_blacklisted) {
                $reviewUrl = route('tenant.account');
                $reviewSmsMessage = "Hi {$tenant->first_name}, you've been marked as moved out from "
                    . TextbeeService::BRAND_NAME . '. Thank you for staying with us! '
                    . "Log in to your account at {$reviewUrl} to leave a quick review of your stay.";

                $sent = app(TextbeeService::class)->send($tenant->contact_number ?? '', $reviewSmsMessage);

                Log::info('[reviews] Move-out review SMS ' . ($sent ? 'sent' : 'failed'), [
                    'tenant_id' => $tenant->id,
                ]);
            }

            $message = 'Tenant account deactivated successfully.';
        } else {
            // Delinquency is derived (blacklisted or an overdue bill), not
            // a tenants.status value, so flipping the column can't clear
            // it -- only settling the overdue bill (or a Reset, for a blacklisted
            // tenant with no overdue bill) can. Reject instead of reporting a success that changes nothing.
            $isDelinquent = $tenant->is_blacklisted
                || $tenant->billingStatements()->where('status', 'overdue')->exists();

            if ($isDelinquent) {
                return response()->json([
                    'message' => "A delinquent account can't be set to active. It clears automatically once the tenant's overdue balance is paid and confirmed.",
                ], 409);
            }

            if ($tenant->status === 'active') {
                return response()->json(['message' => 'This tenant is already active.'], 409);
            }

            $tenant->update([
                'status' => 'active',
                'deactivation_reason' => null,
                'deactivated_at' => null,
                'deactivated_by' => null,
            ]);

            if ($tenant->user) {
                $tenant->user->update(['is_active' => true]);
            }

            $message = 'Tenant account reactivated successfully.';
        }

        return response()->json([
            'message' => $message,
            'tenant' => $this->transformRow(
                $tenant->fresh(['activeContract.bed.room']),
                BillingStatement::where('status', 'overdue')->pluck('tenant_id')
            ),
        ]);
    }

    private function transformRow(Tenant $tenant, $overdueTenantIds): array
    {
        $contract = $tenant->activeContract;
        $bed = $contract?->bed;
        $room = $bed?->room;

        return [
            'id' => $tenant->id,
            'full_name' => $tenant->full_name,
            'email' => $tenant->email,
            'contact_number' => $tenant->contact_number,
            'room_bed' => $room ? "Room {$room->room_no} - {$bed->bed_label}" : null,
            'date_started' => $contract?->start_date ? Carbon::parse($contract->start_date)->format('M Y') : null,
            'date_started_raw' => $contract?->start_date ? Carbon::parse($contract->start_date)->format('Y-m-d') : null,
            'contract_created_raw' => $contract?->created_at?->toIso8601String(),
            'monthly_rate' => $contract?->monthly_rate,
            'status' => $this->deriveStatus($tenant, $overdueTenantIds),
            // The real tenants.status column, kept separate from the
            // derived display label above -- deriveStatus() can label a
            // pending-move-in tenant "delinquent" (an overdue move-in fee,
            // say) even though they've never actually occupied a room.
            // Counting who genuinely occupies a room needs this raw value,
            // not the display label.
            'raw_status' => $tenant->status,
            'is_blacklisted' => (bool) $tenant->is_blacklisted,
            'tenant_type' => $tenant->tenant_type,
            'id_photo_url' => $tenant->id_photo_url,
            'initials' => $tenant->initials,
            // Newest lease's status, for the Lease filter (replaces the old
            // Lease Management page's tabs).
            'lease_status' => $tenant->latestContract?->status,
            'lease_end' => $tenant->latestContract?->end_date?->format('M j, Y'),
            'notarized_status' => $tenant->latestContract?->notarized_status,
        ];
    }

    private function deriveStatus(Tenant $tenant, $overdueTenantIds): string
    {
        if ($tenant->status === 'inactive') {
            return 'inactive';
        }

        if ($tenant->is_blacklisted || $overdueTenantIds->contains($tenant->id)) {
            return 'delinquent';
        }

        if ($tenant->status === 'pending_move_in_payment') {
            return 'pending_move_in_payment';
        }

        return 'active';
    }

        /**
     * Handles Carbon instances or plain strings the same way, and always
     * formats in the app's local timezone rather than letting an
     * unformatted Carbon object serialize to JSON as UTC (which shifted
     * dates back a day and printed a raw ISO timestamp instead of a date).
     */
    private function formatDate($value, string $format = 'Y-m-d'): ?string
    {
        if (! $value) {
            return null;
        }

        return $value instanceof \Illuminate\Support\Carbon
            ? $value->format($format)
            : Carbon::parse($value)->format($format);
    }

    private function publicUrlFor(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return str_starts_with($path, 'http') ? $path : Storage::disk('public')->url($path);
    }
}