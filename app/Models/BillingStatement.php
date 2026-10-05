<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\EscalationLog;
use Illuminate\Support\Facades\DB;

class BillingStatement extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'tenant_id',
        'type',
        'billing_period_start',
        'billing_period_end',
        'due_date',
        'grace_period_days',
        'late_penalty_percent',
        'base_rent',
        'advance_amount',
        'deposit_amount',
        'utilities_amount',
        'wifi_amount',
        'penalty_amount',
        'total_amount',
        'status',
        'reservation_expired_at',
    ];

    protected $casts = [
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'due_date' => 'date',
        'reservation_expired_at' => 'datetime',
        'grace_period_days' => 'integer',
        'late_penalty_percent' => 'float',
        'base_rent' => 'decimal:2',
        'advance_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'utilities_amount' => 'decimal:2',
        'wifi_amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Every new bill keeps a copy of the grace period and late penalty %
     * in effect when it was created. Changing the settings later only
     * affects bills created after the change, never older ones.
     *
     * A move-in fee is 1 month advance rent + 1 month security deposit; if
     * the caller didn't split it, split it evenly here.
     */
    protected static function booted(): void
    {
        static::creating(function (BillingStatement $bill) {
            if ($bill->grace_period_days === null || $bill->late_penalty_percent === null) {
                $profile = DormitoryProfile::current();
                $bill->grace_period_days ??= $profile->grace_period_days;
                $bill->late_penalty_percent ??= $profile->late_penalty_percent;
            }

            if ($bill->type === 'move_in' && $bill->deposit_amount === null) {
                $bill->advance_amount ??= round((float) $bill->base_rent / 2, 2);
                $bill->deposit_amount = round((float) $bill->base_rent - (float) $bill->advance_amount, 2);
            }
        });
    }

    /** Grace period for this bill: its own copy, or today's setting for very old rows. */
    public function graceDays(?DormitoryProfile $profile = null): int
    {
        return $this->grace_period_days ?? ($profile ?? DormitoryProfile::current())->grace_period_days;
    }

    /** Late penalty % for this bill: its own copy, or today's setting for very old rows. */
    public function penaltyPercent(?DormitoryProfile $profile = null): float
    {
        return (float) ($this->late_penalty_percent ?? ($profile ?? DormitoryProfile::current())->late_penalty_percent);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(LeaseContract::class, 'contract_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'billing_id');
    }

    /**
     * What's still owed on this bill: total minus APPROVED payments (pending
     * proofs don't count yet). Never below zero. Uses a preloaded
     * `approved_paid` sum when present (see withApprovedPaid()), otherwise
     * queries it.
     */
    public function remainingBalance(): float
    {
        $paid = $this->approved_paid ?? $this->payments()->where('status', 'approved')->sum('amount_paid');

        return max(0, round((float) $this->total_amount - (float) $paid, 2));
    }

    /**
     * Move-in "Partial Payment" is a fixed half of the move-in fee. Once the
     * first half is approved, what's left is the other half, so this simply
     * caps at the remaining balance.
     */
    public function moveInHalfAmount(): float
    {
        return min($this->remainingBalance(), round((float) $this->total_amount / 2, 2));
    }

    /**
     * Payments and Fees Schedule 3.2: a reservation is valid for one month
     * from the date of payment. Counted from the first approved payment on
     * a half-paid move-in fee; null if nothing has been approved yet.
     */
    public function reservationDeadline(): ?\Carbon\Carbon
    {
        if ($this->type !== 'move_in') {
            return null;
        }

        $firstPaid = $this->payments()->where('status', 'approved')->min('payment_date');

        return $firstPaid
            ? \Carbon\Carbon::parse($firstPaid)->startOfDay()->addMonthsNoOverflow(\App\Console\Commands\ExpireMoveInReservations::MONTHS)->endOfDay()
            : null;
    }

    /** True when the remaining balance is more than half, i.e. paying half still leaves something for later. */
    public function canPayMoveInHalf(): bool
    {
        return $this->moveInHalfAmount() < $this->remainingBalance();
    }

    /** Query scope: preload the approved-payments sum used by remainingBalance(). */
    public function scopeWithApprovedPaid($query)
    {
        return $query->withSum(['payments as approved_paid' => fn ($q) => $q->where('status', 'approved')], 'amount_paid');
    }

    public function escalationLogs(): HasMany
    {
        return $this->hasMany(EscalationLog::class, 'billing_id');
    }

    /**
     * Use Case Report Table 22, steps 1–3: the system should flag an account
     * as "Payment Overdue" once the due date passes without payment.
     *
     * Before this existed, a statement only ever moved to 'overdue'
     * reactively — inside PaymentController::resyncStatementStatus(),
     * triggered whenever a payment happened to be recorded against it. A
     * statement with zero payment activity past its due date stayed labeled
     * 'unpaid' indefinitely, which would silently under-count "Overdue
     * Accounts" on the admin dashboard.
     *
     * Same limitation as LeaseContractController::syncExpiringAndExpired():
     * this runs whenever a page happens to load it, as a stand-in for a real
     * scheduled job — not a true cron. Call this from the top of any
     * controller method that lists or displays billing statements
     * (PaymentController::page() does; BillingController::index()/show()
     * should too, for full consistency across every place statuses are read).
     */
    public static function syncOverdueStatuses(): void
    {
        $profile = DormitoryProfile::current();

        // Payments and Fees Schedule 5.1: a bill only becomes overdue once
        // the grace period after its due date has passed (due on the 1st,
        // grace until the 4th, overdue from the 5th).
        //
        // Move-in fee bills never go overdue: the tenant hasn't moved in
        // yet, so the delinquency ladder (SMS, portal lock, blacklist) must
        // not start for them. They stay unpaid/partial until settled.
        //
        // Each bill uses its OWN grace period (the one in effect when it
        // was created), so this checks bill by bill.
        $overdueIds = static::whereIn('status', ['unpaid', 'partial'])
            ->where('type', '!=', 'move_in')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->startOfDay())
            ->get(['id', 'due_date', 'grace_period_days'])
            ->filter(fn (BillingStatement $bill) => $bill->isPastGrace($profile))
            ->pluck('id');

        if ($overdueIds->isNotEmpty()) {
            static::whereIn('id', $overdueIds)->update(['status' => 'overdue']);
        }

        static::applyLatePenalties($profile);
    }

    /**
     * Last day the tenant can pay without a late penalty: the due date plus
     * the grace period (e.g. due Oct 1 + 3 days = Oct 4).
     */
    public function graceDeadline(?DormitoryProfile $profile = null): ?\Carbon\Carbon
    {
        $profile ??= DormitoryProfile::current();

        return $this->due_date?->copy()->addDays($this->graceDays($profile));
    }

    /** True once today is after the grace deadline. */
    public function isPastGrace(?DormitoryProfile $profile = null): bool
    {
        $deadline = $this->graceDeadline($profile);

        return $deadline !== null && now()->startOfDay()->gt($deadline);
    }

    /**
     * Adds the bill up again from its parts. Use after any penalty on it is
     * added, waived or changed.
     */
    public function recalculateTotals(): void
    {
        $penaltyTotal = (float) Penalty::where('billing_id', $this->id)
            ->where('status', 'active')
            ->sum('amount');

        $this->update([
            'penalty_amount' => $penaltyTotal,
            'total_amount' => (float) $this->base_rent + (float) $this->utilities_amount + (float) $this->wifi_amount + $penaltyTotal,
        ]);
    }

    /**
     * How much the tenant paid ON TIME toward this bill. Payments and Fees
     * Schedule, Section 2: "the date you actually paid, as shown on your
     * proof, is used to decide whether the payment was on time" -- so this
     * goes by payment_date, not by when the admin approved it.
     */
    public function paidOnTime(array $statuses = ['approved'], ?DormitoryProfile $profile = null): float
    {
        $deadline = $this->graceDeadline($profile);
        if (! $deadline) {
            return 0.0;
        }

        return (float) $this->payments()
            ->whereIn('status', $statuses)
            ->whereDate('payment_date', '<=', $deadline)
            ->sum('amount_paid');
    }

    /**
     * The part of this month's RENT still unpaid at the grace deadline.
     * Utilities, WiFi and other penalties are not "monthly rent", so the late %
     * is never charged on them. On-time payments count against the bill's
     * charges first, then whatever is left unpaid is capped at the rent.
     */
    public function overdueRent(?DormitoryProfile $profile = null): float
    {
        $charges = (float) $this->base_rent + (float) $this->utilities_amount + (float) $this->wifi_amount;
        $unpaid = max(0, $charges - $this->paidOnTime(['approved'], $profile));

        return round(min((float) $this->base_rent, $unpaid), 2);
    }

    /**
     * Payments and Fees Schedule 5.2 / Agreement 3.3: if rent is still
     * unpaid after the grace period, add a ONE-TIME penalty (the bill's own
     * late penalty %, e.g. 10%) of the overdue monthly rent. Never compounded and never charged twice on the
     * same bill (a waived one is not re-added either).
     *
     * Waits while the tenant has a proof under review that is dated on
     * time -- if the admin approves it, no penalty is due. If it's rejected,
     * the next run adds the penalty.
     */
    public static function applyLatePenalties(?DormitoryProfile $profile = null): void
    {
        $profile ??= DormitoryProfile::current();

        $bills = static::where('status', 'overdue')
            ->where('type', 'monthly')
            ->whereDoesntHave('penalties', fn ($q) => $q->where('type', 'late_payment'))
            ->get();

        foreach ($bills as $bill) {
            $percent = $bill->penaltyPercent($profile);
            if ($percent <= 0) {
                continue;
            }

            if (! $bill->isPastGrace($profile)) {
                continue;
            }

            if ($bill->paidOnTime(['pending'], $profile) > 0) {
                continue;
            }

            $overdueRent = $bill->overdueRent($profile);
            if ($overdueRent <= 0) {
                continue;
            }

            $amount = round($overdueRent * $percent / 100, 2);
            $percentLabel = rtrim(rtrim(number_format($percent, 2), '0'), '.');

            DB::transaction(function () use ($bill, $amount, $overdueRent, $percentLabel, $profile) {
                $penalty = Penalty::create([
                    'tenant_id' => $bill->tenant_id,
                    'billing_id' => $bill->id,
                    'type' => 'late_payment',
                    'description' => "Late payment penalty: {$percentLabel}% of ₱" . number_format($overdueRent, 2)
                        . ' unpaid rent for ' . $bill->billing_period_start->format('F Y'),
                    'amount' => $amount,
                    'date_incurred' => $bill->graceDeadline($profile)->copy()->addDay()->toDateString(),
                    'status' => 'active',
                    'created_by' => null,
                ]);

                PenaltyAuditLog::create([
                    'penalty_id' => $penalty->id,
                    'action' => 'created',
                    'performed_by' => null,
                    'reason' => 'Added automatically after the grace period ended.',
                    'created_at' => now(),
                ]);

                $bill->recalculateTotals();
            });

            TenantNotification::send($bill->tenant_id, 'late_penalty',
                'A late payment penalty was added',
                'Your ' . $bill->billing_period_start->format('F Y') . ' rent was not paid within the grace period, so a one-time penalty of ₱'
                    . number_format($amount, 2) . ' was added to the bill.',
                '/billing', "late_penalty:{$bill->id}");
        }
    }

    /**
     * Called after a payment is approved or recorded. If the tenant's
     * on-time payments (by payment date) already covered the rent, the late
     * penalty was charged only because the proof was reviewed late, so it
     * is waived automatically, with an audit-log entry saying why.
     */
    public function waiveLatePenaltyIfPaidOnTime(?int $performedBy = null): bool
    {
        $penalty = Penalty::where('billing_id', $this->id)
            ->where('type', 'late_payment')
            ->where('status', 'active')
            ->first();

        if (! $penalty || $this->overdueRent() > 0) {
            return false;
        }

        DB::transaction(function () use ($penalty, $performedBy) {
            $penalty->update(['status' => 'waived']);

            PenaltyAuditLog::create([
                'penalty_id' => $penalty->id,
                'action' => 'waived',
                'performed_by' => $performedBy,
                'reason' => 'Waived automatically: the payment proof shows the rent was paid within the grace period.',
                'created_at' => now(),
            ]);

            $this->recalculateTotals();
        });

        return true;
    }

    public function penalties(): HasMany
    {
        return $this->hasMany(Penalty::class, 'billing_id');
    }
}
