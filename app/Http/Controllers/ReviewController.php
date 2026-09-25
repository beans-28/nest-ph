<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ReviewModerationService;

/**
 * Use Case Reports Tables 41/42 — Reviews & Ratings, Submit Review after
 * Move-out.
 *
 * Scope simplification (flagged for BAGUI): the Figma "Review Overlay"
 * design shows 5 separate category ratings plus a recommend field.
 * Tables 41/42 only ever call for one overall star rating (1-5) and an
 * optional comment, so that's all this stores.
 *
 * Eligibility trigger: a separate Record Move-Out flow was never built
 * (that use case was removed from the manuscript), so this uses the tenant's existing status: inactive from
 * Deactivate Account (Table 37) instead — while excluding blacklisted
 * tenants (evictions), since Stage 6 blacklisting never changes
 * tenants.status on its own.
 */
class ReviewController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $tenant = Tenant::where('user_id', Auth::id())->first();

        if (! $tenant) {
            return response()->json(['message' => 'No tenant record found for this account.'], 404);
        }

        if ($tenant->status !== 'inactive' || $tenant->is_blacklisted) {
            return response()->json([
                'message' => 'Reviews can only be submitted after a normal move-out has been recorded.',
            ], 403);
        }

        if ($tenant->review) {
            return response()->json([
                'message' => 'You have already submitted a review for your stay.',
            ], 409);
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ], [
            'rating.required' => 'Please select a star rating before submitting.',
        ]);

        $review = Review::create([
            'tenant_id' => $tenant->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        $review = app(ReviewModerationService::class)->autoModerate($review);

        return response()->json([
            'message' => $review->status === 'hidden'
                ? 'Thank you for your review! It will appear publicly once an administrator has checked it.'
                : 'Thank you for your review!',
            'review' => $review,
        ]);
    }
}