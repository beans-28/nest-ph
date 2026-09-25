<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    public const STATUS_LABELS = [
        'published' => 'Public',
        'hidden' => 'Hidden',
        'removed' => 'Removed',
    ];

    protected $fillable = [
        'tenant_id',
        'rating',
        'comment',
        'is_approved',
        'status',
        'flag_reasons',
        'moderated_by',
        'moderated_at',
        'moderation_note',
    ];

    // Needed so a new Review has status "published" in PHP before it is
    // saved; otherwise the saving hook below would set is_approved = false.
    protected $attributes = [
        'status' => 'published',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'flag_reasons' => 'array',
        'moderated_at' => 'datetime',
    ];

    /**
     * `status` is the moderation state. `is_approved` stays the public gate
     * that aggregate(), breakdown() and the /dorm-info list already use, so
     * it is always derived from status here and never set by hand.
     */
    protected static function booted(): void
    {
        static::saving(function (Review $review) {
            $review->is_approved = $review->status === 'published';
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst((string) $this->status);
    }

    /**
     * One-line history shown under each review on the admin moderation card.
     */
    public function moderationSummary(): string
    {
        $who = $this->moderator?->name ?? 'an admin';
        $when = $this->moderated_at?->format('M j, Y g:i A');
        $filterNote = $this->flag_reasons ? ' Filter had flagged: ' . implode('; ', $this->flag_reasons) . '.' : '';

        return match (true) {
            $this->status === 'hidden' && ! $this->moderated_by
                => 'Auto-flagged: ' . implode('; ', $this->flag_reasons ?? ['matched the filter']) . '.',
            $this->status === 'hidden'
                => "Hidden by {$who} on {$when}." . $filterNote,
            $this->status === 'removed'
                => "Removed by {$who} on {$when}." . ($this->moderation_note ? " Reason: {$this->moderation_note}" : ''),
            $this->status === 'published' && (bool) $this->moderated_by
                => "Published by {$who} on {$when}." . $filterNote,
            default => '',
        };
    }

    /**
     * Table 41 homepage aggregate. Only public (published) reviews count.
     */
    public static function aggregate(): array
    {
        $reviews = self::where('is_approved', true)->get();
        $count = $reviews->count();

        return [
            'average' => $count > 0 ? round($reviews->avg('rating'), 1) : 0,
            'count' => $count,
        ];
    }

    /**
     * Per-star distribution for the rating breakdown bar (Table 41, step 1.3).
     */
    public static function breakdown(): array
    {
        $counts = self::where('is_approved', true)
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');

        $totalReviews = $counts->sum();

        $rows = [];
        for ($star = 5; $star >= 1; $star--) {
            $count = (int) ($counts[$star] ?? 0);
            $rows[] = [
                'star' => $star,
                'count' => $count,
                'percent' => $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0,
            ];
        }

        return $rows;
    }
}