<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ApplicationAcknowledgmentMail;
use App\Mail\ApplicationApprovedMail;
use App\Mail\ApplicationReapplicationMail;
use App\Mail\ApplicationRejectedMail;
use App\Models\Application;
use App\Models\Bed;
use App\Models\Inquiry;
use App\Models\LeaseContract;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    private const STATUSES = ['pending', 'approved', 'rejected', 're_application_requested', 'cancelled'];

    private const TENANT_TYPES = ['student', 'working_student', 'full_time_employee', 'part_time_employee'];

    /**
     * Public "Apply for Occupancy" submission. No authentication required —
     * the applicant is not a tenant yet, so their personal info is stored on
     * the application itself. tenant_id stays null until approval.
     *
     * Use Case Report — Apply for Occupancy, step 7.3: the selected bedspace
     * is tagged Reserved the moment the application is submitted (not left
     * vacant until approval), so it stops showing as available to other
     * prospective tenants immediately.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'inquiry_id' => ['nullable', 'integer', 'exists:inquiries,id'],

            'full_name' => ['required', 'string', 'max:150'],
            'birthdate' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'max:20'],
            'nationality' => ['nullable', 'string', 'max:60'],
            'medical_condition' => ['nullable', 'string', 'max:255'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'school_company' => ['nullable', 'string', 'max:150'],
            'school_company_address' => ['nullable', 'string', 'max:255'],

            'contact_number' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'landline' => ['nullable', 'string', 'max:20'],
            'home_address' => ['nullable', 'string', 'max:255'],

            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_number' => ['nullable', 'string', 'max:20'],
            'emergency_contact_email' => ['nullable', 'email', 'max:150'],
            'emergency_contact_landline' => ['nullable', 'string', 'max:20'],
            'father_name' => ['nullable', 'string', 'max:150'],
            'mother_name' => ['nullable', 'string', 'max:150'],

            'bed_id' => ['required', 'integer', 'exists:beds,id'],
            'preferred_start_date' => ['nullable', 'date', 'after_or_equal:today'],
            'tenant_end_date' => ['nullable', 'date', 'after:preferred_start_date'],
            'type_of_tenant' => ['nullable', Rule::in(self::TENANT_TYPES)],
            'id_document' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'signed_contract' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],

            // Use Case Report — Apply for Occupancy, step 7.1: "Prevent
            // submission if [contract acceptance] is missing." Validated
            // server-side, same as dpa_consent below — a client-side-only
            // checkbox can be bypassed by calling this endpoint directly.
            'contract_acceptance' => ['required', 'accepted'],

            'dpa_consent' => ['required', 'accepted'],
        ], [
            'dpa_consent.required' => 'You must consent to the data privacy notice before submitting.',
            'dpa_consent.accepted' => 'You must consent to the data privacy notice before submitting.',
            'contract_acceptance.required' => 'You must confirm you have reviewed the dormitory contract before submitting.',
            'contract_acceptance.accepted' => 'You must confirm you have reviewed the dormitory contract before submitting.',
            'preferred_start_date.after_or_equal' => 'Preferred start date cannot be in the past.',
            'tenant_end_date.after' => 'Tenant end date must be after the preferred start date.',
        ]);

        if (empty($data['contact_number']) && empty($data['email'])) {
            return response()->json([
                'message' => 'Please provide either a contact number or an email address.',
                'errors' => [
                    'contact_number' => ['Provide a contact number or an email address.'],
                ],
            ], 422);
        }

        $bed = Bed::with('room')->findOrFail($data['bed_id']);

        if ($bed->status !== 'vacant') {
            return response()->json([
                'message' => 'That bedspace is no longer available. Please choose another.',
            ], 409);
        }

        $alreadyPending = Application::where('bed_id', $bed->id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyPending) {
            return response()->json([
                'message' => 'There is already a pending application for that bedspace.',
            ], 409);
        }

        $idDocumentPath = $request->hasFile('id_document')
            ? $request->file('id_document')->store('application-documents', 'public')
            : null;

        $signedContractPath = $request->hasFile('signed_contract')
            ? $request->file('signed_contract')->store('application-documents', 'public')
            : null;

        $application = DB::transaction(function () use ($data, $bed, $idDocumentPath, $signedContractPath) {
            $application = Application::create([
                'inquiry_id' => $data['inquiry_id'] ?? null,
                'tenant_id' => null,

                'full_name' => $data['full_name'],
                'birthdate' => $data['birthdate'] ?? null,
                'gender' => $data['gender'] ?? null,
                'nationality' => $data['nationality'] ?? null,
                'medical_condition' => $data['medical_condition'] ?? null,
                'occupation' => $data['occupation'] ?? null,
                'school_company' => $data['school_company'] ?? null,
                'school_company_address' => $data['school_company_address'] ?? null,

                'contact_number' => $data['contact_number'] ?? null,
                'email' => $data['email'] ?? null,
                'landline' => $data['landline'] ?? null,
                'home_address' => $data['home_address'] ?? null,

                'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                'emergency_contact_number' => $data['emergency_contact_number'] ?? null,
                'emergency_contact_email' => $data['emergency_contact_email'] ?? null,
                'emergency_contact_landline' => $data['emergency_contact_landline'] ?? null,
                'father_name' => $data['father_name'] ?? null,
                'mother_name' => $data['mother_name'] ?? null,

                'bed_id' => $bed->id,
                'preferred_start_date' => $data['preferred_start_date'] ?? null,
                'tenant_end_date' => $data['tenant_end_date'] ?? null,
                'type_of_tenant' => $data['type_of_tenant'] ?? null,
                'id_document_path' => $idDocumentPath,
                'signed_contract_path' => $signedContractPath,

                'dpa_consent' => true,
                'status' => 'pending',
            ]);

            // Step 7.3: tag the bedspace Reserved so it stops appearing
            // available to other prospective tenants immediately.
            $bed->update(['status' => 'reserved']);
            $bed->room?->syncStatusFromBeds();

            return $application;
        });

        if ($application->inquiry_id) {
            Inquiry::where('id', $application->inquiry_id)
                ->where('status', '!=', 'converted')
                ->update(['status' => 'converted']);
        }

        // Step 7.4: notify the administrator. Real delivery (email/SMS to a
        // configured admin address) isn't wired up yet — same stub pattern
        // used for inquiries — so this keeps a durable record rather than a
        // promise the code doesn't keep.
        $this->notify('application.submitted', [
            'application_id' => $application->id,
            'applicant' => $application->full_name,
            'bed_id' => $bed->id,
        ]);

        // Step 7.5: acknowledgment email to the applicant. A failed send
        // must not block the submission itself — caught and logged instead
        // of surfacing as an error to the applicant, same as the other
        // outcome emails.
        if ($application->email) {
            try {
                Mail::to($application->email)->send(new ApplicationAcknowledgmentMail($application));
            } catch (\Throwable $e) {
                Log::warning('Application acknowledgment email failed to send.', [
                    'application_id' => $application->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'message' => 'Application submitted successfully. It is now pending review.',
            'application' => $application->load('bed.room:id,room_no,room_type,monthly_rate'),
        ], 201);
    }

    /**
     * Admin: list applications, newest first. Optional ?status= filter.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'status' => ['nullable', Rule::in(self::STATUSES)],
        ]);

        $query = Application::with([
            'bed:id,room_id,bed_label,status',
            'bed.room:id,room_no,room_type,monthly_rate',
            'inquiry:id,full_name,status',
            'tenant:id,full_name',
        ])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $applications = $query->get()->map(function ($application) {
            $application->returning_tenant_match = $this->findReturningTenant($application)?->only(['id', 'full_name', 'email', 'contact_number']);

            return $application;
        });

        return response()->json($applications);
    }

    /**
     * Admin: view a single application in full.
     */
    public function show(Application $application): JsonResponse
    {
        $application->load([
            'bed:id,room_id,bed_label,status',
            'bed.room:id,room_no,room_type,monthly_rate',
            'inquiry',
            'tenant',
            'createdBy:id,name',
            'approvedBy:id,name',
            'leaseContract',
        ]);

        $returning = $this->findReturningTenant($application);

        return response()->json([
            'application' => $application,
            'returning_tenant_match' => $returning?->only(['id', 'full_name', 'email', 'contact_number']),
            'discount_eligible' => (bool) $returning,
        ]);
    }

    /**
     * Applicant-facing status check. Looks up by application id + the contact
     * detail used on the application, so someone without an account can still
     * check their own status without exposing anyone else's.
     */
    public function checkStatus(Request $request): JsonResponse
    {
        $data = $request->validate([
            'application_id' => ['required', 'integer'],
            'contact' => ['required', 'string', 'max:150'],
        ]);

        $application = Application::where('id', $data['application_id'])
            ->where(function ($query) use ($data) {
                $query->where('email', $data['contact'])
                    ->orWhere('contact_number', $data['contact']);
            })
            ->with('bed.room:id,room_no,room_type,monthly_rate', 'leaseContract:id,application_id,esign_status,status')
            ->first();

        if (! $application) {
            return response()->json([
                'message' => 'No application found matching those details.',
            ], 404);
        }

        return response()->json([
            'id' => $application->id,
            'full_name' => $application->full_name,
            'status' => $application->status,
            'rejection_reason' => $application->rejection_reason,
            're_application_note' => $application->re_application_note,
            'preferred_start_date' => $application->preferred_start_date,
            'bed' => $application->bed,
            'contract' => $application->leaseContract,
            'submitted_at' => $application->created_at,
        ]);
    }

    /**
     * Admin: approve an application. This is the pivot point of onboarding —
     * it creates (or re-links) the tenant record and their login account,
     * creates the lease contract, and assigns the bed, all in one transaction
     * so a partial failure can't leave the system half-onboarded.
     *
     * Accepts an optional discount_amount — Week 4 timeline: "Apply Discount
     * button for returning tenants." Only meaningful when the applicant
     * matches a past tenant record; there's no automatic discount rule
     * defined anywhere in the spec, so this is a manual admin judgment call.
     */
    public function approve(Request $request, Application $application): JsonResponse
    {
        $data = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'monthly_rate' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($application->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending applications can be approved.',
            ], 409);
        }

        $bed = Bed::with('room')->find($application->bed_id);

        if (! $bed) {
            return response()->json([
                'message' => 'The bedspace on this application no longer exists.',
            ], 409);
        }

        // The bed should be sitting in the Reserved state this application put
        // it in at submission time. If it isn't, something else has already
        // claimed or changed it — approving now would silently double-assign
        // a bed.
        if ($bed->status !== 'reserved') {
            return response()->json([
                'message' => 'That bedspace is no longer reserved for this application and cannot be assigned.',
            ], 409);
        }

        $returningTenant = $this->findReturningTenant($application);

        $result = DB::transaction(function () use ($application, $bed, $data, $returningTenant, $request) {
            $tenant = $returningTenant;
            $temporaryPassword = null;

            if (! $tenant) {
                [$tenant, $temporaryPassword] = $this->createTenantWithLogin($application);
            }

            // The discount is applied directly to the stored rate rather than
            // kept as a separate adjustment applied ad-hoc wherever a bill is
            // calculated. That means any future consumer of monthly_rate —
            // a recurring monthly billing generator, for instance, which
            // doesn't exist yet — automatically inherits the discount with
            // nothing to remember. discount_amount itself is kept purely as
            // an audit trail of how much was taken off, not as a value
            // anything needs to re-subtract later.
            $baseRate = $data['monthly_rate'] ?? $bed->room->perBedRate();
            $discountAmount = $data['discount_amount'] ?? 0;
            $monthlyRate = max(0, $baseRate - $discountAmount);

            $contract = LeaseContract::create([
                'application_id' => $application->id,
                'tenant_id' => $tenant->id,
                'bed_id' => $bed->id,
                'inquiry_id' => $application->inquiry_id,
                'start_date' => $data['start_date']
                    ?? $application->preferred_start_date
                    ?? now()->toDateString(),
                'end_date' => $data['end_date'] ?? $application->tenant_end_date ?? null,
                'monthly_rate' => $monthlyRate,
                'discount_amount' => $data['discount_amount'] ?? null,
                'esign_status' => 'pending',
                'status' => 'pending', // becomes 'active' once the contract is signed
                'created_by' => $request->user()?->id,
                'approved_by' => $request->user()?->id,
            ]);

            // NOTE: the bed is deliberately left as 'reserved' here — it was
            // already reserved at application submission time (store()), and
            // approving the application doesn't mean the tenant has actually
            // paid anything yet. It only becomes 'occupied' once the move-in
            // fee payment is verified — see
            // PaymentController::activateTenantIfMoveInSettled(). Setting it
            // to 'occupied' here was a real bug: it let a bed look permanently
            // assigned even if the applicant never paid.

            $application->update([
                'status' => 'approved',
                'tenant_id' => $tenant->id,
                'approved_by' => $request->user()?->id,
            ]);

            // Step 11.2: "create billing record" — the move-in fee breakdown
            // (security deposit + 1 month advance rent) that Table 17's Pay
            // Move-In Fees flow requires to exist before the tenant can even
            // see a total due. A brand-new tenant is only ever created with
            // 'pending_move_in_payment' status by createTenantWithLogin();
            // a returning tenant's existing status is left untouched, since
            // they may already be Active from a prior stay.
            $moveInFeeAmount = $monthlyRate * 2; // 1 month deposit + 1 month advance

            $billingStatement = \App\Models\BillingStatement::create([
                'contract_id' => $contract->id,
                'tenant_id' => $tenant->id,
                'type' => 'move_in',
                'billing_period_start' => $contract->start_date,
                'billing_period_end' => $contract->start_date,
                'due_date' => $contract->start_date,
                'base_rent' => $moveInFeeAmount,
                'utilities_amount' => 0,
                'wifi_amount' => 0,
                'penalty_amount' => 0,
                'total_amount' => $moveInFeeAmount,
                'status' => 'unpaid',
            ]);

            return [
                'tenant' => $tenant,
                'contract' => $contract,
                'temporary_password' => $temporaryPassword,
                'move_in_billing' => $billingStatement,
            ];
        });

        // Step 11.3: email the applicant their login credentials. Sent outside
        // the transaction so a slow mail server never holds the DB lock, and
        // a failed send doesn't roll back an otherwise-successful approval.
        if ($application->email) {
            try {
                Mail::to($application->email)->send(new ApplicationApprovedMail(
                    $application,
                    $application->email,
                    $result['temporary_password']
                ));
            } catch (\Throwable $e) {
                Log::warning('Application approval email failed to send.', [
                    'application_id' => $application->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->notify('application.approved', [
            'application_id' => $application->id,
            'tenant_id' => $result['tenant']->id,
            'contract_id' => $result['contract']->id,
            'returning_tenant' => (bool) $returningTenant,
            'discount_amount' => $data['discount_amount'] ?? null,
            'approved_by' => $request->user()?->id,
        ]);

        return response()->json([
            'message' => 'Application approved. Tenant account created and credentials sent.',
            'discount_eligible' => (bool) $returningTenant,
            'application' => $application->fresh()->load('bed.room:id,room_no,room_type,monthly_rate'),
            'tenant' => $result['tenant'],
            'contract' => $result['contract'],
        ]);
    }

    /**
     * Admin: reject a pending application.
     *
     * Use Case Report steps 12–13: requires a reason, releases the Reserved
     * bedspace back to Vacant, and emails the applicant with that reason.
     */
    public function reject(Request $request, Application $application): JsonResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'reason.required' => 'A rejection reason is required.',
        ]);

        if ($application->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending applications can be rejected.',
            ], 409);
        }

        DB::transaction(function () use ($application, $data, $request) {
            $application->update([
                'status' => 'rejected',
                'rejection_reason' => $data['reason'],
                'approved_by' => $request->user()?->id,
            ]);

            $this->releaseBed($application);
        });

        if ($application->email) {
            try {
                Mail::to($application->email)->send(new ApplicationRejectedMail($application->fresh()));
            } catch (\Throwable $e) {
                Log::warning('Application rejection email failed to send.', [
                    'application_id' => $application->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->notify('application.rejected', [
            'application_id' => $application->id,
            'applicant' => $application->full_name,
            'reason' => $data['reason'],
            'rejected_by' => $request->user()?->id,
        ]);

        return response()->json([
            'message' => 'Application rejected. Applicant notified.',
            'application' => $application->fresh(),
        ]);
    }

    /**
     * Admin: request re-application. Use Case Report steps 14–15 — a third
     * outcome distinct from rejection: the bedspace is released the same way,
     * but the applicant is invited to submit a fresh application rather than
     * being turned away outright.
     */
    public function requestReapplication(Request $request, Application $application): JsonResponse
    {
        $data = $request->validate([
            'note' => ['required', 'string', 'max:500'],
        ], [
            'note.required' => 'Instructions for the applicant are required.',
        ]);

        if ($application->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending applications can have re-application requested.',
            ], 409);
        }

        DB::transaction(function () use ($application, $data, $request) {
            $application->update([
                'status' => 're_application_requested',
                're_application_note' => $data['note'],
                'approved_by' => $request->user()?->id,
            ]);

            $this->releaseBed($application);
        });

        if ($application->email) {
            try {
                Mail::to($application->email)->send(new ApplicationReapplicationMail($application->fresh()));
            } catch (\Throwable $e) {
                Log::warning('Re-application request email failed to send.', [
                    'application_id' => $application->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->notify('application.reapplication_requested', [
            'application_id' => $application->id,
            'applicant' => $application->full_name,
            'note' => $data['note'],
            'requested_by' => $request->user()?->id,
        ]);

        return response()->json([
            'message' => 'Re-application request sent to applicant.',
            'application' => $application->fresh(),
        ]);
    }

    /**
     * Admin: cancel a pending application (soft workflow action, not a delete).
     */
    public function cancel(Application $application): JsonResponse
    {
        if ($application->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending applications can be cancelled.',
            ], 409);
        }

        DB::transaction(function () use ($application) {
            $application->update(['status' => 'cancelled']);
            $this->releaseBed($application);
        });

        return response()->json([
            'message' => 'Application cancelled.',
            'application' => $application->fresh(),
        ]);
    }

    /**
     * Renders the admin Review Applications page.
     */
    public function page()
    {
        $applications = Application::with([
            'bed:id,room_id,bed_label,status',
            'bed.room:id,room_no,room_type,monthly_rate',
        ])->latest()->get()->map(function ($application) {
            $returning = $this->findReturningTenant($application);

            return [
                'id' => $application->id,
                'status' => $application->status,
                'full_name' => $application->full_name,
                'birthdate' => $application->birthdate?->format('M j, Y'),
                'gender' => $application->gender,
                'nationality' => $application->nationality,
                'medical_condition' => $application->medical_condition,
                'occupation' => $application->occupation,
                'school_company' => $application->school_company,
                'school_company_address' => $application->school_company_address,
                'contact_number' => $application->contact_number,
                'email' => $application->email,
                'landline' => $application->landline,
                'home_address' => $application->home_address,
                'emergency_contact_name' => $application->emergency_contact_name,
                'emergency_contact_number' => $application->emergency_contact_number,
                'emergency_contact_email' => $application->emergency_contact_email,
                'father_name' => $application->father_name,
                'mother_name' => $application->mother_name,
                'room_no' => $application->bed?->room?->room_no,
                'bed_label' => $application->bed?->bed_label,
                'monthly_rate' => $application->bed?->room?->perBedRate(),
                'preferred_start_date' => $application->preferred_start_date?->format('M j, Y'),
                'tenant_end_date' => $application->tenant_end_date?->format('M j, Y'),
                'type_of_tenant' => $application->type_of_tenant,
                'id_document_url' => $this->publicUrlFor($application->id_document_path),
                'signed_contract_url' => $this->publicUrlFor($application->signed_contract_path),
                'rejection_reason' => $application->rejection_reason,
                're_application_note' => $application->re_application_note,
                'created_at' => $application->created_at?->format('M j, Y g:ia'),
                'returning_tenant' => $returning ? [
                    'id' => $returning->id,
                    'full_name' => $returning->full_name,
                ] : null,
            ];
        })->values();

        return view('adminapplications', ['applications' => $applications]);
    }

    /**
     * Releases the bedspace a rejected/re-application-requested/cancelled
     * application was holding, back to Vacant — steps 13.2 / 15.2.
     */
    private function releaseBed(Application $application): void
    {
        $bed = Bed::with('room')->find($application->bed_id);

        if ($bed && $bed->status === 'reserved') {
            $bed->update(['status' => 'vacant']);
            $bed->room?->syncStatusFromBeds();
        }
    }

    private function publicUrlFor(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return str_starts_with($path, 'http')
            ? $path
            : Storage::disk('public')->url($path);
    }

    /**
     * Creates the tenant record together with a login account, so an approved
     * tenant can actually sign in to the portal. Without this link, the
     * tenant-scoped routes have no tenant to resolve from the session.
     *
     * If the applicant gave no email there's no way to create an account, so
     * the tenant record is created unlinked — an admin can attach a login
     * later once contact details are on file.
     *
     * Returns [Tenant, temporaryPassword|null].
     */
    private function createTenantWithLogin(Application $application): array
    {
        $tenantUser = null;
        $temporaryPassword = null;

        if (! empty($application->email)) {
            $tenantRole = Role::firstOrCreate(['role_name' => 'tenant']);

            $tenantUser = User::where('email', $application->email)->first();

            if (! $tenantUser) {
                $temporaryPassword = Str::random(12);

                $tenantUser = User::create([
                    'name' => $application->full_name,
                    'email' => $application->email,
                    'password' => Hash::make($temporaryPassword),
                    'role_id' => $tenantRole->id,
                    'is_active' => true,
                ]);
            }
        }

        $tenant = Tenant::create([
            'user_id' => $tenantUser?->id,
            'full_name' => $application->full_name,
            'contact_number' => $application->contact_number,
            'email' => $application->email,
            'emergency_contact_name' => $application->emergency_contact_name,
            'emergency_contact_number' => $application->emergency_contact_number,
            'status' => 'pending_move_in_payment',
        ]);

        return [$tenant, $temporaryPassword];
    }

    /**
     * Look for an existing tenant record matching this applicant, so returning
     * tenants can be flagged for a discount and keep their history. Matches on
     * email or contact number (exact), never on name alone — names are far too
     * easy to collide on and a false match would merge two different people.
     */
    private function findReturningTenant(Application $application): ?Tenant
    {
        if (empty($application->email) && empty($application->contact_number)) {
            return null;
        }

        return Tenant::where(function ($query) use ($application) {
            if (! empty($application->email)) {
                $query->orWhere('email', $application->email);
            }
            if (! empty($application->contact_number)) {
                $query->orWhere('contact_number', $application->contact_number);
            }
        })->first();
    }

    /**
     * Notification stub. Week 4 scope is the hook itself, not real delivery —
     * swap the log call for a Mail/SMS notification when that's built.
     */
    private function notify(string $event, array $payload): void
    {
        Log::info("[notification stub] {$event}", $payload);
    }
}