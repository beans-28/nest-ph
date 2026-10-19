<?php

namespace App\Services;

use App\Models\BillingStatement;
use App\Models\DormitoryProfile;
use App\Models\LeaseContract;
use Carbon\Carbon;

/**
 * Paying rent ahead (tool defense revision): a tenant may pay up to
 * MAX_MONTHS of future RENT early. Utilities and Wi-Fi are not prepaid,
 * because they change month to month; they stay on each monthly bill.
 *
 * A pay-ahead is a BillingStatement of type pay_ahead whose billing period
 * is the exact months it covers. Once it's paid, each monthly bill for one
 * of those months has its rent covered (prepaid_rent) and only the rest is
 * due. Unpaid, it is never a balance due, never overdue, and never counts
 * toward delinquency (BillingStatement::scopeOwed()).
 */
class PayAhead
{
    public const MAX_MONTHS = 3;

    /**
     * The next full rental periods the tenant can still pay ahead, up to
     * MAX_MONTHS: after the last monthly bill, the move-in advance and any
     * earlier pay-ahead, and never past the contract's end date.
     *
     * @return array<int, array{0: Carbon, 1: Carbon}>
     */
    public static function upcomingPeriods(LeaseContract $contract): array
    {
        $byStartDate = DormitoryProfile::current()->rent_due_basis === 'start_date';
        $start = $contract->start_date->copy()->startOfDay();

        $lastMonthly = BillingStatement::where('contract_id', $contract->id)->where('type', 'monthly')
            ->max('billing_period_end');
        $lastPayAhead = BillingStatement::where('contract_id', $contract->id)->where('type', BillingStatement::PAY_AHEAD)
            ->max('billing_period_end');
        $lastCovered = collect([$lastMonthly, $lastPayAhead])->filter()->map(fn ($d) => Carbon::parse($d))->max();

        if ($lastCovered) {
            $from = $lastCovered->copy()->addDay()->startOfDay();
        } else {
            // Nothing billed yet: start after the move-in advance.
            $moveIn = BillingStatement::where('contract_id', $contract->id)->where('type', 'move_in')->latest('id')->first();
            $advance = $moveIn?->setRelation('contract', $contract)->advanceMonths() ?? 0;
            $from = $byStartDate || $advance === 0
                ? $start->copy()->addMonthsNoOverflow($advance)
                : $start->copy()->addMonthsNoOverflow($advance)->startOfMonth();
        }

        $periods = [];
        while (count($periods) < self::MAX_MONTHS) {
            if ($byStartDate) {
                // Periods run from the start day of one month to the day
                // before it in the next (start Oct 16: Nov 16 - Dec 15).
                $n = 0;
                while ($start->copy()->addMonthsNoOverflow($n)->lt($from)) {
                    $n++;
                }
                $periodStart = $start->copy()->addMonthsNoOverflow($n);
                $periodEnd = $start->copy()->addMonthsNoOverflow($n + 1)->subDay();
            } else {
                // Calendar months. A part month (e.g. a mid-month move-in
                // with no advance) is billed normally, not paid ahead.
                $periodStart = $from->day === 1 ? $from->copy() : $from->copy()->addMonthNoOverflow()->startOfMonth();
                $periodEnd = $periodStart->copy()->endOfMonth()->startOfDay();
            }

            if ($contract->end_date && $periodEnd->gt($contract->end_date)) {
                break;
            }

            $periods[] = [$periodStart, $periodEnd];
            $from = $periodEnd->copy()->addDay();
        }

        return $periods;
    }

    /**
     * How much of a monthly bill's rent is already paid ahead: the unused
     * part of any PAID pay-ahead whose months include this bill's period.
     * $exceptBillId leaves out a bill that is being re-checked.
     */
    public static function coverFor(LeaseContract $contract, Carbon $periodStart, float $rent, ?int $exceptBillId = null): float
    {
        $payAheads = BillingStatement::where('contract_id', $contract->id)
            ->where('type', BillingStatement::PAY_AHEAD)
            ->where('status', 'paid')
            ->whereDate('billing_period_start', '<=', $periodStart)
            ->whereDate('billing_period_end', '>=', $periodStart)
            ->get();

        $available = $payAheads->sum(function (BillingStatement $payAhead) use ($contract, $exceptBillId) {
            $used = BillingStatement::where('contract_id', $contract->id)
                ->where('type', 'monthly')
                ->when($exceptBillId, fn ($q) => $q->where('id', '!=', $exceptBillId))
                ->whereDate('billing_period_start', '>=', $payAhead->billing_period_start)
                ->whereDate('billing_period_start', '<=', $payAhead->billing_period_end)
                ->sum('prepaid_rent');

            return max(0, (float) $payAhead->total_amount - (float) $used);
        });

        return round(min($available, $rent), 2);
    }

    /**
     * Paid-ahead rent not used on any bill yet (shown to the tenant, and to
     * the admin at move-out so any leftover is refunded with the deposit).
     */
    public static function unusedCredit(LeaseContract $contract): float
    {
        $paid = (float) BillingStatement::where('contract_id', $contract->id)
            ->where('type', BillingStatement::PAY_AHEAD)->where('status', 'paid')->sum('total_amount');
        $used = (float) BillingStatement::where('contract_id', $contract->id)
            ->where('type', 'monthly')->sum('prepaid_rent');

        return max(0, round($paid - $used, 2));
    }

    /**
     * Once a pay-ahead is paid, a monthly bill for one of its months may
     * already exist (bills are issued 10 days early). If nothing has been
     * paid on it yet, cover its rent now.
     */
    public static function applyToIssuedBills(BillingStatement $payAhead): void
    {
        if ($payAhead->type !== BillingStatement::PAY_AHEAD || $payAhead->status !== 'paid') {
            return;
        }

        $contract = $payAhead->contract;
        $bills = BillingStatement::where('contract_id', $payAhead->contract_id)
            ->where('type', 'monthly')
            ->where('prepaid_rent', 0)
            ->where('status', 'unpaid')
            ->whereDate('billing_period_start', '>=', $payAhead->billing_period_start)
            ->whereDate('billing_period_start', '<=', $payAhead->billing_period_end)
            ->whereDoesntHave('payments', fn ($q) => $q->whereIn('status', ['approved', 'pending']))
            ->get();

        foreach ($bills as $bill) {
            $cover = self::coverFor($contract, $bill->billing_period_start, (float) $bill->base_rent, $bill->id);
            if ($cover <= 0) {
                continue;
            }

            $bill->update([
                'base_rent' => round((float) $bill->base_rent - $cover, 2),
                'prepaid_rent' => $cover,
            ]);
            $bill->recalculateTotals();

            if ((float) $bill->fresh()->total_amount <= 0) {
                $bill->update(['status' => 'paid']);
            }
        }
    }

    /**
     * Everything the Billing page needs: an unfinished pay-ahead to pay or
     * cancel, the months that can be paid ahead, or why it isn't possible.
     */
    public static function optionsFor(\App\Models\Tenant $tenant): array
    {
        $contract = $tenant->activeContract()->first();
        $open = BillingStatement::where('tenant_id', $tenant->id)
            ->where('type', BillingStatement::PAY_AHEAD)
            ->whereIn('status', ['unpaid', 'partial'])
            ->with('payments')
            ->latest('id')
            ->first();

        $blocked = null;
        $periods = [];
        if ($tenant->status !== 'active' || ! $contract) {
            $blocked = 'You need an active contract to pay ahead.';
        } elseif (! $open && BillingStatement::where('tenant_id', $tenant->id)->owed()->exists()) {
            $blocked = 'Please settle your current bill first. You can pay ahead once nothing is due.';
        } elseif (! $open) {
            $periods = self::upcomingPeriods($contract);
            if (! $periods) {
                $blocked = 'Your contract has no full months left to pay ahead.';
            }
        }

        return [
            'contract' => $contract,
            'rate' => (float) ($contract?->monthly_rate ?? 0),
            'open' => $open,
            'periods' => $periods,
            'blocked' => $blocked,
            'unused_credit' => $contract ? self::unusedCredit($contract) : 0.0,
        ];
    }

    /** "Nov 2026" or "Nov 2026 – Jan 2027" for calendar months; exact dates otherwise. */
    public static function label(Carbon $from, ?Carbon $to): string
    {
        $to ??= $from;
        $calendar = $from->day === 1 && $to->isSameDay($to->copy()->endOfMonth());
        if ($calendar) {
            return $from->isSameMonth($to) ? $from->format('M Y') : $from->format('M Y') . ' – ' . $to->format('M Y');
        }

        return $from->format('M j, Y') . ' – ' . $to->format('M j, Y');
    }
}
