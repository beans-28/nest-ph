<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    private const FEED_LIMIT = 20;

    /**
     * Table 43 — homepage newsfeed. Returns recent announcements with a
     * comment COUNT only; the full thread loads on demand via comments()
     * when a tenant expands a card, keeping this initial payload light.
     */
    public function index(): JsonResponse
    {
        $announcements = Announcement::withCount('comments')
            ->latest()
            ->take(self::FEED_LIMIT)
            ->get()
            ->map(fn (Announcement $a) => [
                'id' => $a->id,
                'body' => $a->body,
                'poster_name' => $a->poster_name,
                'poster_initials' => $a->poster_initials,
                'posted_at' => $a->created_at->diffForHumans(),
                'comments_count' => $a->comments_count,
                'comments_restricted' => $a->comments_restricted,
            ]);

        return response()->json(['announcements' => $announcements]);
    }

    /**
     * Table 45 — Manage Announcements. Route middleware restricts this
     * to admin/owner accounts.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $announcement = Announcement::create([
            'user_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        return response()->json([
            'message' => 'Announcement posted.',
            'announcement' => [
                'id' => $announcement->id,
                'body' => $announcement->body,
                'poster_name' => $announcement->poster_name,
                'poster_initials' => $announcement->poster_initials,
                'posted_at' => $announcement->created_at->diffForHumans(),
                'comments_count' => 0,
                'comments_restricted' => false,
            ],
        ], 201);
    }

    /**
     * Table 43, step 2 — expand comment thread.
     */
    public function comments(Announcement $announcement): JsonResponse
    {
        $comments = $announcement->comments->map(fn (AnnouncementComment $c) => [
            'id' => $c->id,
            'body' => $c->body,
            'author_name' => $c->author_name,
            'author_initials' => $c->author_initials,
            'is_admin' => $c->is_admin,
            'posted_at' => $c->created_at->diffForHumans(),
        ]);

        return response()->json([
            'comments' => $comments,
            'comments_restricted' => $announcement->comments_restricted,
        ]);
    }

    /**
     * Table 44 — Comment on Announcement. Open to both tenants and
     * admins; comments_restricted only ever blocks a tenant.
     */
    public function storeComment(Request $request, Announcement $announcement): JsonResponse
    {
        $user = $request->user();
        $isAdmin = $user->role?->role_name === 'admin';

        if ($announcement->comments_restricted && ! $isAdmin) {
            return response()->json([
                'message' => 'Comments are restricted on this announcement.',
            ], 403);
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $comment = AnnouncementComment::create([
            'announcement_id' => $announcement->id,
            'user_id' => $isAdmin ? $user->id : null,
            'tenant_id' => $isAdmin ? null : $user->tenant?->id,
            'body' => $data['body'],
            'created_at' => now(),
        ]);

        return response()->json([
            'message' => 'Comment posted.',
            'comment' => [
                'id' => $comment->id,
                'body' => $comment->body,
                'author_name' => $comment->author_name,
                'author_initials' => $comment->author_initials,
                'is_admin' => $comment->is_admin,
                'posted_at' => $comment->created_at->diffForHumans(),
            ],
            'comments_count' => $announcement->comments()->count(),
        ], 201);
    }

    /**
     * Table 46 — Manage Comments (delete). Admin/owner only.
     */
    public function destroyComment(AnnouncementComment $comment): JsonResponse
    {
        $announcementId = $comment->announcement_id;
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted.',
            'comments_count' => AnnouncementComment::where('announcement_id', $announcementId)->count(),
        ]);
    }

    /**
     * Table 46 — Manage Comments (restrict/unrestrict toggle). Admin/owner only.
     */
    public function toggleRestrict(Announcement $announcement): JsonResponse
    {
        $announcement->comments_restricted = ! $announcement->comments_restricted;
        $announcement->save();

        return response()->json([
            'message' => $announcement->comments_restricted
                ? 'Comments have been restricted.'
                : 'Comments have been re-enabled.',
            'comments_restricted' => $announcement->comments_restricted,
        ]);
    }
}