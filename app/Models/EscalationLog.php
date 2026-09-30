<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EscalationLog extends Model
{
    /**
     * The six escalation stages, shared by the admin Delinquency page
     * (DelinquencyController) and the tenant's own page
     * (TenantDelinquencyController), so the two can't drift apart.
     * 'tenant_name' is the wording the tenant sees where it differs.
     * 'text' is the stage-number colour: dark green on the lighter early
     * stages, white once the background is dark enough (4+).
     */
    public const STAGES = [
        1 => ['name' => 'Account Flagged', 'accent' => '#ffec60', 'text' => '#004f0f'],
        2 => ['name' => 'SMS Reminders', 'accent' => '#f87542', 'text' => '#004f0f'],
        3 => ['name' => 'Portal Restricted', 'accent' => '#fe424b', 'text' => '#004f0f'],
        4 => ['name' => 'Emergency Contact', 'tenant_name' => 'Emergency Contact Notified', 'accent' => '#a24346', 'text' => '#ffffff'],
        5 => ['name' => 'Demand Letter', 'tenant_name' => 'Demand Letter Issued', 'accent' => '#645d5d', 'text' => '#ffffff'],
        6 => ['name' => 'Blacklisted', 'accent' => '#000000', 'text' => '#ffffff'],
    ];

    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'billing_id',
        'stage',
        'action_type',
        'message_content',
        'status',
        'performed_by',
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
