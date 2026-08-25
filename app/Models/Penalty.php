<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penalty extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'damage_id',
        'billing_id',
        'type',
        'description',
        'amount',
        'status',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

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
