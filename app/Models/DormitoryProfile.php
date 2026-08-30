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
}