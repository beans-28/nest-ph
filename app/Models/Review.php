<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'rating',
        'comment',
        'is_approved',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

        /**
     * Table 42 — homepage aggregate. Only counts approved reviews (all of
     * them, by default, since we're not building a moderation queue for
     * the MVP — see the migration's is_approved default).
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
     * Per-star distribution for the rating breakdown bar (Table 42, step
     * 1.3). Percent is share of total approved reviews, matching the
     * standard "80% gave 5 stars" convention most review UIs use.
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