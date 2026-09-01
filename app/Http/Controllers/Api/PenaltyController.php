<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BillingStatement;
use App\Models\Penalty;
use App\Models\PenaltyAuditLog;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PenaltyController extends Controller
{
    /**
     * Admin: list penalties. Optional ?tenant_id= and ?status= filters.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'tenant_id' => ['nullable', 'integer', 'exists:tenants,id'],
            'status' => ['nullable', Rule::in(['active', 'waived'])],
        ]);

        $query = Penalty::with([
            'tenant:id,full_name',
            'tenant.activeContract.bed.room:id,room_no',
            'damage:id,description,date_incurred,photo_path',
            'createdBy:id,name',
        ])->latest();

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->input('tenant_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // room_no, damage_photo_url, and date are computed here (not stored
        // columns) so the admin Penalties table can show them without a
        // second request — same pattern as $bill->balance / $payment->proof_url
        // elsewhere in this codebase. "date" unifies the two ways a penalty
        // can carry a date: a damage-linked one uses its Damage's own
        // date_incurred, a manually-added one uses its own date_incurred
        // column — falling back to created_at for rows added before that
        // column existed.
        return response()->json(
            $query->get()->map(function ($penalty) {
                $penalty->room_no = $penalty->tenant?->activeContract?->bed?->room?->room_no;
                $penalty->damage_photo_url = $penalty->damage?->photo_path
                    ? Storage::disk('public')->url($penalty->damage->photo_path)
                    : null;
                $penalty->date = $penalty->type === 'damage'
                    ? $penalty->damage?->date_incurred
                    : ($penalty->date_incurred ?? $penalty->created_at?->toDateString());

                return $penalty;
            })
        );
    }

    /**
     * Admin: view a single penalty with its full audit trail.
     */
    public function show(Penalty $penalty): JsonResponse
    {
        return response()->json(
            $penalty->load(['tenant', 'damage', 'billingStatement', 'createdBy:id,name', 'auditLogs.performedBy:id,name'])
        );
    }

    /**
     * Admin: manually add a penalty not tied to a recorded damage
     * (e.g. a late fee, a rule violation charge).
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'type' => ['nullable', Rule::in(['manual', 'other'])],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date_incurred' => ['nullable', 'date', 'before_or_equal:today'],
        ], [
            'date_incurred.before_or_equal' => 'The date cannot be in the future.',
        ]);

        $penalty = DB::transaction(function () use ($data, $request) {
            $penalty = Penalty::create([
                'tenant_id' => $data['tenant_id'],
                'damage_id' => null,
                'type' => $data['type'] ?? 'manual',
                'description' => $data['description'],
                'amount' => $data['amount'],
                'date_incurred' => $data['date_incurred'] ?? now()->toDateString(),
                'status' => 'active',
                'created_by' => $request->user()?->id,
            ]);

            PenaltyAuditLog::create([
                'penalty_id' => $penalty->id,
                'action' => 'created',
                'performed_by' => $request->user()?->id,
                'created_at' => now(),
            ]);

            return $penalty;
        });

        return response()->json([
            'message' => 'Penalty added.',
            'penalty' => $penalty,
            'running_total' => $this->computeRunningTotal($data['tenant_id']),
        ], 201);
    }

    /**
     * Admin: edit an active penalty's description/amount.
     */
    public function update(Request $request, Penalty $penalty): JsonResponse
    {
        if ($penalty->status !== 'active') {
            return response()->json([
                'message' => 'Only active penalties can be edited.',
            ], 409);
        }

        $data = $request->validate([
            'description' => ['sometimes', 'required', 'string', 'max:255'],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0.01'],
        ]);

        $amountChanged = isset($data['amount']) && (float) $data['amount'] !== (float) $penalty->amount;

        DB::transaction(function () use ($penalty, $data, $amountChanged) {
            $penalty->update($data);

            // If it's already on a bill, that bill's totals must move with it.
            if ($amountChanged && $penalty->billing_id) {
                $this->resyncBillingTotals($penalty->billing_id);
            }
        });

        return response()->json([
            'message' => 'Penalty updated.',
            'penalty' => $penalty->fresh(),
            'running_total' => $this->computeRunningTotal($penalty->tenant_id),
        ]);
    }

    /**
     * Admin: waive a penalty (Thursday's task).
     *
     * This is a status change, not a delete — the penalty stays on record with
     * a full audit-log entry recording who waived it, when, and why. If it was
     * already folded into a billing statement, that statement's penalty and
     * total amounts are recalculated so the tenant isn't still charged for it.
     */
    public function waive(Request $request, Penalty $penalty): JsonResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'reason.required' => 'A reason is required when waiving a penalty.',
        ]);

        if ($penalty->status === 'waived') {
            return response()->json([
                'message' => 'This penalty has already been waived.',
            ], 409);
        }

        DB::transaction(function () use ($penalty, $data, $request) {
            $penalty->update(['status' => 'waived']);

            PenaltyAuditLog::create([
                'penalty_id' => $penalty->id,
                'action' => 'waived',
                'performed_by' => $request->user()?->id,
                'reason' => $data['reason'],
                'created_at' => now(),
            ]);

            if ($penalty->billing_id) {
                $this->resyncBillingTotals($penalty->billing_id);
            }
        });

        return response()->json([
            'message' => 'Penalty waived.',
            'penalty' => $penalty->fresh()->load('auditLogs.performedBy:id,name'),
            'running_total' => $this->computeRunningTotal($penalty->tenant_id),
        ]);
    }

    /**
     * Admin: reinstate a waived penalty (e.g. waived in error, or a disputed
     * charge that was later upheld). Also fully audit-logged.
     */
    public function reinstate(Request $request, Penalty $penalty): JsonResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'reason.required' => 'A reason is required when reinstating a penalty.',
        ]);

        if ($penalty->status !== 'waived') {
            return response()->json([
                'message' => 'Only waived penalties can be reinstated.',
            ], 409);
        }

        DB::transaction(function () use ($penalty, $data, $request) {
            $penalty->update(['status' => 'active']);

            PenaltyAuditLog::create([
                'penalty_id' => $penalty->id,
                'action' => 'reinstated',
                'performed_by' => $request->user()?->id,
                'reason' => $data['reason'],
                'created_at' => now(),
            ]);

            if ($penalty->billing_id) {
                $this->resyncBillingTotals($penalty->billing_id);
            }
        });

        return response()->json([
            'message' => 'Penalty reinstated.',
            'penalty' => $penalty->fresh()->load('auditLogs.performedBy:id,name'),
            'running_total' => $this->computeRunningTotal($penalty->tenant_id),
        ]);
    }

    /**
     * Admin: remove a penalty entirely (distinct from Waive — a hard delete
     * for genuine mistakes, e.g. added to the wrong tenant).
     */
    public function destroy(Penalty $penalty): JsonResponse
    {
        if ($penalty->billing_id) {
            return response()->json([
                'message' => 'This penalty is already included in a billing statement and cannot be deleted. Waive it instead.',
            ], 409);
        }

        $tenantId = $penalty->tenant_id;

        DB::transaction(function () use ($penalty) {
            $penalty->auditLogs()->delete();
            $penalty->delete();
        });

        return response()->json([
            'message' => 'Penalty deleted.',
            'running_total' => $this->computeRunningTotal($tenantId),
        ]);
    }

    /**
     * A tenant's current running total: unpaid balance across all billing
     * statements, plus any active penalties not yet folded into a bill.
     */
    public function runningTotal(Tenant $tenant): JsonResponse
    {
        return response()->json($this->computeRunningTotal($tenant->id));
    }

    private function computeRunningTotal(int $tenantId): array
    {
        $bills = BillingStatement::where('tenant_id', $tenantId)
            ->whereIn('status', ['unpaid', 'partial', 'overdue'])
            ->with('payments')
            ->get();

        $unpaidBillingTotal = $bills->sum(function ($bill) {
            return $bill->total_amount - $bill->payments->sum('amount_paid');
        });

        $unbilledPenaltyTotal = Penalty::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->whereNull('billing_id')
            ->sum('amount');

        return [
            'tenant_id' => $tenantId,
            'unpaid_billing_total' => round($unpaidBillingTotal, 2),
            'unbilled_penalty_total' => round($unbilledPenaltyTotal, 2),
            'grand_total' => round($unpaidBillingTotal + $unbilledPenaltyTotal, 2),
        ];
    }

    /**
     * Recalculates a billing statement's penalty_amount and total_amount from
     * its currently-active penalty line items. Called whenever a penalty on
     * that statement is waived, reinstated, or has its amount edited.
     */
    private function resyncBillingTotals(int $billingId): void
    {
        $bill = BillingStatement::find($billingId);
        if (! $bill) {
            return;
        }

        $activePenaltyTotal = Penalty::where('billing_id', $billingId)
            ->where('status', 'active')
            ->sum('amount');

        $bill->update([
            'penalty_amount' => $activePenaltyTotal,
            'total_amount' => $bill->base_rent + $bill->utilities_amount + $bill->wifi_amount + $activePenaltyTotal,
        ]);
    }
}