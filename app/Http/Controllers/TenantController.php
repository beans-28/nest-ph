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

class TenantController extends Controller
{
    private const TENANT_TYPES = ['student', 'employee', 'transient_worker'];

    /**
     * Tenant Manager admin page — Table 14 (Manage Tenant Records).
     * Whole tenant list rendered once, then searched/filtered/paginated
     * client-side in JS, same pattern as Lease Management and Delinquency.
     */
    public function page(Request $request)
    {
        BillingStatement::syncOverdueStatuses();

        $overdueTenantIds = BillingStatement::where('status', 'overdue')->pluck('tenant_id');

        $tenants = Tenant::with(['activeContract.bed.room'])
            ->orderBy('full_name')
            ->get();

        $rows = $tenants->map(fn (Tenant $tenant) => $this->transformRow($tenant, $overdueTenantIds))->values();

        return view('tenantmanager', [
            'tenants' => $rows,
            'totalCount' => $tenants->count(),
        ]);
    }

    /**
     * Table 14, step 3 — full profile for the View drawer. Also used to
     * pre-fill the Edit modal, so there's one source of truth for what a
     * tenant's full record looks like.
     */
    public function show(Tenant $tenant): JsonResponse
    {
        $tenant->load(['activeContract.bed.room']);

        $outstanding = $tenant->billingStatements()
            ->whereIn('status', ['unpaid', 'partial', 'overdue'])
            ->sum('total_amount');

        $contract = $tenant->activeContract;

        return response()->json([
            'id' => $tenant->id,
            'full_name' => $tenant->full_name,
            'email' => $tenant->email,
            'contact_number' => $tenant->contact_number,
            'date_of_birth' => $this->formatDate($tenant->date_of_birth),
            'home_address' => $tenant->home_address,
            'tenant_type' => $tenant->tenant_type,
            'emergency_contact_name' => $tenant->emergency_contact_name,
            'emergency_contact_number' => $tenant->emergency_contact_number,
            'status' => $tenant->status,
            'is_blacklisted' => (bool) $tenant->is_blacklisted,
            'id_document_url' => $this->publicUrlFor($tenant->id_document_path),
            'signed_contract_url' => $this->publicUrlFor($tenant->signed_contract_path),
            'contract' => $contract ? [
                'room_no' => $contract->bed?->room?->room_no,
                'bed_label' => $contract->bed?->bed_label,
                'start_date' => $this->formatDate($contract->start_date, 'M j, Y'),
                'end_date' => $this->formatDate($contract->end_date, 'M j, Y'),
                'monthly_rate' => $contract->monthly_rate,
            ] : null,
            'outstanding_balance' => (float) $outstanding,
            'payments_count' => $tenant->payments()->count(),
        ]);
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
            'full_name' => ['required', 'string', 'max:150'],
            'date_of_birth' => ['nullable', 'date'],
            'home_address' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:150', 'unique:tenants,email'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_number' => ['nullable', 'string', 'max:20'],
            'tenant_type' => ['nullable', Rule::in(self::TENANT_TYPES)],
            'id_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
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
        $signedContractPath = $request->hasFile('signed_contract')
            ? $request->file('signed_contract')->store('tenant-documents', 'public')
            : null;

        [$tenant, $temporaryPassword] = DB::transaction(function () use ($data, $bed, $idDocPath, $signedContractPath, $request) {
            $tenantRole = Role::firstOrCreate(['role_name' => 'tenant']);
            $temporaryPassword = Str::random(12);

            $user = User::create([
                'name' => $data['full_name'],
                'email' => $data['email'],
                'password' => Hash::make($temporaryPassword),
                'role_id' => $tenantRole->id,
                'is_active' => true,
            ]);

            $tenant = Tenant::create([
                'user_id' => $user->id,
                'full_name' => $data['full_name'],
                'contact_number' => $data['contact_number'] ?? null,
                'email' => $data['email'],
                'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                'emergency_contact_number' => $data['emergency_contact_number'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'home_address' => $data['home_address'] ?? null,
                'tenant_type' => $data['tenant_type'] ?? null,
                'id_document_path' => $idDocPath,
                'signed_contract_path' => $signedContractPath,
                // A walk-in registration is completed entirely by the
                // admin, bypassing the online Pay Move-In Fees flow that
                // pending_move_in_payment tenants go through -- so the
                // tenant is active immediately. This is the same open
                // question already flagged for Table 16 (move-in
                // confirmation) -- worth a deliberate call from BAGUI if a
                // walk-in flow ever needs its own payment step too.
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
            'full_name' => ['required', 'string', 'max:150'],
            'date_of_birth' => ['nullable', 'date'],
            'home_address' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:150', Rule::unique('tenants', 'email')->ignore($tenant->id)],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_number' => ['nullable', 'string', 'max:20'],
            'tenant_type' => ['nullable', Rule::in(self::TENANT_TYPES)],
            'id_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'signed_contract' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ], [
            'email.unique' => 'A tenant record with this email address already exists.',
        ]);

        if ($request->hasFile('id_document')) {
            $data['id_document_path'] = $request->file('id_document')->store('tenant-documents', 'public');
        }
        if ($request->hasFile('signed_contract')) {
            $data['signed_contract_path'] = $request->file('signed_contract')->store('tenant-documents', 'public');
        }
        unset($data['id_document'], $data['signed_contract']);

        $tenant->update($data);

        // Keeps the login account's name/email in sync with the tenant
        // record, so the portal login and this page never drift apart.
        if ($tenant->user) {
            $tenant->user->update(['name' => $data['full_name'], 'email' => $data['email']]);
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
     * Table 38 — Deactivate Tenant Account (and its reverse, Reactivate,
     * which the manuscript doesn't name separately but which the "Set
     * Status" action needs to be reversible).
     *
     * Note: Table 38 assumes move-out already happened (Table 16 -- Record
     * Occupancy Transaction, not yet built). Since that's not wired up yet,
     * deactivating here also releases the tenant's current bed/lease, so a
     * deactivated tenant is never left showing as still occupying a room.
     * Worth revisiting once Table 16 exists as its own flow.
     */
    public function setStatus(Request $request, Tenant $tenant): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['active', 'archived'])],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        if ($data['status'] === 'archived') {
            if ($tenant->status === 'archived') {
                return response()->json(['message' => 'This tenant is already deactivated.'], 409);
            }

            if (empty($data['reason'])) {
                return response()->json(['message' => 'A reason is required to deactivate this account.'], 422);
            }

            $outstanding = $tenant->billingStatements()
                ->whereIn('status', ['unpaid', 'partial', 'overdue'])
                ->sum('total_amount');

            if ($outstanding > 0) {
                return response()->json([
                    'message' => 'This tenant has an unpaid balance of ₱' . number_format($outstanding, 2) . '. Please settle before deactivating.',
                ], 409);
            }

            DB::transaction(function () use ($tenant, $data) {
                $tenant->update([
                    'status' => 'archived',
                    'deactivation_reason' => $data['reason'],
                    'deactivated_at' => now(),
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

            $message = 'Tenant account deactivated successfully.';
        } else {
            $tenant->update([
                'status' => 'active',
                'deactivation_reason' => null,
                'deactivated_at' => null,
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
            'monthly_rate' => $contract?->monthly_rate,
            'status' => $this->deriveStatus($tenant, $overdueTenantIds),
            'tenant_type' => $tenant->tenant_type,
        ];
    }

    private function deriveStatus(Tenant $tenant, $overdueTenantIds): string
    {
        if ($tenant->status === 'archived') {
            return 'archived';
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