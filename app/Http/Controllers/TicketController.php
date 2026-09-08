<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceTicket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Use Case Reports — View and Manage Tickets (Table 34), Ticket Priority
 * Classification (Table 40), Ticket Escalation Reminder (Table 41).
 * Admin side only -- tenant-side Submit Ticket (Table 33) and Track Ticket
 * Status (Table 35) are separate, not-yet-built work.
 *
 * Figma: "ticket view" modal (node 441-527), type dropdown (994-5004),
 * status dropdown (882-3246). No Figma exists for the list page itself --
 * built here matching the app-grid/app-card pattern Applications/Inquiries
 * already use, since Table 41 literally calls this a "ticket card."
 */
class TicketController extends Controller
{
    /**
     * Whole ticket list rendered once, then searched/filtered client-side
     * in JS -- same pattern as Tenant Manager, Lease Management, and
     * Applications.
     */
    public function page(Request $request)
    {
        $tickets = MaintenanceTicket::with(['tenant', 'assignedTo', 'bed.room'])
            ->latest('created_at')
            ->get();

        $rows = $tickets->map(fn (MaintenanceTicket $t) => $this->transformRow($t))->values();

        return view('admintickets', [
            'tickets' => $rows,
            'categories' => MaintenanceTicket::CATEGORIES,
            'statuses' => MaintenanceTicket::STATUSES,
            'priorities' => MaintenanceTicket::PRIORITIES,
            'openCount' => $tickets->where('status', 'open')->count(),
            'inProgressCount' => $tickets->where('status', 'in_progress')->count(),
            'overdueCount' => $tickets->filter(fn ($t) => $t->isOverdue())->count(),
        ]);
    }

    /**
     * Ticket detail for the "ticket view" modal (Figma node 441-527).
     * Fetched on demand when a card is opened -- same on-demand-detail
     * pattern Tenant Manager uses for its View drawer -- rather than
     * embedding every reply/attachment for every ticket up front.
     */
    public function show(MaintenanceTicket $ticket): JsonResponse
    {
        $ticket->load([
            'tenant',
            'bed.room',
            'assignedTo',
            'replies' => fn ($q) => $q->with('user')->oldest('created_at'),
        ]);

        return response()->json(array_merge(
            $this->transformRow($ticket),
            [
                'description' => $ticket->description,
                'attachment_urls' => $ticket->attachment_urls,
                'replies' => $ticket->replies->map(fn ($r) => [
                    'id' => $r->id,
                    'message' => $r->message,
                    'author' => $r->user?->name ?? 'Admin',
                    'created_at' => $r->created_at->format('M j, Y g:ia'),
                ]),
                'assignable_admins' => User::whereHas('role', fn ($q) => $q->where('role_name', 'admin'))
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(['id', 'name']),
            ]
        ));
    }

    /**
     * "Save" on the ticket view modal -- Table 34 steps 4/5 (status
     * update, reply). Also handles the card's standalone priority
     * selector (Table 40) -- same endpoint, just status resent unchanged.
     */
    public function update(Request $request, MaintenanceTicket $ticket): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(MaintenanceTicket::STATUSES))],
            'priority' => ['nullable', Rule::in(array_keys(MaintenanceTicket::PRIORITIES))],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'reply_message' => ['nullable', 'string', 'max:2000'],
        ]);

        if (! empty($data['assigned_to'])) {
            $isAdmin = User::whereHas('role', fn ($q) => $q->where('role_name', 'admin'))
                ->whereKey($data['assigned_to'])
                ->exists();

            if (! $isAdmin) {
                return response()->json([
                    'message' => 'Selected user is not an admin and cannot be assigned a ticket.',
                ], 422);
            }
        }

        DB::transaction(function () use ($ticket, $data, $request) {
            $wasResolved = in_array($ticket->status, ['resolved', 'rejected'], true);
            $isNowResolved = in_array($data['status'], ['resolved', 'rejected'], true);

            $ticket->update([
                'status' => $data['status'],
                'priority' => $data['priority'] ?? $ticket->priority,
                'assigned_to' => $request->has('assigned_to') ? $data['assigned_to'] : $ticket->assigned_to,
                'resolved_at' => $isNowResolved
                    ? ($ticket->resolved_at ?? now())
                    : ($wasResolved ? null : $ticket->resolved_at),
            ]);

            if (! empty($data['reply_message'])) {
                TicketReply::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $request->user()?->id,
                    'message' => $data['reply_message'],
                ]);
            }
        });

        return response()->json([
            'message' => 'Ticket updated successfully.',
            'ticket' => $this->transformRow($ticket->fresh(['tenant', 'assignedTo', 'bed.room'])),
        ]);
    }

    private function transformRow(MaintenanceTicket $ticket): array
    {
        $room = $ticket->bed?->room ?? $ticket->tenant?->activeContract?->bed?->room;

        return [
            'id' => $ticket->id,
            'tenant_name' => $ticket->tenant?->full_name,
            'room_no' => $room?->room_no,
            'title' => $ticket->title,
            'category' => $ticket->category,
            'category_label' => $ticket->category_label,
            'status' => $ticket->status,
            'status_label' => $ticket->status_label,
            'priority' => $ticket->priority,
            'priority_label' => $ticket->priority_label,
            'assigned_to' => $ticket->assigned_to,
            'assigned_to_name' => $ticket->assignedTo?->name,
            'is_overdue' => $ticket->isOverdue(),
            'unresolved_for' => $ticket->unresolvedForHumans(),
            'submitted_at' => $ticket->created_at->format('M j, Y g:ia'),
        ];
    }
}