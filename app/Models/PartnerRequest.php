<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A dormitory owner asking to use NEST.PH for their own dormitory.
 */
class PartnerRequest extends Model
{
    protected $fillable = [
        'owner_name',
        'dorm_name',
        'email',
        'contact_number',
        'city',
        'bed_count',
        'message',
        'dpa_consent',
        'status',
        'notes',
    ];

    /** Status => label, in the order a request normally moves through. */
    public const STATUSES = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'demo_scheduled' => 'Demo scheduled',
        'onboarded' => 'Onboarded',
        'declined' => 'Declined',
    ];

    protected $casts = [
        'dpa_consent' => 'boolean',
        'bed_count' => 'integer',
    ];
}
