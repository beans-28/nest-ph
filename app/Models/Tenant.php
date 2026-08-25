<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    ];

    protected $casts = [
        'is_blacklisted' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(LeaseContract::class);
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
}