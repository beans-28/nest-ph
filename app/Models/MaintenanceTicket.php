<?php

namespace App\Models;

use App\Services\TicketPriorityClassifier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
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
        'tenant_report' => 'Report a Tenant',
    ];

    /** Reasons a tenant can pick when filing a "Report a Tenant" ticket. */
    public const REPORT_REASONS = [
        'noise_disturbance' => 'Noise / Disturbance',
        'harassment_threats' => 'Harassment or Threats',
        'theft_missing_items' => 'Theft / Missing Items',
        'property_damage' => 'Damaging Property',
        'house_rules_violation' => 'House Rules Violation',
        'cleanliness_hygiene' => 'Cleanliness / Hygiene',
        'unauthorized_visitors' => 'Unauthorized Visitors',
        'other' => 'Other',
    ];

    /**
     * Pending is stored as "open" (label only) so existing open-ticket
     * counts keep working. Resolved = fixed; Closed = ended without a fix
     * (duplicate, not valid, withdrawn).
     */
    public const STATUSES = [
        'open' => 'Pending',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ];

    public const CLOSED_STATUSES = ['resolved', 'closed'];

    /**
     * A deadline is "due soon" once this share of its time window is left
     * (e.g. Medium resolve = 3 days -> warning in the last 18 hours;
     * Critical response = 5 min -> last 75 seconds).
     */
    public const DUE_SOON_SHARE = 0.25;

    /**
     * Set by the system (TicketPriorityClassifier), never by hand. Order
     * matters: most serious first.
     */
    public const PRIORITIES = [
        'critical' => 'Critical',
        'high' => 'High',
        'medium' => 'Medium',
        'low' => 'Low',
    ];

    /**
     * Service targets per priority, in minutes.
     *   response: staff must first act (mark Seen / reply / change status)
     *   resolve:  ticket must be Resolved or Rejected
     */
    public const SLA = [
        'critical' => ['response' => 5, 'resolve' => 60],
        'high' => ['response' => 30, 'resolve' => 4 * 60],
        'medium' => ['response' => 8 * 60, 'resolve' => 3 * 24 * 60],
        'low' => ['response' => 2 * 24 * 60, 'resolve' => 7 * 24 * 60],
    ];

    /** Submit Ticket form allows up to 5 photos per ticket. */
    public const MAX_ATTACHMENTS = 5;

    protected $fillable = [
        'tenant_id',
        'bed_id',
        'reported_tenant_id',
        'report_reason',
        'title',
        'category',
        'description',
        'attachment_paths',
        'priority',
        'priority_reason',
        'status',
        'assigned_to',
        'responded_at',
        'delay_reason',
        'revised_due_at',
        'resolved_at',
    ];

    protected $casts = [
        'attachment_paths' => 'array',
        'responded_at' => 'datetime',
        'revised_due_at' => 'datetime',
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

    public function reportedTenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'reported_tenant_id');
    }

    /** Runs the classifier and returns the columns to save. */
    public static function autoPriorityFor(string $category, ?string $title, ?string $description, ?string $reportReason = null): array
    {
        $result = TicketPriorityClassifier::classify($category, $title, $description, $reportReason);

        return ['priority' => $result['priority'], 'priority_reason' => $result['reason']];
    }

    public function getReportReasonLabelAttribute(): ?string
    {
        return $this->report_reason ? (self::REPORT_REASONS[$this->report_reason] ?? $this->report_reason) : null;
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
        return self::PRIORITIES[$this->priority] ?? null;
    }

    public function isClosed(): bool
    {
        return in_array($this->status, self::CLOSED_STATUSES, true);
    }

    /** Lower = more serious. Used for sorting. */
    public function priorityRank(): int
    {
        $rank = array_search($this->priority, array_keys(self::PRIORITIES), true);

        return $rank === false ? 2 : $rank;
    }

    public function responseDueAt(): Carbon
    {
        return $this->created_at->copy()->addMinutes(self::SLA[$this->priority ?? 'medium']['response']);
    }

    /** Normal target from the SLA, ignoring any revised date. */
    public function originalResolveDueAt(): Carbon
    {
        return $this->created_at->copy()->addMinutes(self::SLA[$this->priority ?? 'medium']['resolve']);
    }

    /** A revised date recorded by an admin (external delay) replaces the normal target. */
    public function resolveDueAt(): Carbon
    {
        return $this->revised_due_at ?? $this->originalResolveDueAt();
    }

    public function isDelayed(): bool
    {
        return $this->revised_due_at !== null;
    }

    /**
     * The deadline the ticket is racing right now: the response target
     * until staff first act, then the resolution target.
     *
     * @return array{type: 'response'|'resolve', due: Carbon, window_minutes: float}|null
     */
    public function currentDeadline(): ?array
    {
        if ($this->isClosed()) {
            return null;
        }

        if (! $this->responded_at) {
            $due = $this->responseDueAt();

            return ['type' => 'response', 'due' => $due, 'window_minutes' => $this->created_at->diffInMinutes($due)];
        }

        $due = $this->resolveDueAt();

        return ['type' => 'resolve', 'due' => $due, 'window_minutes' => $this->created_at->diffInMinutes($due)];
    }

    /** Not overdue yet, but inside the last DUE_SOON_SHARE of its window. */
    public function isDueSoon(): bool
    {
        $d = $this->currentDeadline();
        if (! $d || now()->greaterThan($d['due'])) {
            return false;
        }

        return now()->diffInMinutes($d['due']) <= $d['window_minutes'] * self::DUE_SOON_SHARE;
    }

    /** Staff haven't responded yet and the response target has passed. */
    public function isResponseOverdue(): bool
    {
        return ! $this->isClosed() && ! $this->responded_at && now()->greaterThan($this->responseDueAt());
    }

    /** Still not closed after the resolution target. */
    public function isResolutionOverdue(): bool
    {
        return ! $this->isClosed() && now()->greaterThan($this->resolveDueAt());
    }

    public function isOverdue(): bool
    {
        return $this->isResponseOverdue() || $this->isResolutionOverdue();
    }

    /** "Respond by ..." / "Resolve by ..." for the admin card, or null when closed. */
    /**
     * One line that says where the ticket stands against its clock:
     *   "Response overdue · was due 3:05pm"
     *   "Due soon · resolve by Oct 7, 5:00pm"
     *   "Respond by 3:05pm"
     * "(revised date)" is added when an admin recorded a delay.
     */
    public function nextDeadlineLabel(): ?string
    {
        $d = $this->currentDeadline();
        if (! $d) {
            return null;
        }

        $due = $d['due'];
        $when = $due->isToday() ? $due->format('g:ia') : $due->format('M j, g:ia');
        $revised = $d['type'] === 'resolve' && $this->isDelayed() ? ' (revised date)' : '';

        if (now()->greaterThan($due)) {
            $what = $d['type'] === 'response' ? 'Response overdue' : 'Resolution overdue';

            return "{$what} · was due {$when}{$revised}";
        }

        $verb = $d['type'] === 'response' ? 'respond' : 'resolve';
        $line = "{$verb} by {$when}{$revised}";

        return $this->isDueSoon() ? "Due soon · {$line}" : ucfirst($line);
    }

    /**
     * The ONE place overdue tickets are counted. The dashboard banner,
     * the dashboard Tickets card, the notification bell and the Tickets
     * page stat strip all call this, so they can never disagree.
     *
     * @return array{total: int, critical: int, due_soon: int}
     */
    public static function overdueSummary(): array
    {
        $active = self::whereNotIn('status', self::CLOSED_STATUSES)->get();
        $overdue = $active->filter(fn (self $t) => $t->isOverdue());

        return [
            'total' => $overdue->count(),
            'critical' => $overdue->whereIn('priority', ['critical', 'high'])->count(),
            'due_soon' => $active->filter(fn (self $t) => $t->isDueSoon())->count(),
        ];
    }

    public function unresolvedForHumans(): ?string
    {
        if ($this->isClosed()) {
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