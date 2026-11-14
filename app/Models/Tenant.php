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
        'first_name',
        'last_name',
        'contact_number',
        'email',
        'emergency_contact_name',
        'emergency_contact_number',
        'emergency_contact_email',
        'emergency_billing_reminders',
        'is_blacklisted',
        'status',
        'portal_restricted',
        'escalation_paused',
        'date_of_birth',
        'home_address',
        'tenant_type',
        'id_document_path',
        'id_photo_path',
        'emergency_contact_id_path',
        'signed_contract_path',
        'deactivation_reason',
        'deactivated_at',
        'deactivated_by',
    ];

    protected $casts = [
        'is_blacklisted' => 'boolean',
        'emergency_billing_reminders' => 'boolean',
        'portal_restricted' => 'boolean',
        'escalation_paused' => 'boolean',
        'date_of_birth' => 'date',
        'deactivated_at' => 'datetime',
    ];

    protected $appends = ['full_name'];

    /** Public URL of the tenant's 2x2 ID picture (their profile picture), or null. */
    public function getIdPhotoUrlAttribute(): ?string
    {
        if (! $this->id_photo_path) {
            return null;
        }

        return str_starts_with($this->id_photo_path, 'http')
            ? $this->id_photo_path
            : \Illuminate\Support\Facades\Storage::disk('public')->url($this->id_photo_path);
    }

    /** Two-letter initials, shown when there's no ID picture. */
    public function getInitialsAttribute(): string
    {
        return strtoupper(mb_substr((string) $this->first_name, 0, 1) . mb_substr((string) $this->last_name, 0, 1)) ?: '?';
    }

    public function getFullNameAttribute(): string
    {
        $parts = array_filter([$this->first_name, $this->last_name]);

        return $parts ? implode(' ', $parts) : '(no name on record)';
    }

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

    /** The tenant's newest lease, whatever its status (for the lease filter). */
    public function latestContract(): HasOne
    {
        return $this->hasOne(LeaseContract::class)->latestOfMany();
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

    public function deactivatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deactivated_by');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}