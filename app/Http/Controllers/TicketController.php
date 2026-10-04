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
 * Use Case Reports — View and Manage Tickets (Table 33), Ticket Priority
 * Classification (Table 39), Ticket Escalation Reminder (Table 40).
 * Admin side only -- tenant-side Submit Ticket (Table 32) and Track Ticket
 * Status (Table 34) are separate, not-yet-built work.
 *
 * Figma: "ticket view" modal (node 441-527), type dropdown (994-5004),
 * status dropdown (882-3246). No Figma exists for the list page itself --
 * built here matching the app-grid/app-card pattern Applications/Inquiries
 * already use, since Table 40 literally calls this a "ticket card."
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
        $tickets = MaintenanceTicket::with(['tenant', 'reportedTenant', 'assignedTo', 'bed.room'])
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
            'overdueCount' => MaintenanceTicket::overdueSummary()['total'],
            'dueSoonCount' => MaintenanceTicket::overdueSummary()['due_soon'],
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
            'replies' => fn ($q) => $q->with('user.role', 'user.privileges')->oldest('created_at'),
        ]);

        return response()->json(array_merge(
            $this->transformRow($ticket),
            [
                'description' => $ticket->description,
                'delay_reason' => $ticket->delay_reason,
                // datetime-local input format
                'revised_due_at' => $ticket->revised_due_at?->format('Y-m-d\TH:i'),
                'original_resolve_due' => $ticket->originalResolveDueAt()->format('M j, Y g:ia'),
                'attachment_urls' => $ticket->attachment_urls,
                'replies' => $ticket->replies->map(fn ($r) => [
                    'id' => $r->id,
                    'message' => $r->message,
                    'author' => $r->user?->name ?? 'Admin',
                    'author_tag' => $r->user?->roleTag() ?? 'admin',
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
     * "Save" on the ticket view modal -- Table 33 steps 4/5 (status
     * update, reply). Priority is NOT editable here -- the system sets it
     * when the ticket is filed (TicketPriorityClassifier).
     */
    public function update(Request $request, MaintenanceTicket $ticket): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(MaintenanceTicket::STATUSES))],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'reply_message' => ['nullable', 'string', 'max:2000'],
            // External delay: both or neither. Sending delayed=false clears it.
            'delayed' => ['sometimes', 'boolean'],
            'delay_reason' => ['exclude_unless:delayed,true', 'required', 'string', 'max:1000'],
            'revised_due_at' => ['exclude_unless:delayed,true', 'required', 'date', 'after:now'],
        ], [
            'delay_reason.required' => 'Please explain what is delaying this ticket.',
            'revised_due_at.required' => 'Please set the revised expected resolution date.',
            'revised_due_at.after' => 'The revised resolution date must be in the future.',
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

        $oldStatus = $ticket->status;
        $oldRevisedDue = $ticket->revised_due_at;

        DB::transaction(function () use ($ticket, $data, $request) {
            $wasResolved = $ticket->isClosed();
            $isNowResolved = in_array($data['status'], MaintenanceTicket::CLOSED_STATUSES, true);

            $delayFields = [];
            if (array_key_exists('delayed', $data)) {
                $delayFields = $data['delayed']
                    ? ['delay_reason' => $data['delay_reason'], 'revised_due_at' => $data['revised_due_at']]
                    : ['delay_reason' => null, 'revised_due_at' => null];
            }

            $ticket->update($delayFields + [
                'status' => $data['status'],
                // First staff action counts as the "initial response".
                'responded_at' => $ticket->responded_at
                    ?? (($data['status'] !== 'open' || ! empty($data['reply_message'])) ? now() : null),
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

        // Tenant notification panel (v39): status changes and staff replies.
        $label = MaintenanceTicket::STATUSES[$data['status']] ?? $data['status'];
        if ($oldStatus !== $data['status']) {
            \App\Models\TenantNotification::send($ticket->tenant_id,
                $data['status'] === 'resolved' ? 'ticket_resolved' : 'ticket_update',
                "Your ticket \"{$ticket->title}\" is now {$label}",
                ! empty($data['reply_message']) ? 'Staff replied: ' . \Illuminate\Support\Str::limit($data['reply_message'], 140) : null,
                '/my/tickets');
        }

        // Tell the tenant when a new or changed revised date is recorded.
        $ticket->refresh();
        if ($ticket->revised_due_at && ($oldRevisedDue === null || ! $ticket->revised_due_at->equalTo($oldRevisedDue))) {
            \App\Models\TenantNotification::send($ticket->tenant_id, 'ticket_update',
                "Your ticket \"{$ticket->title}\" is delayed",
                'Expected resolution: ' . $ticket->revised_due_at->format('M j, Y g:ia') . '. Reason: '
                    . \Illuminate\Support\Str::limit($ticket->delay_reason, 120),
                '/my/tickets');
        }

        if ($oldStatus === $data['status'] && ! empty($data['reply_message'])) {
            \App\Models\TenantNotification::send($ticket->tenant_id, 'ticket_reply',
                "New reply on your ticket \"{$ticket->title}\"",
                \Illuminate\Support\Str::limit($data['reply_message'], 140),
                '/my/tickets');
        }

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
            'reported_tenant_id' => $ticket->reported_tenant_id,
            'reported_tenant_name' => $ticket->reportedTenant?->full_name,
            'reported_tenant_room' => $ticket->reportedTenant?->activeContract?->bed?->room?->room_no,
            'report_reason_label' => $ticket->report_reason_label,
            'status' => $ticket->status,
            'status_label' => $ticket->status_label,
            'priority' => $ticket->priority,
            'priority_label' => $ticket->priority_label,
            'assigned_to' => $ticket->assigned_to,
            'assigned_to_name' => $ticket->assignedTo?->name,
            'priority_reason' => $ticket->priority_reason,
            'priority_rank' => $ticket->priorityRank(),
            'is_overdue' => $ticket->isOverdue(),
            'is_response_overdue' => $ticket->isResponseOverdue(),
            'deadline_label' => $ticket->nextDeadlineLabel(),
            'is_due_soon' => $ticket->isDueSoon(),
            'is_delayed' => $ticket->isDelayed(),
            'created_ts' => $ticket->created_at->timestamp,
            'unresolved_for' => $ticket->unresolvedForHumans(),
            'submitted_at' => $ticket->created_at->format('M j, Y g:ia'),
        ];
    }
}