<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Services\ReviewModerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Admin review moderation, shown as a card on the Dormitory Profile page.
 * "Remove" is the admin's delete: the row stays so the tenant can't
 * resubmit (reviews.tenant_id is unique), but it never shows publicly.
 */
class ReviewModerationController extends Controller
{
    public function publish(Request $request, Review $review): JsonResponse
    {
        return $this->decide($request, $review, 'published', null, 'Review is now public.');
    }

    public function hide(Request $request, Review $review): JsonResponse
    {
        return $this->decide($request, $review, 'hidden', null, 'Review hidden from the public page.');
    }

    public function remove(Request $request, Review $review): JsonResponse
    {
        $data = $request->validate([
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        return $this->decide($request, $review, 'removed', $data['note'] ?? null, 'Review removed.');
    }

    /**
     * Re-runs the filter on every review no admin has acted on yet.
     * Useful for old reviews and after editing the word list.
     */
    public function rescan(ReviewModerationService $moderation): JsonResponse
    {
        $checked = 0;
        $hidden = 0;

        Review::whereNull('moderated_by')->get()->each(function (Review $review) use ($moderation, &$checked, &$hidden) {
            $checked++;
            if ($moderation->autoModerate($review)->status === 'hidden') {
                $hidden++;
            }
        });

        return response()->json([
            'message' => "Checked {$checked} reviews. {$hidden} currently hidden by the filter.",
            'checked' => $checked,
            'hidden' => $hidden,
        ]);
    }

    private function decide(Request $request, Review $review, string $status, ?string $note, string $message): JsonResponse
    {
        $review->update([
            'status' => $status,
            'moderated_by' => $request->user()->id,
            'moderated_at' => now(),
            'moderation_note' => $note ?: null,
        ]);

        $review->load('moderator');

        return response()->json([
            'message' => $message,
            'review' => [
                'id' => $review->id,
                'status' => $review->status,
                'summary' => $review->moderationSummary(),
            ],
            'aggregate' => Review::aggregate(),
        ]);
    }
}