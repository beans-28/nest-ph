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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Services\TenancyDocuments;
use App\Services\TextbeeService;

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

            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            // Tenant Agreement 14.1: the tenant declares they are at least 18.
            'birthdate' => ['required', 'date', 'before_or_equal:' . now()->subYears(18)->toDateString()],
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
            'emergency_contact_relation' => ['nullable', 'string', 'max:50'],

            'bed_id' => ['required', 'integer', 'exists:beds,id'],
            'preferred_start_date' => ['nullable', 'date', 'after_or_equal:today'],
            'tenant_end_date' => ['nullable', 'date', 'after:preferred_start_date'],
            'type_of_tenant' => ['nullable', Rule::in(self::TENANT_TYPES)],
            'id_document' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'emergency_contact_id' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'signed_contract' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],

            // Use Case Report — Apply for Occupancy, step 7.1: "Prevent
            // submission if [contract acceptance] is missing." Validated
            // server-side, same as dpa_consent below — a client-side-only
            // checkbox can be bypassed by calling this endpoint directly.
            'signed_contract_path' => ['nullable', 'string', 'max:255'],
            'contract_acceptance' => ['required', 'accepted'],

            'dpa_consent' => ['required', 'accepted'],

            // Token handed back by verifyOtp() once the applicant typed the
            // code we texted to their cellphone number.
            'phone_verification_token' => ['required', 'string', 'max:100'],
        ], [
            'phone_verification_token.required' => 'Please verify your cellphone number with the code we text you.',
            'dpa_consent.required' => 'You must consent to the data privacy notice before submitting.',
            'dpa_consent.accepted' => 'You must consent to the data privacy notice before submitting.',
            'contract_acceptance.required' => 'You must confirm you have reviewed the dormitory contract before submitting.',
            'contract_acceptance.accepted' => 'You must confirm you have reviewed the dormitory contract before submitting.',
            'preferred_start_date.after_or_equal' => 'Preferred start date cannot be in the past.',
            'tenant_end_date.after' => 'Tenant end date must be after the preferred start date.',
            'emergency_contact_id.required' => 'Please upload a valid ID of your emergency contact.',
            'emergency_contact_id.mimes' => "Your emergency contact's ID must be a JPG, PNG, or PDF file.",
            'emergency_contact_id.max' => "Your emergency contact's ID file is larger than 5 MB. Please upload a smaller photo or scan.",
            'id_document.mimes' => 'Your ID must be a JPG, PNG, or PDF file.',
            'id_document.max' => 'Your ID file is larger than 5 MB. Please upload a smaller photo or scan.',
            'birthdate.required' => 'Please enter your birthdate.',
            'birthdate.before_or_equal' => 'Tenants must be at least 18 years old.',
        ]);

        // Payments and Fees Schedule 4.1: the minimum stay is 3 months (the
        // dorm's setting). An End Date is optional; without one the stay
        // ends on the last day of the third month.
        $profile = \App\Models\DormitoryProfile::current();
        if (! empty($data['preferred_start_date']) && ! empty($data['tenant_end_date'])) {
            $minimumEnd = $profile->minimumEndDate(\Carbon\Carbon::parse($data['preferred_start_date']));
            if (\Carbon\Carbon::parse($data['tenant_end_date'])->lt($minimumEnd)) {
                return response()->json([
                    'message' => "The minimum stay is {$profile->minimum_stay_months} months, so your end date can't be earlier than {$minimumEnd->format('F j, Y')}.",
                    'errors' => ['tenant_end_date' => ["The earliest end date is {$minimumEnd->format('F j, Y')}."]],
                ], 422);
            }
        }

        if ($error = $this->sameNumberError($data)) {
            return response()->json([
                'message' => $error,
                'errors' => ['emergency_contact_number' => [$error]],
            ], 422);
        }

        // The cellphone number must be the one the OTP was sent to and
        // confirmed, otherwise anyone could apply using someone else's number.
        $textbee = app(TextbeeService::class);
        $verifiedNumber = Cache::get('apply-otp-verified:' . $data['phone_verification_token']);
        if (! $verifiedNumber || $verifiedNumber !== $textbee->normalizePhilippineNumber((string) ($data['contact_number'] ?? ''))) {
            return response()->json([
                'message' => 'Please verify your cellphone number with the code we text you.',
                'errors' => ['contact_number' => ['Your cellphone number has not been verified.']],
            ], 422);
        }

        if (empty($data['contact_number']) && empty($data['email'])) {
            return response()->json([
                'message' => 'Please provide either a contact number or an email address.',
                'errors' => [
                    'contact_number' => ['Provide a contact number or an email address.'],
                ],
            ], 422);
        }

        // One email = one person. If a current tenant already uses this
        // email, accepting the application would merge a second person (or a
        // second stay) into that tenant's record and account.
        if (! empty($data['email']) && $this->findCurrentTenantByEmail($data['email'])) {
            return response()->json([
                'message' => 'This email address is already used by a current tenant. Please use a different email, or contact the dormitory if you are already staying here.',
                'errors' => ['email' => ['This email is already used by a current tenant.']],
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

        // E-sign flow: the applicant (and their emergency contact) already
        // signed the documents via signContract(). Only a path that
        // signContract() itself wrote is accepted, and only if the details
        // on the form still match what was signed. A raw file upload is
        // kept as a fallback for anyone whose browser can't run the
        // signature pad (no emergency-contact consent is recorded then).
        $emergencyContactIdPath = $request->file('emergency_contact_id')->store('application-documents', 'public');

        $signingRecord = $this->readSigningRecord($request->input('signed_contract_path'));

        if ($signingRecord) {
            $bedForCheck = Bed::find($data['bed_id']);
            if (($signingRecord['fingerprint'] ?? null) !== $this->signingFingerprint($data, $bedForCheck)) {
                return response()->json([
                    'message' => 'You changed some details after signing. Please review and sign the documents again so the signed copy matches your application.',
                    'errors' => ['signed_contract_path' => ['Please sign the documents again.']],
                ], 422);
            }
        }

        $signedContractPath = $signingRecord
            ? $request->input('signed_contract_path')
            : ($request->hasFile('signed_contract')
                ? $request->file('signed_contract')->store('application-documents', 'public')
                : null);

        if (! $signedContractPath) {
            return response()->json([
                'message' => 'Please review and sign the contract before submitting your application.',
                'errors' => ['signed_contract_path' => ['The contract must be signed first.']],
            ], 422);
        }

        $application = DB::transaction(function () use ($data, $bed, $idDocumentPath, $emergencyContactIdPath, $signedContractPath, $signingRecord) {
            $application = Application::create([
                'inquiry_id' => $data['inquiry_id'] ?? null,
                'tenant_id' => null,

                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
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
                'emergency_contact_relation' => $data['emergency_contact_relation'] ?? null,
                // Taken from the signed copy, not from the form, so they
                // always match what the emergency contact actually signed.
                'emergency_contact_signed' => (bool) ($signingRecord['emergency_contact_signed'] ?? false),
                'emergency_billing_consent' => (bool) ($signingRecord['emergency_billing_consent'] ?? false),

                'bed_id' => $bed->id,
                'preferred_start_date' => $data['preferred_start_date'] ?? null,
                'tenant_end_date' => $data['tenant_end_date'] ?? null,
                'type_of_tenant' => $data['type_of_tenant'] ?? null,
                'id_document_path' => $idDocumentPath,
                'emergency_contact_id_path' => $emergencyContactIdPath,
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
            'tenant:id,first_name,last_name',
        ])->latest();

        // Once an applicant resubmits with the same email, their old
        // re-application request is resolved and drops off the list.
        $query->where(function ($q) {
            $q->where('status', '!=', 're_application_requested')
                ->orWhereNotExists(function ($sub) {
                    $sub->selectRaw('1')
                        ->from('applications as newer')
                        ->whereRaw('LOWER(newer.email) = LOWER(applications.email)')
                        ->whereColumn('newer.created_at', '>', 'applications.created_at');
                });
        });

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

        // Safety net for applications submitted before the check in store()
        // existed, or whose email became a current tenant's in the meantime.
        if (! empty($application->email) && $this->findCurrentTenantByEmail($application->email)) {
            return response()->json([
                'message' => 'This email belongs to a current tenant. Reject this application or ask the applicant to re-apply with their own email.',
            ], 409);
        }

        $returningTenant = $this->findReturningTenant($application);

        $result = DB::transaction(function () use ($application, $bed, $data, $returningTenant, $request) {
            $tenant = $returningTenant;
            $temporaryPassword = null;

            if (! $tenant) {
                [$tenant, $temporaryPassword] = $this->createTenantWithLogin($application);
            } else {
                // A returning tenant signed a new Agreement with (possibly) a
                // new emergency contact, whose billing-reminder consent
                // (Agreement 9.3) replaces the old one.
                $tenant->update([
                    'emergency_contact_name' => $application->emergency_contact_name ?? $tenant->emergency_contact_name,
                    'emergency_contact_number' => $application->emergency_contact_number ?? $tenant->emergency_contact_number,
                    'emergency_billing_reminders' => (bool) $application->emergency_billing_consent,
                ]);
            }

            $startDate = \Carbon\Carbon::parse($data['start_date'] ?? $application->preferred_start_date ?? now()->toDateString());

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

            // Bug fix: the applicant already signed and uploaded their
            // contract during Apply for Occupancy (Table 13, step 6) —
            // this was previously ignored, forcing the admin to re-upload
            // the exact same document again on Lease Management before the
            // contract could go Active. Now it's carried straight over.
            $contract = LeaseContract::create([
                'application_id' => $application->id,
                'tenant_id' => $tenant->id,
                'bed_id' => $bed->id,
                'inquiry_id' => $application->inquiry_id,
                'start_date' => $startDate->toDateString(),
                // Agreement 2.2: no End Date means the stay ends on the last
                // day of the third month (the minimum stay).
                'end_date' => $data['end_date']
                    ?? $application->tenant_end_date
                    ?? \App\Models\DormitoryProfile::current()->minimumEndDate($startDate)->toDateString(),
                'monthly_rate' => $monthlyRate,
                'discount_amount' => $data['discount_amount'] ?? null,
                'esign_status' => $application->signed_contract_path ? 'signed' : 'pending',
                'signed_document_url' => $application->signed_contract_path,
                'signed_at' => $application->signed_contract_path ? now() : null,
                'status' => $application->signed_contract_path ? 'active' : 'pending',
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
            // (security deposit + 1 month advance rent) that Table 16's Pay
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
                // Kept apart so the deposit is never counted as rent. The
                // advance covers the tenant's first rental period.
                'advance_amount' => $monthlyRate,
                'deposit_amount' => $monthlyRate,
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
            $current = $application->email ? $this->findCurrentTenantByEmail($application->email) : null;

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
                'emergency_contact_relation' => $application->emergency_contact_relation,
                'emergency_contact_signed' => (bool) $application->emergency_contact_signed,
                'emergency_billing_consent' => (bool) $application->emergency_billing_consent,
                'room_no' => $application->bed?->room?->room_no,
                'bed_label' => $application->bed?->bed_label,
                'monthly_rate' => $application->bed?->room?->perBedRate(),
                'preferred_start_date' => $application->preferred_start_date?->format('M j, Y'),
                'tenant_end_date' => $application->tenant_end_date?->format('M j, Y'),
                'type_of_tenant' => $application->type_of_tenant,
                'id_document_url' => $this->publicUrlFor($application->id_document_path),
                'emergency_contact_id_url' => $this->publicUrlFor($application->emergency_contact_id_path),
                'signed_contract_url' => $this->publicUrlFor($application->signed_contract_path),
                'rejection_reason' => $application->rejection_reason,
                're_application_note' => $application->re_application_note,
                'created_at' => $application->created_at?->format('M j, Y g:ia'),
                'returning_tenant' => $returning ? [
                    'id' => $returning->id,
                    'full_name' => $returning->full_name,
                ] : null,
                'current_tenant' => $current ? [
                    'id' => $current->id,
                    'full_name' => $current->full_name,
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
                $temporaryPassword = \App\Support\TemporaryPassword::generate();

                $tenantUser = User::forceCreate([
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
            'first_name' => $application->first_name,
            'last_name' => $application->last_name,
            'contact_number' => $application->contact_number,
            'email' => $application->email,
            'emergency_contact_name' => $application->emergency_contact_name,
            'emergency_contact_number' => $application->emergency_contact_number,
            'emergency_billing_reminders' => (bool) $application->emergency_billing_consent,
            // Bug fix: these five were being silently dropped on approval --
            // they exist on the application (from the online form) but were
            // never carried over onto the tenant record itself.
            'date_of_birth' => $application->birthdate,
            'home_address' => $application->home_address,
            'tenant_type' => $application->type_of_tenant,
            'id_document_path' => $application->id_document_path,
            'emergency_contact_id_path' => $application->emergency_contact_id_path,
            'signed_contract_path' => $application->signed_contract_path,
            'status' => 'pending_move_in_payment',
        ]);

        return [$tenant, $temporaryPassword];
    }

    /**
     * Look for an existing tenant record matching this applicant, so returning
     * tenants can be flagged for a discount and keep their history. Matches on
     * email (exact), or on contact number (exact) plus the same last name —
     * never on name alone. A phone number by itself isn't enough: siblings
     * and parents often share one, and a false match would merge two
     * different people.
     *
     * Only tenants who have moved out (inactive) count as "returning". A
     * current tenant with the same email is blocked instead — see
     * findCurrentTenantByEmail().
     */
    private function findReturningTenant(Application $application): ?Tenant
    {
        if (! empty($application->email)) {
            $byEmail = Tenant::where('status', 'inactive')
                ->whereRaw('LOWER(email) = ?', [strtolower($application->email)])
                ->first();
            if ($byEmail) {
                return $byEmail;
            }
        }

        if (empty($application->contact_number) || empty($application->last_name)) {
            return null;
        }

        return Tenant::where('status', 'inactive')
            ->where('contact_number', $application->contact_number)
            ->where('last_name', $application->last_name)
            ->first();
    }

    /**
     * A tenant who is staying here now (active) or has been approved and is
     * about to move in (pending_move_in_payment) with this email, if any.
     */
    private function findCurrentTenantByEmail(string $email): ?Tenant
    {
        return Tenant::whereIn('status', ['active', 'pending_move_in_payment'])
            ->whereRaw('LOWER(email) = ?', [strtolower($email)])
            ->first();
    }

    /**
     * Notification stub. Week 4 scope is the hook itself, not real delivery —
     * swap the log call for a Mail/SMS notification when that's built.
     */
    private function notify(string $event, array $payload): void
    {
        Log::info("[notification stub] {$event}", $payload);
    }

    /**
     * Apply page, "Review & Sign": the dorm's documents filled in with what
     * the applicant has typed so far, as a PDF (unsigned). With ?document=
     * agreement|rules|fees it returns just that one, for the page's tabs;
     * without it, all three (the "Download as PDF" button). Nothing is saved.
     */
    public function previewContract(Request $request, TenancyDocuments $documents)
    {
        [$applicant, $bed] = $this->validateContractData($request);
        $only = $request->validate([
            'document' => ['nullable', Rule::in(array_keys(TenancyDocuments::DOCUMENTS))],
        ])['document'] ?? null;

        if ($only) {
            return $documents->respond($only, $documents->data($applicant, $bed), 'Tenancy-Documents-Preview.pdf');
        }

        return $documents->pdf($documents->data($applicant, $bed))
            ->stream('Tenancy-Documents-Preview.pdf');
    }

    /**
     * Signs the documents: the applicant's signature goes on all three, the
     * emergency contact's on the Agreement (Section 9.1 says they agreed to
     * be listed), along with their separate yes/no on billing reminders
     * (Section 9.3). Saves one PDF and hands back its path, which the
     * application submission then references.
     *
     * A small record of what was signed is saved next to the PDF, so the
     * submission can check the applicant didn't change their details after
     * signing, and so the consent saved on the application is the consent
     * actually printed on the signed copy.
     */
    public function signContract(Request $request, TenancyDocuments $documents): JsonResponse
    {
        [$applicant, $bed] = $this->validateContractData($request);

        $signed = $request->validate([
            'signature_image' => ['required', 'string'],
            'emergency_signature_image' => ['required', 'string'],
            'emergency_billing_consent' => ['required', 'boolean'],
            'acknowledged' => ['required', 'array'],
            'acknowledged.*' => [Rule::in(array_keys(TenancyDocuments::DOCUMENTS))],
        ], [
            'emergency_signature_image.required' => 'Your emergency contact needs to sign on their signature pad.',
        ]);

        if (count(array_unique($signed['acknowledged'])) !== count(TenancyDocuments::DOCUMENTS)) {
            return response()->json([
                'message' => 'Please confirm you have read all three documents before signing.',
            ], 422);
        }

        foreach (['signature_image', 'emergency_signature_image'] as $field) {
            if (! preg_match('/^data:image\/png;base64,[A-Za-z0-9+\/=]+$/', $signed[$field]) || strlen($signed[$field]) > 600_000) {
                return response()->json([
                    'message' => 'Something went wrong reading a signature. Please clear it and sign again.',
                ], 422);
            }
        }

        if (empty($applicant['emergency_contact_name'])) {
            return response()->json([
                'message' => 'Please enter your emergency contact’s name before they sign.',
            ], 422);
        }

        $signedAt = now();
        $applicant['emergency_billing_consent'] = (bool) $signed['emergency_billing_consent'];

        $path = self::SIGNED_DOCUMENTS_DIR . Str::uuid() . '.pdf';

        // Freeze a copy of any document the owner uploaded, so replacing
        // that file later never changes what this applicant signed.
        $uploadedCopies = [];
        foreach (array_keys(TenancyDocuments::DOCUMENTS) as $document) {
            if ($source = $documents->uploaded($document)) {
                $copy = preg_replace('/\.pdf$/', '-' . $document . '.pdf', $path);
                Storage::disk('public')->copy($source, $copy);
                $uploadedCopies[$document] = Storage::disk('public')->url($copy);
            }
        }

        $data = $documents->data($applicant, $bed, [
            'tenant' => $signed['signature_image'],
            'emergency_contact' => $signed['emergency_signature_image'],
            'signed_at' => $signedAt,
            'uploaded_copies' => $uploadedCopies,
        ]);

        Storage::disk('public')->put($path, $documents->pdf($data)->output());

        Storage::disk('public')->put($this->signingRecordPath($path), json_encode([
            'signed_at' => $signedAt->toIso8601String(),
            'fingerprint' => $this->signingFingerprint($applicant, $bed),
            'emergency_contact_signed' => true,
            'emergency_billing_consent' => $applicant['emergency_billing_consent'],
            'uploaded_copies' => $uploadedCopies,
        ]));

        return response()->json([
            'message' => 'Documents signed.',
            'signed_contract_path' => $path,
            'preview_url' => Storage::disk('public')->url($path),
            'signed_at' => $signedAt->format('F j, Y'),
        ]);
    }

    /** Where signed tenancy documents are saved (public disk). */
    private const SIGNED_DOCUMENTS_DIR = 'application-documents/signed-contracts/';

    private function signingRecordPath(string $pdfPath): string
    {
        return preg_replace('/\.pdf$/', '.json', $pdfPath);
    }

    /**
     * The details printed on the signed documents. If any of these change
     * between signing and submitting, the signed copy no longer matches
     * the application, so it has to be signed again.
     */
    private function signingFingerprint(array $applicant, ?Bed $bed): string
    {
        $fields = ['first_name', 'last_name', 'home_address', 'contact_number', 'email',
            'emergency_contact_name', 'emergency_contact_number', 'emergency_contact_relation', 'preferred_start_date', 'tenant_end_date'];

        $values = array_map(fn ($f) => trim((string) ($applicant[$f] ?? '')), $fields);
        $values[] = (string) $bed?->id;

        return hash('sha256', implode('|', $values));
    }

    /**
     * Reads back what was signed (see signContract()). Returns null when the
     * path isn't one of our signed documents, or the record is missing.
     */
    private function readSigningRecord(?string $path): ?array
    {
        if (! $path || ! str_starts_with($path, self::SIGNED_DOCUMENTS_DIR) || str_contains($path, '..')) {
            return null;
        }

        $disk = Storage::disk('public');
        $record = $this->signingRecordPath($path);

        if (! $disk->exists($path) || ! $disk->exists($record)) {
            return null;
        }

        return json_decode($disk->get($record), true) ?: null;
    }

    /**
     * Validates the applicant details the documents print, and checks the
     * chosen bed. Returns [applicant fields, Bed|null].
     */
    private function validateContractData(Request $request): array
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'home_address' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_number' => ['nullable', 'string', 'max:20'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:50'],
            'bed_id' => ['nullable', 'integer', 'exists:beds,id'],
            'preferred_start_date' => ['nullable', 'date'],
            'tenant_end_date' => ['nullable', 'date'],
        ]);

        if ($error = $this->sameNumberError($data)) {
            throw \Illuminate\Validation\ValidationException::withMessages(['emergency_contact_number' => [$error]]);
        }

        $bed = ! empty($data['bed_id'])
            ? Bed::with('room.roomType', 'room.floor')->find($data['bed_id'])
            : null;

        return [$data, $bed];
    }

    /**
     * The emergency contact has to be a different person, so their
     * cellphone number can't be the applicant's own. Numbers are compared
     * after normalizing, so "0917 123 4567" and "+639171234567" match.
     */
    private function sameNumberError(array $data): ?string
    {
        $textbee = app(TextbeeService::class);
        $mine = $textbee->normalizePhilippineNumber((string) ($data['contact_number'] ?? ''));
        $theirs = $textbee->normalizePhilippineNumber((string) ($data['emergency_contact_number'] ?? ''));

        if ($mine && $mine === $theirs) {
            return "Your emergency contact's cellphone number can't be the same as your own.";
        }

        return null;
    }

    /**
     * Apply page: texts a 6-digit code to the applicant's cellphone number.
     * The code lasts 10 minutes. A number can only get a new code once a
     * minute, so the SMS gateway can't be spammed.
     */
    public function sendOtp(Request $request, TextbeeService $textbee): JsonResponse
    {
        $data = $request->validate(['contact_number' => ['required', 'string', 'max:20']]);

        $number = $textbee->normalizePhilippineNumber($data['contact_number']);
        if (! $number) {
            return response()->json([
                'message' => 'Please enter a valid Philippine cellphone number (e.g. 09171234567).',
            ], 422);
        }

        if (! Cache::add('apply-otp-cooldown:' . $number, true, now()->addMinute())) {
            return response()->json([
                'message' => 'A code was just sent. Please wait a minute before asking for a new one.',
            ], 429);
        }

        $code = (string) random_int(100000, 999999);
        Cache::put('apply-otp:' . $number, ['code' => $code, 'attempts' => 0], now()->addMinutes(10));

        $sent = $textbee->send($number, 'Your ' . TextbeeService::BRAND_NAME . " verification code is {$code}. It expires in 10 minutes. Do not share this code with anyone.");

        if (! $sent) {
            Cache::forget('apply-otp-cooldown:' . $number);

            return response()->json([
                'message' => "We couldn't send the code right now. Please try again in a moment.",
            ], 503);
        }

        return response()->json(['message' => 'Code sent. Please check your messages.']);
    }

    /**
     * Apply page: checks the code the applicant typed. On success, hands
     * back a token the application submission must include (good for 1
     * hour). After 5 wrong tries the code is thrown away.
     */
    public function verifyOtp(Request $request, TextbeeService $textbee): JsonResponse
    {
        $data = $request->validate([
            'contact_number' => ['required', 'string', 'max:20'],
            'code' => ['required', 'digits:6'],
        ], [
            'code.digits' => 'The code is 6 digits.',
        ]);

        $number = $textbee->normalizePhilippineNumber($data['contact_number']);
        $entry = $number ? Cache::get('apply-otp:' . $number) : null;

        if (! $entry) {
            return response()->json([
                'message' => 'This code has expired. Please request a new one.',
            ], 422);
        }

        if (! hash_equals($entry['code'], $data['code'])) {
            $entry['attempts']++;
            if ($entry['attempts'] >= 5) {
                Cache::forget('apply-otp:' . $number);

                return response()->json([
                    'message' => 'Too many wrong tries. Please request a new code.',
                ], 422);
            }
            Cache::put('apply-otp:' . $number, $entry, now()->addMinutes(10));

            return response()->json(['message' => 'That code is incorrect. Please try again.'], 422);
        }

        Cache::forget('apply-otp:' . $number);
        $token = Str::random(40);
        Cache::put('apply-otp-verified:' . $token, $number, now()->addHour());

        return response()->json([
            'message' => 'Cellphone number verified.',
            'phone_verification_token' => $token,
        ]);
    }
}
