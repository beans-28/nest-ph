<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BillingStatement;
use App\Models\LeaseContract;
use App\Models\Penalty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BillingController extends Controller
{
    /**
     * Grace period between a billing period starting and its due date.
     * Adjust here if the team decides on a different policy.
     */
    private const DUE_DATE_GRACE_DAYS = 5;

    /**
     * Admin: list billing statements, newest first. Optional filters.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'tenant_id' => ['nullable', 'integer', 'exists:tenants,id'],
            'status' => ['nullable', Rule::in(['unpaid', 'partial', 'paid', 'overdue'])],
        ]);

        $query = BillingStatement::with([
            'tenant:id,full_name,email,contact_number',
            'contract:id,bed_id,monthly_rate',
            'payments',
        ])->latest('due_date');

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->input('tenant_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return response()->json(
            $query->get()->map(fn ($bill) => $this->withBalance($bill))
        );
    }

    /**
     * Admin: view a single billing statement with its payments and the
     * penalty line items that make up its penalty_amount.
     */
    public function show(BillingStatement $billingStatement): JsonResponse
    {
        $billingStatement->load([
            'tenant',
            'contract.bed.room:id,room_no,room_type',
            'payments.recordedBy:id,name',
        ]);

        $billingStatement->penalties = Penalty::where('billing_id', $billingStatement->id)
            ->with('damage:id,description,date_incurred,photo_path')
            ->get();

        return response()->json($this->withBalance($billingStatement));
    }

    /**
     * Admin: generate the next billing statement for every active contract
     * that's due for one. Safe to call repeatedly.
     */
    public function generate(Request $request): JsonResponse
    {
        $contracts = LeaseContract::where('status', 'active')->get();

        $created = [];
        $skipped = [];

        foreach ($contracts as $contract) {
            $bill = $this->generateForContract($contract);

            if ($bill) {
                $created[] = $bill;
            } else {
                $skipped[] = $contract->id;
            }
        }

        return response()->json([
            'message' => count($created) . ' billing statement(s) generated.',
            'created' => $created,
            'skipped_contract_ids' => $skipped,
        ]);
    }

    /**
     * Admin: generate the next billing statement for one specific contract.
     */
    public function generateForContractEndpoint(LeaseContract $contract): JsonResponse
    {
        if ($contract->status !== 'active') {
            return response()->json([
                'message' => 'Only active contracts can be billed.',
            ], 409);
        }

        $bill = $this->generateForContract($contract);

        if (! $bill) {
            return response()->json([
                'message' => 'This contract already has a current-period statement, or is not yet due for one.',
            ], 409);
        }

        return response()->json([
            'message' => 'Billing statement generated.',
            'billing_statement' => $bill,
        ], 201);
    }

    /**
     * Admin: pull any outstanding unbilled penalties for this statement's
     * tenant onto it, without generating a new period. Useful when a damage
     * is recorded after that month's statement already went out.
     */
    public function attachPenalties(BillingStatement $billingStatement): JsonResponse
    {
        if ($billingStatement->status === 'paid') {
            return response()->json([
                'message' => 'This statement is already paid. Penalties will go onto the next statement instead.',
            ], 409);
        }

        $attached = DB::transaction(function () use ($billingStatement) {
            return $this->foldPenaltiesInto($billingStatement);
        });

        if ($attached === 0) {
            return response()->json([
                'message' => 'No unbilled penalties found for this tenant.',
            ], 409);
        }

        return response()->json([
            'message' => "{$attached} penalty line item(s) added to this statement.",
            'billing_statement' => $this->withBalance($billingStatement->fresh('payments')),
        ]);
    }

    /**
     * Core billing-generation logic.
     *
     * Monthly rent from the contract's current monthly_rate, one statement per
     * monthly period. Due date is a grace period after the period opens.
     * Period rolls forward from the last statement's end + 1 day, or the
     * contract's start_date if none exists yet — and only once that period has
     * actually begun, so future months aren't billed early.
     *
     * Any of the tenant's active, unbilled penalties are folded in as line
     * items at the same time (Tuesday's "penalty auto-appears as a billing
     * line item" requirement).
     */
    private function generateForContract(LeaseContract $contract): ?BillingStatement
    {
        $lastBill = BillingStatement::where('contract_id', $contract->id)
            ->orderByDesc('billing_period_end')
            ->first();

        $periodStart = $lastBill
            ? $lastBill->billing_period_end->copy()->addDay()
            : $contract->start_date->copy();

        // Not due yet — don't generate future periods early.
        if ($periodStart->isAfter(now())) {
            return null;
        }

        // Don't bill past the contract's end date, if it has one.
        if ($contract->end_date && $periodStart->isAfter($contract->end_date)) {
            return null;
        }

        $periodEnd = $periodStart->copy()->addMonthNoOverflow()->subDay();
        $dueDate = $periodStart->copy()->addDays(self::DUE_DATE_GRACE_DAYS);

        return DB::transaction(function () use ($contract, $periodStart, $periodEnd, $dueDate) {
            $bill = BillingStatement::create([
                'contract_id' => $contract->id,
                'tenant_id' => $contract->tenant_id,
                'billing_period_start' => $periodStart,
                'billing_period_end' => $periodEnd,
                'due_date' => $dueDate,
                'base_rent' => $contract->monthly_rate,
                'penalty_amount' => 0,
                'total_amount' => $contract->monthly_rate,
                'status' => 'unpaid',
            ]);

            $this->foldPenaltiesInto($bill);

            return $bill->fresh();
        });
    }

    /**
     * Attaches the tenant's active, not-yet-billed penalties to this statement
     * and recalculates its totals. Returns how many were attached.
     */
    private function foldPenaltiesInto(BillingStatement $bill): int
    {
        $penalties = Penalty::where('tenant_id', $bill->tenant_id)
            ->where('status', 'active')
            ->whereNull('billing_id')
            ->get();

        if ($penalties->isEmpty()) {
            return 0;
        }

        Penalty::whereIn('id', $penalties->pluck('id'))->update(['billing_id' => $bill->id]);

        $penaltyTotal = Penalty::where('billing_id', $bill->id)
            ->where('status', 'active')
            ->sum('amount');

        $bill->update([
            'penalty_amount' => $penaltyTotal,
            'total_amount' => $bill->base_rent + $penaltyTotal,
        ]);

        return $penalties->count();
    }

    /**
     * Adds a computed `amount_paid` / `balance` without storing them — always
     * derived fresh from the actual payment records.
     */
    private function withBalance(BillingStatement $bill): BillingStatement
    {
        $paid = $bill->payments->sum('amount_paid');
        $bill->amount_paid = round($paid, 2);
        $bill->balance = round($bill->total_amount - $paid, 2);

        return $bill;
    }
}