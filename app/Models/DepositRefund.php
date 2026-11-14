<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A security deposit given back to a tenant at move-out (v39). */
class DepositRefund extends Model
{
    protected $fillable = [
        'tenant_id', 'deposit_amount', 'deductions_amount', 'deductions_note',
        'refund_amount', 'forfeited', 'refund_method', 'reference_number', 'refunded_at', 'recorded_by',
    ];

    protected $casts = [
        'deposit_amount' => 'decimal:2',
        'deductions_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'forfeited' => 'boolean',
        'refunded_at' => 'date',
    ];

    /**
     * Pureza rule (2026-11-14): the deposit is non-refundable when the tenant
     * stayed less than the set months (6), and refundable, minus deductions,
     * after that. Stay = first lease start → today (or last lease end if
     * earlier). Returns months stayed, required months and whether it's refundable.
     */
    public static function eligibilityFor(Tenant $tenant): array
    {
        $required = (int) (DormitoryProfile::current()->deposit_refundable_after_months ?? 6);
        $first = $tenant->contracts()->whereNotNull('start_date')->orderBy('start_date')->first();
        $last = $tenant->contracts()->latest('id')->first();

        $months = 0;
        if ($first) {
            $end = now()->startOfDay();
            if ($tenant->status === 'inactive' && $last?->end_date && $last->end_date->lt($end)) {
                $end = $last->end_date;
            }
            $months = (int) $first->start_date->diffInMonths($end);
        }

        return [
            'months_stayed' => $months,
            'required_months' => $required,
            'refundable' => $required === 0 || $months >= $required,
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * The deposit a tenant paid: the deposit part of their PAID move-in fee
     * (advance rent + security deposit, see ApplicationController).
     * 0 when there's no paid move-in fee (e.g. walk-in tenants).
     */
    public static function depositHeldFor(Tenant $tenant): float
    {
        $moveIn = BillingStatement::where('tenant_id', $tenant->id)
            ->where('type', 'move_in')
            ->where('status', 'paid')
            ->latest('id')
            ->first();

        if (! $moveIn) {
            return 0.0;
        }

        return self::depositOf($moveIn);
    }

    /**
     * Deposit already used to pay penalties (Pureza revision 2026-11-14).
     * Only active penalties count, so a waived one gives its share back.
     */
    public static function usedForPenalties(Tenant $tenant, ?int $exceptPenaltyId = null): float
    {
        return round((float) Penalty::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->when($exceptPenaltyId, fn ($q) => $q->where('id', '!=', $exceptPenaltyId))
            ->sum('deposit_deducted'), 2);
    }

    /**
     * Deposit still available to cover new penalties: the deposit paid
     * minus what penalties already used. 0 once the deposit has been
     * refunded/settled at move-out, or if no deposit was ever paid.
     */
    public static function depositRemainingFor(Tenant $tenant, ?int $exceptPenaltyId = null): float
    {
        if (self::where('tenant_id', $tenant->id)->exists()) {
            return 0.0;
        }

        return max(0, round(self::depositHeldFor($tenant) - self::usedForPenalties($tenant, $exceptPenaltyId), 2));
    }

    /** The deposit part of a move-in fee (rows saved without the split are the old 1 + 1 month fee). */
    private static function depositOf(BillingStatement $moveIn): float
    {
        return round((float) ($moveIn->deposit_amount ?? $moveIn->base_rent / 2), 2);
    }

    /**
     * The dates a move-in fee's advance rent pays for, e.g. "Oct 16 – Nov 30, 2026"
     * for 2 months starting Oct 16 on calendar-month billing. Null with no advance.
     */
    public static function advanceCoverage(BillingStatement $moveIn): ?string
    {
        $months = $moveIn->advanceMonths();
        $start = $moveIn->contract?->start_date;
        if ($months < 1 || ! $start) {
            return null;
        }

        $end = $start->copy()->addMonthsNoOverflow($months)->subDay();
        if (DormitoryProfile::current()->rent_due_basis !== 'start_date') {
            // Calendar-month billing: the advance runs to the end of the month.
            $end = $start->copy()->addMonthsNoOverflow($months - 1)->endOfMonth();
        }

        return $start->format($start->year === $end->year ? 'M j' : 'M j, Y') . ' – ' . $end->format('M j, Y');
    }

    /**
     * What the tenant dashboard shows about the move-in fee: the advance
     * rent (what it paid for) and the security deposit (held, refunded, or
     * used up by deductions). Null for tenants with no move-in fee.
     */
    public static function summaryFor(Tenant $tenant): ?array
    {
        $moveIn = BillingStatement::where('tenant_id', $tenant->id)
            ->where('type', 'move_in')
            ->with(['contract', 'payments' => fn ($q) => $q->where('status', 'approved')->latest('payment_date')])
            ->latest('id')
            ->first();

        if (! $moveIn) {
            return null;
        }

        $paid = $moveIn->status === 'paid';
        $refund = self::where('tenant_id', $tenant->id)->latest('id')->first();
        $lastPayment = $moveIn->payments->first();

        // Prorated mid-month move-in: the days before the move-in date come
        // off the first monthly bill (see BillingController).
        $start = $moveIn->contract?->start_date;
        $profile = DormitoryProfile::current();
        $credit = 0.0;
        if ($start && $start->day > 1 && $moveIn->advanceMonths() > 0
            && $profile->rent_due_basis !== 'start_date' && $profile->mid_month_move_in === 'prorated') {
            $credit = round((float) $moveIn->contract->monthly_rate * ($start->day - 1) / $start->daysInMonth, 2);
        }

        if (! $paid) {
            $depositStatus = 'awaiting';
        } elseif (! $refund) {
            $depositStatus = 'held';
        } else {
            $depositStatus = (float) $refund->refund_amount > 0 ? 'refunded' : 'fully_deducted';
        }

        return [
            'paid' => $paid,
            'paid_on' => $lastPayment?->payment_date?->format('M j, Y'),
            'reference' => $lastPayment?->reference_number,
            'advance_amount' => (float) ($moveIn->advance_amount ?? $moveIn->base_rent / 2),
            'advance_months' => $moveIn->advanceMonths(),
            'advance_covers' => self::advanceCoverage($moveIn),
            'prorated_credit' => $credit,
            'pay_ahead_credit' => $moveIn->contract ? \App\Services\PayAhead::unusedCredit($moveIn->contract) : 0.0,
            'deposit_amount' => self::depositOf($moveIn),
            // Penalties paid from the deposit during the stay (2026-11-14).
            'used_for_penalties' => $paid ? self::usedForPenalties($tenant) : 0.0,
            'deposit_status' => $depositStatus,
            'refund' => $refund ? [
                'deductions_amount' => (float) $refund->deductions_amount,
                'deductions_note' => $refund->deductions_note,
                'refund_amount' => (float) $refund->refund_amount,
                'refund_method' => $refund->refund_method,
                'refunded_at' => $refund->refunded_at->format('M j, Y'),
            ] : null,
            // Deductions can't exceed the deposit, so anything more stays
            // on the tenant's unpaid bills.
            'still_owed' => $refund ? round((float) BillingStatement::where('tenant_id', $tenant->id)
                ->owed()->withApprovedPaid()->get()
                ->sum(fn ($b) => $b->remainingBalance()), 2) : 0.0,
        ];
    }
}
