<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class MaintenanceTicket extends Model
{
    use HasFactory;

    /** Figma "drop down type" (node 994-5004). Flagged for BAGUI vs Table 33. */
    public const CATEGORIES = [
        'billing_payment_concern' => 'Billing & Payment Concern',
        'electrical_issue' => 'Electrical Issue',
        'plumbing_water_emergency' => 'Plumbing / Water Emergency',
        'security_concern' => 'Security Concern',
        'structural_damage' => 'Structural Damage',
        'safety_security' => 'Safety & Security',
        'fire_safety_hazard' => 'Fire / Safety Hazard',
        'maintenance_repairs' => 'Maintenance & Repairs',
        'facilities_amenities' => 'Facilities & Amenities',
        'administrative_leasing_concern' => 'Administrative / Leasing Concern',
        'account_access_issue' => 'Account & Access Issue',
        'noise_roommate_concern' => 'Noise / Roommate Concern',
        'suggestion_feedback' => 'Suggestion / Feedback',
    ];

    /** Figma "drop down status" (node 882-3246). Flagged for BAGUI vs Table 34. */
    public const STATUSES = [
        'open' => 'Open',
        'seen' => 'Seen',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'rejected' => 'Rejected',
    ];

    /** Table 40. */
    public const PRIORITIES = [
        'urgent' => 'Urgent',
        'non_urgent' => 'Non-Urgent',
    ];

    /**
     * Table 41 gives a RANGE, not a single number (Urgent: 24-48h,
     * Non-Urgent: 3-5 days). These use the lower bound as a placeholder --
     * same pattern as EscalationService::DAYS_PER_STAGE. Confirm the real
     * policy value with the team before the defense.
     */
    public const URGENT_OVERDUE_HOURS = 24;
    public const NON_URGENT_OVERDUE_DAYS = 3;

    protected $fillable = [
        'tenant_id',
        'bed_id',
        'title',
        'category',
        'description',
        'attachment_path',
        'priority',
        'status',
        'assigned_to',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class, 'ticket_id');
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment_path
            ? Storage::disk('public')->url($this->attachment_path)
            : null;
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getPriorityLabelAttribute(): ?string
    {
        return $this->priority ? (self::PRIORITIES[$this->priority] ?? $this->priority) : null;
    }

    /**
     * Table 41: red "Overdue" badge once past the priority's threshold.
     * False for an unclassified ticket (no priority yet) or an already
     * resolved/rejected one -- postcondition says both indicators are
     * removed once resolved.
     */
    public function isOverdue(): bool
    {
        if (! $this->priority || in_array($this->status, ['resolved', 'rejected'], true)) {
            return false;
        }

        $hoursElapsed = $this->created_at->diffInHours(now());
        $thresholdHours = $this->priority === 'urgent'
            ? self::URGENT_OVERDUE_HOURS
            : self::NON_URGENT_OVERDUE_DAYS * 24;

        return $hoursElapsed >= $thresholdHours;
    }

    /**
     * Table 41: "Unresolved for N days" running count below each open
     * ticket card. Null once resolved/rejected.
     */
    public function unresolvedForHumans(): ?string
    {
        if (in_array($this->status, ['resolved', 'rejected'], true)) {
            return null;
        }

        $days = (int) $this->created_at->diffInDays(now());

        if ($days < 1) {
            $hours = (int) $this->created_at->diffInHours(now());
            return $hours <= 1 ? 'Unresolved for less than an hour' : "Unresolved for {$hours} hours";
        }

        return $days === 1 ? 'Unresolved for 1 day' : "Unresolved for {$days} days";
    }
}