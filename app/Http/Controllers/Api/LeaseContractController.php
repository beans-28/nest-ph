<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bed;
use App\Models\DormitoryProfile;
use App\Models\LeaseContract;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LeaseContractController extends Controller
{
    private const STATUSES = ['pending', 'active', 'expiring_soon', 'expired', 'terminated'];

    private const EXPIRING_SOON_WINDOW_DAYS = 30;

    /**
     * Renders the admin Lease Management page: stat cards, tab counts, and
     * the contract table itself.
     *
     * NOTE: statuses are refreshed on every page load rather than by a real
     * scheduled job (step 14 in the use case calls for an automated,
     * scheduled check). There's no cron/task-scheduler wired up in this
     * project yet, so "Expiring Soon" / "Expired" would otherwise only
     * update whenever this page happens to be visited. Wiring
     * `php artisan schedule:run` to a real server cron is real remaining
     * infrastructure work, not something this page alone can provide.
     */
    public function page(Request $request)
    {
        // Pureza revision (2026-11-14): Lease Management is now part of the
        // tenant profile in the Tenant Manager, so this old page sends the
        // admin there (old links and bookmarks keep working).
        return redirect()->route('tenant-manager.index', array_filter(['lease' => $request->query('lease')]));
    }

    /**
     * GET /lease-contracts/tenants/{tenant}: every lease of one tenant,
     * newest first, for the Lease tab of the tenant profile.
     */
    public function forTenant(Tenant $tenant): JsonResponse
    {
        $this->syncExpiringAndExpired();

        return response()->json([
            'contracts' => LeaseContract::where('tenant_id', $tenant->id)
                ->with(['tenant:id,first_name,last_name', 'bed:id,room_id,bed_label,status', 'bed.room:id,room_no'])
                ->latest('id')->get()
                ->map(fn ($c) => $this->transformRow($c))->values(),
            'notarization_required' => (bool) $this->profile()->notarization_required,
        ]);
    }

    /**
     * Admin: list contracts, newest first. Optional ?status= filter.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'status' => ['nullable', Rule::in(self::STATUSES)],
        ]);

        $this->syncExpiringAndExpired();

        $query = LeaseContract::with([
            'tenant:id,first_name,last_name,email,contact_number',
            'bed:id,room_id,bed_label,status',
            'bed.room:id,room_no,room_type,monthly_rate',
            'application:id,first_name,last_name,status',
        ])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return response()->json($query->get());
    }

    public function show(LeaseContract $leaseContract): JsonResponse
    {
        return response()->json($leaseContract->load([
            'tenant',
            'bed:id,room_id,bed_label,status',
            'bed.room:id,room_no,room_type,monthly_rate',
            'application',
            'createdBy:id,name',
            'approvedBy:id,name',
        ]));
    }

    /**
     * Admin: search registered tenants by name, for the Add Lease Contract
     * tenant picker. Use Case Report step 4: "Display a searchable list of
     * registered tenants."
     */
    public function searchTenants(Request $request): JsonResponse
    {
        $q = trim((string) $request->input('q', ''));

        $tenants = Tenant::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%");
                });
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->limit(15)
            ->get(['id', 'first_name', 'last_name', 'email', 'contact_number']);

        return response()->json($tenants);
    }
    /**
     * Admin: manually add a lease contract for an already-registered tenant.
     * Use Case Report steps 3–8 — this is the standalone contract-creation
     * flow, separate from the one Application::approve() creates
     * automatically during onboarding.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'bed_id' => ['required', 'integer', 'exists:beds,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'monthly_rate' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'signed_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'confirm_existing_active_lease' => ['nullable', 'boolean'],
        ], [
            'end_date.after' => 'Lease end date must be after the start date.',
        ]);

        // Step 8.2: a tenant with an existing active lease needs explicit
        // admin confirmation before a second one is created for them.
        $existingActive = LeaseContract::where('tenant_id', $data['tenant_id'])
            ->whereIn('status', ['active', 'expiring_soon'])
            ->exists();

        if ($existingActive && ! $request->boolean('confirm_existing_active_lease')) {
            return response()->json([
                'message' => 'This tenant already has an active lease. Do you want to proceed?',
                'requires_confirmation' => true,
            ], 409);
        }

        $bed = Bed::with('room')->findOrFail($data['bed_id']);

        // Step 8.3: the room/bedspace can't already be committed elsewhere.
        if ($bed->status !== 'vacant') {
            return response()->json([
                'message' => 'That bedspace is not available and cannot be assigned.',
            ], 409);
        }

        $signedPath = $request->hasFile('signed_document')
            ? $request->file('signed_document')->store('signed-contracts', 'public')
            : null;

        $contract = DB::transaction(function () use ($data, $bed, $signedPath, $request) {
            $baseRate = $data['monthly_rate'] ?? $bed->room->perBedRate();
            $discountAmount = $data['discount_amount'] ?? 0;
            $monthlyRate = max(0, $baseRate - $discountAmount);

            $contract = LeaseContract::create([
                'tenant_id' => $data['tenant_id'],
                'bed_id' => $bed->id,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'monthly_rate' => $monthlyRate,
                'discount_amount' => $data['discount_amount'] ?? null,
                'esign_status' => $signedPath ? 'signed' : 'pending',
                'signed_document_url' => $signedPath,
                'signed_at' => $signedPath ? now() : null,
                'status' => 'active',
                'created_by' => $request->user()?->id,
                'approved_by' => $request->user()?->id,
            ]);

            $bed->update(['status' => 'occupied']);
            $bed->room->syncStatusFromBeds();

            return $contract;
        });

        Log::info('[notification stub] lease.added', [
            'contract_id' => $contract->id,
            'tenant_id' => $contract->tenant_id,
            'created_by' => $request->user()?->id,
        ]);

        return response()->json([
            'message' => 'Lease contract added successfully.',
            'contract' => $this->transformRow($contract->fresh(['tenant', 'bed.room'])),
        ], 201);
    }

    /**
     * Submit the signed contract (Wed Aug 26 flow — scanned physical paper).
     */
    public function submitSigned(Request $request, LeaseContract $leaseContract): JsonResponse
    {
        $data = $request->validate([
            'signed_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'signed_document_url' => ['nullable', 'string', 'max:255'],
            'signed_at' => ['nullable', 'date'],
        ]);

        if ($leaseContract->esign_status === 'signed') {
            return response()->json([
                'message' => 'This contract has already been signed.',
            ], 409);
        }

        if (! $request->hasFile('signed_document') && empty($data['signed_document_url'])) {
            return response()->json([
                'message' => 'Provide either a signed document file or a signed document URL.',
                'errors' => [
                    'signed_document' => ['Upload the signed contract, or provide a link to it.'],
                ],
            ], 422);
        }

        $path = $leaseContract->signed_document_url;

        if ($request->hasFile('signed_document')) {
            if ($path && ! str_starts_with($path, 'http') && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            $path = $request->file('signed_document')->store('signed-contracts', 'public');
        } elseif (! empty($data['signed_document_url'])) {
            $path = $data['signed_document_url'];
        }

        $leaseContract->update([
            'signed_document_url' => $path,
            'signed_at' => $data['signed_at'] ?? now(),
            'esign_status' => 'signed',
            'status' => 'active',
            'approved_by' => $leaseContract->approved_by ?? $request->user()?->id,
        ]);

        return response()->json([
            'message' => 'Signed contract recorded. The lease is now active.',
            'contract' => $leaseContract->fresh(),
            'signed_document_url' => $this->publicUrlFor($path),
            'signed_at' => $leaseContract->fresh()->signed_at?->format('M j, Y g:ia'),
        ]);
    }

    public function markNotApplicable(Request $request, LeaseContract $leaseContract): JsonResponse
    {
        if ($leaseContract->esign_status === 'signed') {
            return response()->json([
                'message' => 'This contract has already been signed and cannot be marked not applicable.',
            ], 409);
        }

        $leaseContract->update([
            'esign_status' => 'not_applicable',
            'status' => 'active',
            'approved_by' => $leaseContract->approved_by ?? $request->user()?->id,
        ]);

        return response()->json([
            'message' => 'Contract marked as not requiring a signature. The lease is now active.',
            'contract' => $leaseContract->fresh(),
        ]);
    }

    /**
     * Admin: upload (or replace) the notarized copy of a contract. Only
     * allowed while the dorm's "Require notarized contract" setting is on.
     */
    public function uploadNotarized(Request $request, LeaseContract $leaseContract): JsonResponse
    {
        if (! DormitoryProfile::current()->notarization_required) {
            return response()->json([
                'message' => 'Notarized contracts are turned off in the Rental Policy settings.',
            ], 409);
        }

        $request->validate([
            'notarized_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ], [
            'notarized_document.required' => 'Attach the scan of the notarized contract.',
        ]);

        // The admin checked the paper copy themselves, so it is approved
        // straight away.
        self::storeNotarized($leaseContract, $request->file('notarized_document'), 'approved', $request->user()?->id);

        return response()->json([
            'message' => 'Notarized contract uploaded and approved.',
            'contract' => $this->transformRow($leaseContract->fresh(['tenant', 'bed.room'])),
        ]);
    }

    /**
     * Save a notarized copy on the PRIVATE disk ("local"), replacing any
     * older file. Shared by the admin upload and the tenant upload.
     */
    public static function storeNotarized(LeaseContract $contract, \Illuminate\Http\UploadedFile $file, string $status, ?int $reviewerId = null): void
    {
        $old = $contract->notarized_document_path;
        $path = $file->store('notarized-contracts', 'local');

        $contract->update([
            'notarized_document_path' => $path,
            'notarized_at' => now(),
            'notarized_status' => $status,
            'notarized_rejection_reason' => null,
            'notarized_reviewed_at' => $status === 'approved' ? now() : null,
            'notarized_reviewed_by' => $status === 'approved' ? $reviewerId : null,
        ]);

        if ($old && $old !== $path && Storage::disk('local')->exists($old)) {
            Storage::disk('local')->delete($old);
        }
    }

    /**
     * Admin: approve the notarized copy a tenant submitted.
     */
    public function approveNotarized(Request $request, LeaseContract $leaseContract): JsonResponse
    {
        if ($leaseContract->notarized_status !== 'submitted') {
            return response()->json(['message' => 'There is no submitted notarized copy waiting for review.'], 409);
        }

        $leaseContract->update([
            'notarized_status' => 'approved',
            'notarized_rejection_reason' => null,
            'notarized_reviewed_at' => now(),
            'notarized_reviewed_by' => $request->user()?->id,
        ]);

        return response()->json([
            'message' => 'Notarized copy approved.',
            'contract' => $this->transformRow($leaseContract->fresh(['tenant', 'bed.room'])),
        ]);
    }

    /**
     * Admin: send a submitted notarized copy back to the tenant with a
     * reason. The file is kept so the admin can still see what was sent.
     */
    public function rejectNotarized(Request $request, LeaseContract $leaseContract): JsonResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:255'],
        ], [
            'reason.required' => 'Tell the tenant why the copy was not accepted.',
            'reason.min' => 'Give the tenant a clearer reason (at least 5 characters).',
        ]);

        if ($leaseContract->notarized_status !== 'submitted') {
            return response()->json(['message' => 'There is no submitted notarized copy waiting for review.'], 409);
        }

        $leaseContract->update([
            'notarized_status' => 'rejected',
            'notarized_rejection_reason' => $data['reason'],
            'notarized_reviewed_at' => now(),
            'notarized_reviewed_by' => $request->user()?->id,
        ]);

        return response()->json([
            'message' => 'Notarized copy sent back to the tenant.',
            'contract' => $this->transformRow($leaseContract->fresh(['tenant', 'bed.room'])),
        ]);
    }

    /**
     * Stream the notarized copy. Admins may view any contract's copy; a
     * tenant may view only their own (checked by the tenant route).
     */
    public static function streamNotarized(LeaseContract $leaseContract)
    {
        $path = $leaseContract->notarized_document_path;

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }

    public function viewNotarized(LeaseContract $leaseContract)
    {
        return self::streamNotarized($leaseContract);
    }

    /**
     * Admin: renew a lease. Use Case Report steps 10–11: the new end date
     * must be a valid future date AND later than the current end date, and
     * an Expiring Soon / Expired contract returns to Active.
     */
    public function renew(Request $request, LeaseContract $leaseContract): JsonResponse
    {
        $data = $request->validate([
            'end_date' => ['required', 'date', 'after:today'],
        ]);

        if (! in_array($leaseContract->status, ['active', 'expiring_soon', 'expired'], true)) {
            return response()->json([
                'message' => 'Only active, expiring, or expired contracts can be renewed.',
            ], 409);
        }

        // When the dorm requires notarization, a contract without its
        // notarized copy can't be renewed (the requirement must be met first).
        if (DormitoryProfile::current()->notarization_required && $leaseContract->notarized_status !== 'approved') {
            return response()->json([
                'message' => 'This contract needs an approved notarized copy before it can be renewed.',
            ], 409);
        }

        $newEndDate = \Carbon\Carbon::parse($data['end_date']);

        if ($newEndDate->lte($leaseContract->end_date)) {
            return response()->json([
                'message' => 'The new end date must be later than the current end date.',
                'errors' => ['end_date' => ['Must be later than the current end date.']],
            ], 422);
        }

        $leaseContract->update([
            'end_date' => $newEndDate,
            'status' => 'active',
            'last_renewed_at' => now(),
            'last_renewed_by' => $request->user()?->id,
        ]);

        Log::info('[notification stub] lease.renewed', [
            'contract_id' => $leaseContract->id,
            'new_end_date' => $newEndDate->toDateString(),
            'renewed_by' => $request->user()?->id,
        ]);

        \App\Models\TenantNotification::send($leaseContract->tenant_id, 'lease_renewed',
            'Your lease was renewed until ' . $newEndDate->format('F j, Y'),
            'You can view your lease details on your Profile page.', '/account');

        return response()->json([
            'message' => 'Lease renewed successfully.',
            'contract' => $this->transformRow($leaseContract->fresh(['tenant', 'bed.room'])),
        ]);
    }

    /**
     * Admin: terminate a lease.
     *
     * Use Case Report step 13: a reason is required — this previously
     * accepted termination with no reason at all, which the use case
     * explicitly disallows ("system blocks confirmation and prompts the
     * administrator").
     */
    public function terminate(Request $request, LeaseContract $leaseContract): JsonResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'reason.required' => 'A termination reason is required.',
        ]);

        if (! in_array($leaseContract->status, ['pending', 'active', 'expiring_soon'], true)) {
            return response()->json([
                'message' => 'Only pending, active, or expiring contracts can be terminated.',
            ], 409);
        }

        DB::transaction(function () use ($leaseContract, $data) {
            $leaseContract->update([
                'status' => 'terminated',
                'termination_reason' => $data['reason'],
                'terminated_at' => now(),
                'end_date' => $leaseContract->end_date ?? now()->toDateString(),
            ]);

            $bed = $leaseContract->bed;
            if ($bed && $bed->status === 'occupied') {
                $bed->update(['status' => 'vacant']);
                $bed->room?->syncStatusFromBeds();
            }
        });

        Log::info('[notification stub] lease.terminated', [
            'contract_id' => $leaseContract->id,
            'reason' => $data['reason'],
            'terminated_by' => $request->user()?->id,
        ]);

        return response()->json([
            'message' => 'Lease terminated successfully.',
            'contract' => $this->transformRow($leaseContract->fresh(['tenant', 'bed.room'])),
        ]);
    }

    /**
     * Admin: a tenant's currently active lease's room/bed. Used by the
     * Record Damage form to auto-load "Room/Bed" once a tenant is selected
     * (Use Case Report — Record Tenant Damage, step 2.1).
     */
    public function activeLeaseForTenant(Tenant $tenant): JsonResponse
    {
        $contract = LeaseContract::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->with('bed.room')
            ->latest()
            ->first();

        if (! $contract || ! $contract->bed) {
            return response()->json(['room' => null, 'bed' => null]);
        }

        return response()->json([
            'room' => [
                'id' => $contract->bed->room->id,
                'room_no' => $contract->bed->room->room_no,
            ],
            'bed' => [
                'id' => $contract->bed->id,
                'bed_label' => $contract->bed->bed_label,
            ],
        ]);
    }

    /**
     * Step 14: checks every active/expiring contract against today's date
     * and moves it into Expiring Soon (within 30 days of end_date) or
     * Expired (past end_date). Called at the top of every page/index load
     * as a stand-in for the scheduled job the use case describes — see the
     * note on page() above for what's still missing to make this truly
     * automated.
     */
    public function syncExpiringAndExpired(): void
    {
        $today = now()->startOfDay();
        $threshold = $today->copy()->addDays(self::EXPIRING_SOON_WINDOW_DAYS);

        LeaseContract::whereIn('status', ['active', 'expiring_soon'])
            ->whereNotNull('end_date')
            ->where('end_date', '<', $today)
            ->update(['status' => 'expired']);

        LeaseContract::where('status', 'active')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [$today, $threshold])
            ->update(['status' => 'expiring_soon']);
    }

    private function transformRow(LeaseContract $contract): array
    {
        $remainingDays = $contract->end_date
            ? now()->startOfDay()->diffInDays($contract->end_date, false)
            : null;

        return [
            'id' => $contract->id,
            'tenant_name' => $contract->tenant?->full_name ?? '—',
            'room_no' => $contract->bed?->room?->room_no,
            'bed_label' => $contract->bed?->bed_label,
            'start_date' => $contract->start_date?->format('M Y'),
            'end_date' => $contract->end_date?->format('M Y'),
            'remaining_days' => $remainingDays,
            'status' => $contract->status,
            'monthly_rate' => $contract->monthly_rate,
            'discount_amount' => $contract->discount_amount,
            'esign_status' => $contract->esign_status,
            'signed_document_url' => $this->publicUrlFor($contract->signed_document_url),
            'signed_at' => $contract->signed_at?->format('M j, Y g:ia'),
            'notarized_url' => $contract->notarized_document_path
                ? url('/lease-contracts/' . $contract->id . '/notarized')
                : null,
            'notarized_at' => $contract->notarized_at?->format('M j, Y g:ia'),
            'notarized_status' => $contract->notarized_status ?? 'none',
            'notarized_rejection_reason' => $contract->notarized_rejection_reason,
            'notarization_missing' => $contract->isMissingNotarizedCopy($this->profile()),
            'termination_reason' => $contract->termination_reason,
            'terminated_at' => $contract->terminated_at?->format('M j, Y g:ia'),
            'last_renewed_at' => $contract->last_renewed_at?->format('M j, Y g:ia'),
            'created_at' => $contract->created_at?->format('M j, Y'),
        ];
    }

    private ?DormitoryProfile $profileCache = null;

    private function profile(): DormitoryProfile
    {
        return $this->profileCache ??= DormitoryProfile::current();
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
}