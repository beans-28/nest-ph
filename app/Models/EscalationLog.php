<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EscalationLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'billing_id',
        'stage',
        'action_type',
        'message_content',
        'status',
        'performed_by',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function billingStatement(): BelongsTo
    {
        return $this->belongsTo(BillingStatement::class, 'billing_id');
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
