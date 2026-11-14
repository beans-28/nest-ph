<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penalty extends Model
{
    use HasFactory;

    /**
     * damage: from a recorded damage. late_payment: the automatic one-time
     * late fee (Payments and Fees Schedule 5.2). manual/other: added by an
     * admin, usually picked from the dorm's Other Charges list.
     */
    public const TYPES = ['damage', 'manual', 'other', 'late_payment'];

    protected $fillable = [
        'tenant_id',
        'damage_id',
        'billing_id',
        'type',
        'description',
        'amount',
        'deposit_deducted',
        'date_incurred',
        'status',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'deposit_deducted' => 'decimal:2',
        'date_incurred' => 'date',
    ];

    /**
     * Pureza revision (2026-11-14): a penalty is paid from the tenant's
     * security deposit first, as long as some deposit is left. Only the
     * part the deposit can't cover goes on a bill (the normal flow).
     *
     * Runs automatically whenever a penalty is created, its amount is
     * edited, or it is reinstated. A waived penalty keeps its number for
     * the record, but since only ACTIVE penalties count against the
     * deposit, waiving one gives that money back to the deposit.
     */
    protected static function booted(): void
    {
        static::saving(function (Penalty $penalty) {
            $relevant = ! $penalty->exists
                || $penalty->isDirty('amount')
                || ($penalty->isDirty('status') && $penalty->status === 'active');

            if (! $relevant || $penalty->status !== 'active' || ! $penalty->tenant_id) {
                return;
            }

            $tenant = Tenant::find($penalty->tenant_id);
            $available = $tenant ? DepositRefund::depositRemainingFor($tenant, $penalty->id) : 0.0;

            $penalty->deposit_deducted = round(min((float) $penalty->amount, $available), 2);
        });
    }

    /** What's left to charge on a bill after the deposit covered its part. */
    public function chargedAmount(): float
    {
        return max(0, round((float) $this->amount - (float) $this->deposit_deducted, 2));
    }

    /**
     * One sentence for the tenant explaining where this penalty is paid
     * from: the deposit, the next bill, or both.
     */
    public function paymentNote(string $billPhrase = 'It will be included in your next bill.'): string
    {
        $deducted = (float) $this->deposit_deducted;
        if ($deducted <= 0) {
            return $billPhrase;
        }
        $left = number_format(DepositRefund::depositRemainingFor($this->tenant), 2);
        if ($this->chargedAmount() <= 0) {
            return "It was deducted from your security deposit (₱{$left} of your deposit left), so you don't need to pay it separately.";
        }

        return '₱' . number_format($deducted, 2) . ' was deducted from your security deposit, which is now used up. The remaining ₱'
            . number_format($this->chargedAmount(), 2) . ($this->billing_id ? ' was added to the bill.' : ' will be included in your next bill.');
    }

    /** SQL for "amount still to bill", for SUM() queries. */
    public const CHARGED_SQL = '(amount - deposit_deducted)';

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function damage(): BelongsTo
    {
        return $this->belongsTo(Damage::class);
    }

    public function billingStatement(): BelongsTo
    {
        return $this->belongsTo(BillingStatement::class, 'billing_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(PenaltyAuditLog::class);
    }
}