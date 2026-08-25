<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    public $timestamps = false; // table only has created_at, no updated_at

    protected $fillable = [
        'billing_id',
        'tenant_id',
        'amount_paid',
        'payment_method',
        'reference_number',
        'payment_date',
        'status',
        'proof_path',
        'review_notes',
        'reviewed_by',
        'reviewed_at',
        'recorded_by',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'payment_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function billingStatement(): BelongsTo
    {
        return $this->belongsTo(BillingStatement::class, 'billing_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}