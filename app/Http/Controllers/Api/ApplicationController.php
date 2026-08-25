<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    private const STATUSES = ['pending', 'approved', 'rejected', 'cancelled'];

    /**
     * Public "Apply for Occupancy" submission. No authentication required —
     * the applicant is not a tenant yet, so their personal info is stored on
     * the application itself. tenant_id stays null until approval.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'inquiry_id' => ['nullable', 'integer', 'exists:inquiries,id'],

            // Applicant's own details (no tenants row exists for them yet).
            'full_name' => ['required', 'string', 'max:150'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_number' => ['nullable', 'string', 'max:20'],

            'bed_id' => ['required', 'integer', 'exists:beds,id'],
            'preferred_start_date' => ['nullable', 'date', 'after_or_equal:today'],

            // Same server-side RA 10173 consent enforcement as the inquiry form.
            'dpa_consent' => ['required', 'accepted'],
        ], [
            'dpa_consent.required' => 'You must consent to the data privacy notice before submitting.',
            'dpa_consent.accepted' => 'You must consent to the data privacy notice before submitting.',
            'preferred_start_date.after_or_equal' => 'Preferred start date cannot be in the past.',
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

        // Availability is checked live at submission time, not just in the UI.
        if ($bed->status !== 'vacant') {
            return response()->json([
                'message' => 'That bedspace is no longer available. Please choose another.',
            ], 409);
        }

        // Block double-applying for the same bed while one is still pending.
        $alreadyPending = Application::where('bed_id', $bed->id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyPending) {
            return response()->json([
                'message' => 'There is already a pending application for that bedspace.',
            ], 409);
        }

        $application = Application::create([
            'inquiry_id' => $data['inquiry_id'] ?? null,
            'tenant_id' => null, // set on approval, not now
            'full_name' => $data['full_name'],
            'contact_number' => $data['contact_number'] ?? null,
            'email' => $data['email'] ?? null,
            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_number' => $data['emergency_contact_number'] ?? null,
            'bed_id' => $bed->id,
            'preferred_start_date' => $data['preferred_start_date'] ?? null,
            'dpa_consent' => true,
            'status' => 'pending',
        ]);

        // If this came from an inquiry, mark that inquiry as converted.
        if ($application->inquiry_id) {
            Inquiry::where('id', $application->inquiry_id)
                ->where('status', '!=', 'converted')
                ->update(['status' => 'converted']);
        }

        $this->notify('application.submitted', [
            'application_id' => $application->id,
            'applicant' => $application->full_name,
            'bed_id' => $bed->id,
        ]);

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
            // Surface the returning-applicant flag in the review queue so the
            // admin sees who qualifies for a discount before opening each one.
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
     */
    public function approve(Request $request, Application $application): JsonResponse
    {
        $data = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'monthly_rate' => ['nullable', 'numeric', 'min:0'],
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

        if ($bed->status !== 'vacant') {
            return response()->json([
                'message' => 'That bedspace is no longer vacant and cannot be assigned.',
            ], 409);
        }

        $returningTenant = $this->findReturningTenant($application);

        $result = DB::transaction(function () use ($application, $bed, $data, $returningTenant, $request) {
            // Reuse the existing tenant record for a returning tenant, so their
            // history stays intact. Otherwise create a fresh one from the
            // details captured on the application.
            $tenant = $returningTenant;

            if (! $tenant) {
                $tenant = $this->createTenantWithLogin($application);
            }

            $contract = LeaseContract::create([
                'application_id' => $application->id,
                'tenant_id' => $tenant->id,
                'bed_id' => $bed->id,
                'inquiry_id' => $application->inquiry_id,
                'start_date' => $data['start_date']
                    ?? $application->preferred_start_date
                    ?? now()->toDateString(),
                'end_date' => $data['end_date'] ?? null,
                'monthly_rate' => $data['monthly_rate'] ?? $bed->room->monthly_rate ?? 0,
                'esign_status' => 'pending',
                'status' => 'pending', // becomes 'active' once the contract is signed
                'created_by' => $request->user()?->id,
                'approved_by' => $request->user()?->id,
            ]);

            $bed->update(['status' => 'occupied']);
            $bed->room->syncStatusFromBeds();

            $application->update([
                'status' => 'approved',
                'tenant_id' => $tenant->id,
                'approved_by' => $request->user()?->id,
            ]);

            return ['tenant' => $tenant, 'contract' => $contract];
        });

        $this->notify('application.approved', [
            'application_id' => $application->id,
            'tenant_id' => $result['tenant']->id,
            'contract_id' => $result['contract']->id,
            'returning_tenant' => (bool) $returningTenant,
            'approved_by' => $request->user()?->id,
        ]);

        return response()->json([
            'message' => $returningTenant
                ? 'Application approved. This is a returning tenant — they may qualify for a discount.'
                : 'Application approved and lease contract created.',
            'discount_eligible' => (bool) $returningTenant,
            'application' => $application->fresh()->load('bed.room:id,room_no,room_type,monthly_rate'),
            'tenant' => $result['tenant'],
            'contract' => $result['contract'],
        ]);
    }

    /**
     * Admin: reject a pending application.
     */
    public function reject(Request $request, Application $application): JsonResponse
    {
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        if ($application->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending applications can be rejected.',
            ], 409);
        }

        $application->update([
            'status' => 'rejected',
            'approved_by' => $request->user()?->id, // records who actioned it
        ]);

        // NOTE: applications has no rejection_reason column, so the reason is
        // only logged for now, not persisted on the record. Raise with PELEA
        // if the reason needs to be stored and shown back to the applicant.
        $this->notify('application.rejected', [
            'application_id' => $application->id,
            'applicant' => $application->full_name,
            'reason' => $data['reason'] ?? null,
            'rejected_by' => $request->user()?->id,
        ]);

        return response()->json([
            'message' => 'Application rejected.',
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

        $application->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Application cancelled.',
            'application' => $application->fresh(),
        ]);
    }

    /**
     * Creates the tenant record together with a login account, so an approved
     * tenant can actually sign in to the portal. Without this link, the
     * tenant-scoped routes have no tenant to resolve from the session.
     *
     * If the applicant gave no email there's no way to create an account, so
     * the tenant record is created unlinked — an admin can attach a login
     * later once contact details are on file.
     */
    private function createTenantWithLogin(Application $application): Tenant
    {
        $tenantUser = null;

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

                // The temporary password is only logged for now — replace this
                // with a welcome email once mail delivery is set up. Until then,
                // the tenant should use "Forgot Password" to set their own.
                $this->notify('tenant.account_created', [
                    'user_id' => $tenantUser->id,
                    'email' => $tenantUser->email,
                    'temporary_password' => $temporaryPassword,
                ]);
            }
        }

        return Tenant::create([
            'user_id' => $tenantUser?->id,
            'full_name' => $application->full_name,
            'contact_number' => $application->contact_number,
            'email' => $application->email,
            'emergency_contact_name' => $application->emergency_contact_name,
            'emergency_contact_number' => $application->emergency_contact_number,
        ]);
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