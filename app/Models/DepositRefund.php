<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A security deposit given back to a tenant at move-out (v39). */
class DepositRefund extends Model
{
    protected $fillable = [
        'tenant_id', 'deposit_amount', 'deductions_amount', 'deductions_note',
        'refund_amount', 'refund_method', 'reference_number', 'refunded_at', 'recorded_by',
    ];

    protected $casts = [
        'deposit_amount' => 'decimal:2',
        'deductions_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'refunded_at' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * The deposit a tenant paid: half of their PAID move-in fee
     * (1 month deposit + 1 month advance, see ApplicationController).
     * 0 when there's no paid move-in fee (e.g. walk-in tenants).
     */
    public static function depositHeldFor(Tenant $tenant): float
    {
        $moveIn = BillingStatement::where('tenant_id', $tenant->id)
            ->where('type', 'move_in')
            ->where('status', 'paid')
            ->latest('id')
            ->first();

        return $moveIn ? round((float) $moveIn->base_rent / 2, 2) : 0.0;
    }
}
