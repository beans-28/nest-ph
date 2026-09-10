<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Use Case Reports Tables 42/43 — Reviews & Ratings, Submit Review after
 * Move-out.
 *
 * Scope simplification (flagged for BAGUI): the Figma "Review Overlay"
 * design shows 5 separate category ratings plus a recommend field.
 * Tables 42/43 only ever call for one overall star rating (1-5) and an
 * optional comment, so that's all this stores.
 *
 * Eligibility trigger: Table 16 (Record Move-Out) was never built as its
 * own flow, so this uses the tenant's existing status: inactive from
 * Deactivate Account (Table 38) instead — while excluding blacklisted
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

        return response()->json([
            'message' => 'Thank you for your review!',
            'review' => $review,
        ]);
    }
}