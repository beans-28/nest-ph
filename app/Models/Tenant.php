<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'contact_number',
        'email',
        'emergency_contact_name',
        'emergency_contact_number',
        'is_blacklisted',
        'status',
        'portal_restricted',
        'escalation_paused',
        'date_of_birth',
        'home_address',
        'tenant_type',
        'id_document_path',
        'signed_contract_path',
        'deactivation_reason',
        'deactivated_at',
    ];

    protected $casts = [
        'is_blacklisted' => 'boolean',
        'portal_restricted' => 'boolean',
        'escalation_paused' => 'boolean',
        'date_of_birth' => 'date',
        'deactivated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(LeaseContract::class);
    }

    /**
     * This tenant's currently active lease, if any. Added for the admin
     * Penalties tab: lets the Record Damage form auto-load a tenant's
     * Room/Bed once selected, without a separate lookup query scattered
     * across controllers.
     */
    public function activeContract(): HasOne
    {
        return $this->hasOne(LeaseContract::class)->where('status', 'active')->latestOfMany();
    }

    public function billingStatements(): HasMany
    {
        return $this->hasMany(BillingStatement::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function penalties(): HasMany
    {
        return $this->hasMany(Penalty::class);
    }

    public function damages(): HasMany
    {
        return $this->hasMany(Damage::class);
    }

    public function escalationLogs(): HasMany
    {
        return $this->hasMany(EscalationLog::class);
    }

}