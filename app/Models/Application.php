<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'inquiry_id',
        'tenant_id',
        'full_name',
        'contact_number',
        'email',
        'emergency_contact_name',
        'emergency_contact_number',
        'bed_id',
        'preferred_start_date',
        'dpa_consent',
        'status',
        'created_by',
        'approved_by',
    ];

    protected $casts = [
        'preferred_start_date' => 'date',
        'dpa_consent' => 'boolean',
    ];

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function leaseContract(): HasOne
    {
        return $this->hasOne(LeaseContract::class);
    }
}
