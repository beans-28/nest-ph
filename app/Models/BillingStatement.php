<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\EscalationLog;

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
        'base_rent',
        'utilities_amount',
        'wifi_amount',
        'penalty_amount',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'due_date' => 'date',
        'base_rent' => 'decimal:2',
        'utilities_amount' => 'decimal:2',
        'wifi_amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

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

    public function escalationLogs(): HasMany
    {
        return $this->hasMany(EscalationLog::class, 'billing_id');
    }

    /**
     * Use Case Report Table 23, steps 1–3: the system should flag an account
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
        static::whereIn('status', ['unpaid', 'partial'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->startOfDay())
            ->update(['status' => 'overdue']);
    }
}