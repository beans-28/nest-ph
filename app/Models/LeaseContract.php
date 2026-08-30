<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaseContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'tenant_id',
        'bed_id',
        'inquiry_id',
        'start_date',
        'end_date',
        'monthly_rate',
        'discount_amount',
        'esign_status',
        'signed_document_url',
        'signed_at',
        'status',
        'termination_reason',
        'terminated_at',
        'last_renewed_at',
        'last_renewed_by',
        'created_by',
        'approved_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'monthly_rate' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'signed_at' => 'datetime',
        'terminated_at' => 'datetime',
        'last_renewed_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class);
    }

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function billingStatements(): HasMany
    {
        return $this->hasMany(BillingStatement::class, 'contract_id');
    }
}