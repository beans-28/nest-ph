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

    /** Figma "drop down type" (node 994-5004). Broader than Table 32's
     * "Maintenance Request / Concern / Feedback" -- flagged for BAGUI. */
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

    /** Figma "drop down status" (node 882-3246). Adds Seen/Rejected beyond
     * Table 33's literal Open/In Progress/Resolved -- flagged for BAGUI. */
    public const STATUSES = [
        'open' => 'Open',
        'seen' => 'Seen',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'rejected' => 'Rejected',
    ];

    /** Table 39. */
    public const PRIORITIES = [
        'urgent' => 'Urgent',
        'non_urgent' => 'Non-Urgent',
    ];

    /**
     * Table 40 gives a RANGE, not a single number (Urgent: 24-48h,
     * Non-Urgent: 3-5 days). These use the lower bound as a placeholder --
     * same pattern as EscalationService::DAYS_PER_STAGE. Confirm the real
     * policy value with the team before the defense.
     */
    public const URGENT_OVERDUE_HOURS = 24;
    public const NON_URGENT_OVERDUE_DAYS = 3;

    /**
     * Unresolved Non-Urgent (or unclassified) tickets older than this are
     * shown as Urgent on the admin side (never saved). Upper bound of
     * Table 40's 3-5 day range -- PLACEHOLDER, team must confirm.
     */
    public const NON_URGENT_ESCALATE_DAYS = 5;

    /** Submit Ticket form allows up to 5 photos per ticket. */
    public const MAX_ATTACHMENTS = 5;

    protected $fillable = [
        'tenant_id',
        'bed_id',
        'title',
        'category',
        'description',
        'attachment_paths',
        'priority',
        'status',
        'assigned_to',
        'resolved_at',
    ];

    protected $casts = [
        'attachment_paths' => 'array',
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

    /** Array of public Storage URLs, one per uploaded photo. */
    public function getAttachmentUrlsAttribute(): array
    {
        return collect($this->attachment_paths ?? [])
            ->map(fn ($path) => Storage::disk('public')->url($path))
            ->values()
            ->all();
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

    public function isClosed(): bool
    {
        return in_array($this->status, ['resolved', 'rejected'], true);
    }

    /**
     * Priority used for overdue checks and sorting. Computed live and
     * NEVER saved -- the stored `priority` column is untouched. A ticket
     * with no priority counts as non_urgent; an unresolved ticket older
     * than NON_URGENT_ESCALATE_DAYS is treated as urgent (Table 40).
     */
    public function effectivePriority(): string
    {
        if ($this->priority === 'urgent') {
            return 'urgent';
        }

        if (! $this->isClosed()
            && $this->created_at->diffInHours(now()) >= self::NON_URGENT_ESCALATE_DAYS * 24) {
            return 'urgent';
        }

        return $this->priority ?: 'non_urgent';
    }

    /** Urgent only because of age, not because an admin set it. */
    public function isAutoEscalated(): bool
    {
        return $this->effectivePriority() === 'urgent' && $this->priority !== 'urgent';
    }

    /**
     * The ONE place overdue tickets are counted. The dashboard banner,
     * the dashboard Tickets card, and the Tickets page stat strip all
     * call this, so they can never disagree.
     *
     * @return array{total: int, urgent: int}
     */
    public static function overdueSummary(): array
    {
        $overdue = self::whereNotIn('status', ['resolved', 'rejected'])
            ->get()
            ->filter(fn (self $t) => $t->isOverdue());

        return [
            'total' => $overdue->count(),
            'urgent' => $overdue->filter(fn (self $t) => $t->effectivePriority() === 'urgent')->count(),
        ];
    }

    public function isOverdue(): bool
    {
        if ($this->isClosed()) {
            return false;
        }

        $hoursElapsed = $this->created_at->diffInHours(now());
        $thresholdHours = $this->effectivePriority() === 'urgent'
            ? self::URGENT_OVERDUE_HOURS
            : self::NON_URGENT_OVERDUE_DAYS * 24;

        return $hoursElapsed >= $thresholdHours;
    }

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