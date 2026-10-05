<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BillingStatement;
use App\Models\DormitoryProfile;
use App\Models\LeaseContract;
use Carbon\Carbon;
use App\Models\Penalty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class BillingController extends Controller
{
    // The due day, grace period and late penalty used to be a constant here
    // (5 days). They now come from the dorm's rental policy in the
    // Dormitory Profile, matching the signed Payments and Fees Schedule.

    /**
     * Admin: list billing statements, newest first. Optional filters.
     */
    public function index(Request $request): JsonResponse
    {
        // Table 22: catches any statement that's gone overdue with zero
        // payment activity, which nothing else here would ever touch on its
        // own. Same call PaymentController::page() makes -- kept in sync so
        // "overdue" means the same thing everywhere it's displayed.
        BillingStatement::syncOverdueStatuses();

        $request->validate([
            'tenant_id' => ['nullable', 'integer', 'exists:tenants,id'],
            'status' => ['nullable', Rule::in(['unpaid', 'partial', 'paid', 'overdue'])],
        ]);

        $query = BillingStatement::with([
            'tenant:id,first_name,last_name,email,contact_number',
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
        BillingStatement::syncOverdueStatuses();

        $billingStatement->load([
            'tenant',
            'contract.bed.room.floor',
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
     * tenant onto it, without generating a new period.
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
     * Matches Use Case Report Table 18 ("Generate Billing Statement") and
     * the dorm's Payments and Fees Schedule:
     *   - Bills follow CALENDAR months, and rent is due on the dorm's rent
     *     due day (the 1st) -- or, if the dorm bills "based on tenant start
     *     date", on the tenant's start day each month (startDatePeriod()). The grace period and late penalty are handled
     *     later by BillingStatement::syncOverdueStatuses().
     *   - The advance rent paid with the move-in fee covers the month the
     *     tenant moves in (Agreement 4.3), so the first monthly bill is for
     *     the NEXT month. If the dorm prorates a mid-month move-in, the
     *     unused days of that advance are credited on the first bill.
     *   - Utilities/WiFi shares are added unless the dorm marked them as
     *     included in the rent.
     *   - Any unbilled penalties are folded in.
     *
     * Only bills a period once it has actually begun, so future months
     * aren't billed early, and never past the contract's end date.
     */
    private function generateForContract(LeaseContract $contract): ?BillingStatement
    {
        $profile = DormitoryProfile::current();

        $lastBill = BillingStatement::where('contract_id', $contract->id)
            ->where('type', 'monthly')
            ->orderByDesc('billing_period_end')
            ->first();

        $hasMoveInBill = BillingStatement::where('contract_id', $contract->id)
            ->where('type', 'move_in')
            ->exists();

        $start = $contract->start_date->copy()->startOfDay();

        if ($profile->rent_due_basis === 'start_date') {
            // Due on the same day of the month as the tenant's start date
            // (start Oct 15 -> due Nov 15, Dec 15...). No proration needed.
            $period = $this->startDatePeriod($contract, $start, $lastBill, $hasMoveInBill);
            if (! $period) {
                return null;
            }
            [$periodStart, $periodEnd, $baseRent] = $period;
            $dueDate = $periodStart->copy();
        } else {
            if ($lastBill) {
                $periodStart = $lastBill->billing_period_end->copy()->addDay();
            } elseif ($hasMoveInBill) {
                // The advance rent already paid for the move-in month.
                $periodStart = $start->copy()->addMonthNoOverflow()->startOfMonth();
            } else {
                // Walk-in contracts with no move-in fee: bill from the start date.
                $periodStart = $start->copy();
            }

            if ($periodStart->isAfter(now())) {
                return null;
            }

            if ($contract->end_date && $periodStart->isAfter($contract->end_date)) {
                return null;
            }

            // Calendar month, cut short by the contract's end date if it falls
            // mid-month. A period that doesn't cover the whole month (an old
            // anniversary-style period, a walk-in starting mid-month, or a
            // mid-month end date) is charged for the days it covers.
            $periodEnd = $periodStart->copy()->endOfMonth()->startOfDay();
            if ($contract->end_date && $contract->end_date->lt($periodEnd)) {
                $periodEnd = $contract->end_date->copy()->startOfDay();
            }

            $rate = (float) $contract->monthly_rate;
            $baseRent = $this->rentForPeriod($rate, $periodStart, $periodEnd, $profile, ! $lastBill && ! $hasMoveInBill);

            // Prorated mid-month move-in: the advance covered a whole month but
            // the tenant only used part of the move-in month, so the unused days
            // come off the first monthly bill.
            if (! $lastBill && $hasMoveInBill && $profile->mid_month_move_in === 'prorated' && $start->day > 1) {
                $daysInMonth = $start->daysInMonth;
                $unusedDays = $start->day - 1;
                $baseRent = max(0, round($baseRent - $rate * $unusedDays / $daysInMonth, 2));
            }

            $dueDate = $periodStart->copy()->day(min($profile->rent_due_day, $periodStart->daysInMonth));
            if ($dueDate->lt($periodStart)) {
                $dueDate = $periodStart->copy();
            }
        }

        [$utilitiesShare, $wifiShare] = $this->splitUtilityCost($contract);
        if ($profile->water_included && $profile->electricity_included) {
            $utilitiesShare = 0;
        }
        if ($profile->wifi_included) {
            $wifiShare = 0;
        }

        $bill = DB::transaction(function () use ($contract, $periodStart, $periodEnd, $dueDate, $baseRent, $utilitiesShare, $wifiShare) {
            $bill = BillingStatement::create([
                'contract_id' => $contract->id,
                'tenant_id' => $contract->tenant_id,
                'type' => 'monthly',
                'billing_period_start' => $periodStart,
                'billing_period_end' => $periodEnd,
                'due_date' => $dueDate,
                'base_rent' => $baseRent,
                'utilities_amount' => $utilitiesShare,
                'wifi_amount' => $wifiShare,
                'penalty_amount' => 0,
                'total_amount' => $baseRent + $utilitiesShare + $wifiShare,
                'status' => 'unpaid',
            ]);

            $this->foldPenaltiesInto($bill);

            return $bill->fresh();
        });

        // Step 8 of Table 18: "Send Billing Notification to Tenant" via
        // portal + SMS. No SMS gateway is configured anywhere in this
        // project yet -- same stub pattern already used for application and
        // inquiry notifications -- so this keeps a durable record of the
        // event rather than a promise the code doesn't keep. Swap for a
        // real Mail/SMS send once that's built.
        $this->notify('billing.statement_generated', [
            'billing_id' => $bill->id,
            'tenant_id' => $bill->tenant_id,
            'contract_id' => $contract->id,
            'total_amount' => $bill->total_amount,
            'due_date' => $bill->due_date?->toDateString(),
        ]);

        // Tenant notification panel (v39): the "portal" half of the above.
        \App\Models\TenantNotification::send($bill->tenant_id, 'bill_new',
            'Your bill for ' . $bill->billing_period_start->format('F Y') . ' is ready',
            'Total: ₱' . number_format((float) $bill->total_amount, 2) . ', due on ' . $bill->due_date?->format('F j, Y') . '.',
            '/billing');

        return $bill;
    }

    /**
     * "Based on tenant start date" billing: each period runs from the start
     * day of one month to the day before it in the next (start Oct 16 ->
     * Oct 16-Nov 15, then Nov 16-Dec 15). The advance rent in the move-in
     * fee covers the first full period, so the first monthly bill starts
     * one month after the start date. A period cut short (contract ending
     * early, or switching over from calendar-month bills) is charged by
     * the day. Returns [start, end, rent], or null if nothing is due yet.
     */
    private function startDatePeriod(LeaseContract $contract, Carbon $start, ?BillingStatement $lastBill, bool $hasMoveInBill): ?array
    {
        if ($lastBill) {
            $from = $lastBill->billing_period_end->copy()->addDay()->startOfDay();
        } elseif ($hasMoveInBill) {
            $from = $start->copy()->addMonthNoOverflow();
        } else {
            $from = $start->copy();
        }

        if ($from->isAfter(now())) {
            return null;
        }
        if ($contract->end_date && $from->isAfter($contract->end_date)) {
            return null;
        }

        // Find the start-day "anchors" on either side of $from.
        $n = 0;
        while ($start->copy()->addMonthsNoOverflow($n + 1)->lte($from)) {
            $n++;
        }
        $prevAnchor = $start->copy()->addMonthsNoOverflow($n);
        $nextAnchor = $start->copy()->addMonthsNoOverflow($n + 1);

        $to = $nextAnchor->copy()->subDay();
        if ($contract->end_date && $contract->end_date->lt($to)) {
            $to = $contract->end_date->copy()->startOfDay();
        }

        $rate = (float) $contract->monthly_rate;
        $periodDays = (int) round(abs($prevAnchor->diffInDays($nextAnchor)));
        $days = (int) round(abs($from->diffInDays($to))) + 1;
        $rent = $days >= $periodDays ? $rate : round($rate * $days / $periodDays, 2);

        return [$from, $to, $rent];
    }

    /**
     * Rent for one billing period. A full calendar month is the monthly
     * rate. A shorter period (contract ending mid-month, an old
     * anniversary-style period being moved onto calendar months) is charged
     * by the day. The very first bill of a walk-in contract that starts
     * mid-month is charged in full unless the dorm prorates move-ins.
     */
    private function rentForPeriod(float $rate, Carbon $from, Carbon $to, DormitoryProfile $profile, bool $isWalkInFirstBill): float
    {
        $daysInMonth = $from->daysInMonth;
        $days = $from->diffInDays($to) + 1;

        if ($days >= $daysInMonth) {
            return $rate;
        }

        $endsOnMonthEnd = $to->isSameDay($from->copy()->endOfMonth());
        if ($isWalkInFirstBill && $endsOnMonthEnd && $profile->mid_month_move_in !== 'prorated') {
            return $rate;
        }

        return round($rate * $days / $daysInMonth, 2);
    }

    /**
     * Splits this contract's room's monthly utility/wifi cost evenly across
     * its beds (same as rent), at the room's
     * prices as of right now. Bills already generated keep the amounts they
     * were issued with, so a mid-month price change only reaches each
     * tenant's next generated statement.
     */
    private function splitUtilityCost(LeaseContract $contract): array
    {
        $room = $contract->bed?->room;

        if (! $room) {
            // Exception in Table 18: "Utility charge data is incomplete or
            // missing for the billing period; system flags the discrepancy
            // and notifies the administrator." Doesn't block generation --
            // the statement still goes out with a 0 utility share -- this
            // just makes sure the gap is visible instead of silently
            // zeroing it out with no record.
            $this->notify('billing.utility_data_missing', [
                'contract_id' => $contract->id,
                'tenant_id' => $contract->tenant_id,
                'reason' => "No room is assigned to this contract's bed.",
            ]);

            return [0, 0];
        }

        return $room->utilityShares();
    }

    /**
     * Notification stub -- same pattern as ApplicationController /
     * PaymentController. The hook exists and logs a durable record; real
     * delivery (Mail/SMS) isn't wired up yet.
     */
    private function notify(string $event, array $payload): void
    {
        Log::info("[notification stub] {$event}", $payload);
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
            'total_amount' => $bill->base_rent + $bill->utilities_amount + $bill->wifi_amount + $penaltyTotal,
        ]);

        return $penalties->count();
    }

    /**
     * FIXED: previously summed every payment regardless of status, meaning a
     * tenant with a payment proof still awaiting admin review -- or even one
     * that was rejected -- would show as partially or fully paid here before
     * anyone actually approved anything. Now only counts approved payments,
     * matching how PaymentController computes balance everywhere else.
     */
    private function withBalance(BillingStatement $bill): BillingStatement
    {
        $paid = $bill->payments->where('status', 'approved')->sum('amount_paid');
        $bill->amount_paid = round($paid, 2);
        $bill->balance = round($bill->total_amount - $paid, 2);

        return $bill;
    }
}