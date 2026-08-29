<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DormitoryProfile;
use App\Models\LeaseContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LeaseContractController extends Controller
{
    private const STATUSES = ['pending', 'active', 'terminated', 'expired'];

    /**
     * Renders the admin Contract Management page. Server-renders the contract
     * list so the page has data on first paint; the sign/not-applicable/
     * terminate actions below are then called from the page directly.
     */
    public function page(): \Illuminate\View\View
    {
        $contracts = LeaseContract::with([
            'tenant:id,full_name,email,contact_number,emergency_contact_name,emergency_contact_number',
            'bed:id,room_id,bed_label,status',
            'bed.room:id,room_no,room_type,monthly_rate',
            'application:id,full_name,status,home_address',
            'createdBy:id,name',
            'approvedBy:id,name',
        ])->latest()->get()->map(function ($contract) {
            return [
                'id' => $contract->id,
                'status' => $contract->status,
                'esign_status' => $contract->esign_status,
                'start_date' => $contract->start_date?->format('M j, Y'),
                'end_date' => $contract->end_date?->format('M j, Y'),
                'monthly_rate' => $contract->monthly_rate,
                'signed_at' => $contract->signed_at?->format('M j, Y g:ia'),
                'signed_document_url' => $this->publicUrlFor($contract->signed_document_url),
                'created_at' => $contract->created_at?->format('M j, Y'),
                'tenant' => [
                    'full_name' => $contract->tenant?->full_name,
                    'email' => $contract->tenant?->email,
                    'contact_number' => $contract->tenant?->contact_number,
                    'emergency_contact_name' => $contract->tenant?->emergency_contact_name,
                    'emergency_contact_number' => $contract->tenant?->emergency_contact_number,
                ],
                'room_no' => $contract->bed?->room?->room_no,
                'bed_label' => $contract->bed?->bed_label,
                'home_address' => $contract->application?->home_address,
                'approved_by' => $contract->approvedBy?->name,
                'created_by' => $contract->createdBy?->name,
            ];
        })->values();

        $profile = DormitoryProfile::current();

        return view('admincontracts', [
            'contracts' => $contracts,
            'dormName' => $profile->dorm_name ?: 'Pureza Station Dormitory',
            'dormAddress' => $profile->address,
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

        $query = LeaseContract::with([
            'tenant:id,full_name,email,contact_number',
            'bed:id,room_id,bed_label,status',
            'bed.room:id,room_no,room_type,monthly_rate',
            'application:id,full_name,status',
        ])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return response()->json($query->get());
    }

    /**
     * Admin: view a single contract with its full audit trail.
     */
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
     * Submit the signed contract.
     *
     * The team decided on scanned physical paper rather than true e-signature,
     * so this accepts an uploaded scan of the signed copy. Once recorded, the
     * contract becomes active and the tenant is fully onboarded.
     *
     * Accepts multipart/form-data with a `signed_document` file, OR a
     * `signed_document_url` string if the scan is hosted elsewhere.
     */
    public function submitSigned(Request $request, LeaseContract $leaseContract): JsonResponse
    {
        $data = $request->validate([
            'signed_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'], // 10MB
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
            // Replace any previous upload rather than orphaning the old file.
            if ($path && ! str_starts_with($path, 'http') && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            $path = $request->file('signed_document')->store('signed-contracts', 'public');
        } elseif (! empty($data['signed_document_url'])) {
            $path = $data['signed_document_url'];
        }

        DB::transaction(function () use ($leaseContract, $path, $data, $request) {
            $leaseContract->update([
                'signed_document_url' => $path,
                'signed_at' => $data['signed_at'] ?? now(),
                'esign_status' => 'signed',
                'status' => 'active', // tenant is now fully onboarded
                'approved_by' => $leaseContract->approved_by ?? $request->user()?->id,
            ]);
        });

        $this->notify('contract.signed', [
            'contract_id' => $leaseContract->id,
            'tenant_id' => $leaseContract->tenant_id,
            'bed_id' => $leaseContract->bed_id,
            'recorded_by' => $request->user()?->id,
            'signed_at' => $leaseContract->fresh()->signed_at,
        ]);

        return response()->json([
            'message' => 'Signed contract recorded. The lease is now active.',
            'contract' => $leaseContract->fresh()->load([
                'tenant:id,full_name',
                'bed:id,room_id,bed_label,status',
                'bed.room:id,room_no',
            ]),
            'signed_document_url' => $this->publicUrlFor($path),
            'signed_at' => $leaseContract->fresh()->signed_at?->format('M j, Y g:ia'),
        ]);
    }

    /**
     * Admin: mark a contract as not requiring a signature (e.g. legacy tenants
     * onboarded before the contract process existed).
     */
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

        $this->notify('contract.marked_not_applicable', [
            'contract_id' => $leaseContract->id,
            'marked_by' => $request->user()?->id,
        ]);

        return response()->json([
            'message' => 'Contract marked as not requiring a signature. The lease is now active.',
            'contract' => $leaseContract->fresh(),
        ]);
    }

    /**
     * Admin: terminate an active contract and release the bed back to vacant.
     */
    public function terminate(Request $request, LeaseContract $leaseContract): JsonResponse
    {
        if (! in_array($leaseContract->status, ['pending', 'active'], true)) {
            return response()->json([
                'message' => 'Only pending or active contracts can be terminated.',
            ], 409);
        }

        DB::transaction(function () use ($leaseContract) {
            $leaseContract->update([
                'status' => 'terminated',
                'end_date' => $leaseContract->end_date ?? now()->toDateString(),
            ]);

            $bed = $leaseContract->bed;
            if ($bed && $bed->status === 'occupied') {
                $bed->update(['status' => 'vacant']);
                $bed->room?->syncStatusFromBeds();
            }
        });

        $this->notify('contract.terminated', [
            'contract_id' => $leaseContract->id,
            'tenant_id' => $leaseContract->tenant_id,
            'terminated_by' => $request->user()?->id,
        ]);

        return response()->json([
            'message' => 'Contract terminated and bedspace released.',
            'contract' => $leaseContract->fresh(),
        ]);
    }

    /**
     * A signed document can be either an uploaded file on the public disk or
     * an external link, so the stored value is only run through Storage when
     * it isn't already a full URL.
     */
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
     * Notification stub — same placeholder approach as ApplicationController.
     */
    private function notify(string $event, array $payload): void
    {
        Log::info("[notification stub] {$event}", $payload);
    }
}