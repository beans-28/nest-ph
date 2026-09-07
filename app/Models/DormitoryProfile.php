<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DormitoryProfile extends Model
{
    protected $table = 'dormitory_profile';

    protected $fillable = [
        'dorm_name',
        'description',
        'address',
        'contact_number',
        'contact_email',
        'logo_path',
        'policies_file_path',
        'contract_template_path',
        'business_permit_path',
        'bir_registration_path',
        'gcash_number',
        'bdo_account_number',
        'payments_and_fees',
        'house_rules',
        'checkout_procedures',
    ];

    /**
     * This system manages exactly one dormitory, so the profile is effectively
     * a singleton — always row 1. Returns an empty (unsaved) instance if no
     * row exists yet, so callers never have to null-check.
     */
    public static function current(): self
    {
        return static::first() ?? new static([
            'dorm_name' => 'NEST.PH',
        ]);
    }

    /**
     * Use Case Report — Manage Dormitory Profile (Table 39): uploading BIR
     * Registration credentials is what triggers the public "BIR Registration
     * Seal/Badge" per RMC No. 038-2026. There's no separate boolean column
     * for this on purpose — the presence of the file IS the flag, so it can
     * never drift out of sync with whether a document is actually on file.
     */
    public function isBirVerified(): bool
    {
        return (bool) $this->bir_registration_path;
    }
}
