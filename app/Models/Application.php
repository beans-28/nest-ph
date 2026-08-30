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

        // Personal information
        'full_name',
        'birthdate',
        'gender',
        'nationality',
        'medical_condition',
        'occupation',
        'school_company',
        'school_company_address',

        // Contact information
        'contact_number',
        'email',
        'landline',
        'home_address',

        // Emergency contact information
        'emergency_contact_name',
        'emergency_contact_number',
        'emergency_contact_email',
        'emergency_contact_landline',
        'father_name',
        'mother_name',

        // Room information
        'bed_id',
        'preferred_start_date',
        'tenant_end_date',
        'type_of_tenant',
        'id_document_path',
        'signed_contract_path',

        'dpa_consent',
        'status',
        'rejection_reason',
        're_application_note',
        'created_by',
        'approved_by',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'preferred_start_date' => 'date',
        'tenant_end_date' => 'date',
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