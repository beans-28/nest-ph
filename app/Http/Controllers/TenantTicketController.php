<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceTicket;
use App\Models\Tenant;
use App\Models\TicketReply;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Use Case Reports — Submit Ticket (Table 32), Track Ticket Status
 * (Table 34). Figma: My Tickets / tracking page (node 524-6001), Add New
 * Ticket modal (node 564-3561), type dropdown (node 994-5004).
 */
class TenantTicketController extends Controller
{
    public function page(Request $request)
    {
        $tenant = $request->attributes->get('tenant') ?? $request->user()->tenant;

        $tickets = MaintenanceTicket::where('tenant_id', $tenant->id)
            ->latest('created_at')
            ->get()
            ->map(fn ($t) => $this->transform($t, $tenant))
            ->values();

        return view('tenanttickets', [
            'tenant' => $tenant,
            'tickets' => $tickets,
            'categories' => MaintenanceTicket::CATEGORIES,
            'roomNo' => $tenant->activeContract?->bed?->room?->room_no,
            'portalRestricted' => (bool) $tenant->portal_restricted,
            'maxAttachments' => MaintenanceTicket::MAX_ATTACHMENTS,
            'reportReasons' => MaintenanceTicket::REPORT_REASONS,
            // Other tenants with an active lease -- the people who can be
            // reported. Names only: room numbers would tell any tenant
            // where everyone sleeps.
            'reportableTenants' => Tenant::where('id', '!=', $tenant->id)
                ->whereHas('activeContract')
                ->get()
                ->map(fn (Tenant $t) => [
                    'id' => $t->id,
                    'name' => $t->full_name,
                ])
                ->sortBy('name')
                ->values(),
        ]);
    }

    /**
     * Table 32. Stage 3+ restriction is already enforced by the
     * delinquency.check middleware on this route group. Up to
     * MAX_ATTACHMENTS photos accepted, each validated individually.
     */
    public function store(Request $request): JsonResponse
    {
        $tenant = $request->attributes->get('tenant') ?? $request->user()->tenant;

        $data = $request->validate([
            'category' => ['required', Rule::in(array_keys(MaintenanceTicket::CATEGORIES))],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:2000'],
            'attachment' => ['nullable', 'array', 'max:' . MaintenanceTicket::MAX_ATTACHMENTS],
            'attachment.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'reported_tenant_id' => [
                'exclude_unless:category,tenant_report', 'required', 'integer',
                Rule::notIn([$tenant->id]),
                Rule::exists('tenants', 'id'),
            ],
            'report_reason' => [
                'exclude_unless:category,tenant_report', 'required',
                Rule::in(array_keys(MaintenanceTicket::REPORT_REASONS)),
            ],
        ], [
            'reported_tenant_id.required' => 'Please select the tenant you are reporting.',
            'reported_tenant_id.not_in' => 'You cannot report yourself.',
            'report_reason.required' => 'Please select a reason for the report.',
        ]);

        $paths = [];
        foreach ($request->file('attachment', []) as $file) {
            $paths[] = $file->store('ticket-attachments', 'public');
        }

        $ticket = MaintenanceTicket::create([
            'tenant_id' => $tenant->id,
            'bed_id' => $tenant->activeContract?->bed_id,
            'title' => $data['title'],
            'category' => $data['category'],
            'description' => $data['description'],
            'attachment_paths' => $paths ?: null,
            'reported_tenant_id' => $data['reported_tenant_id'] ?? null,
            'report_reason' => $data['report_reason'] ?? null,
            'status' => 'open',
            ...MaintenanceTicket::autoPriorityFor($data['category'], $data['title'], $data['description'], $data['report_reason'] ?? null),
        ]);

        return response()->json([
            'message' => "Ticket #{$ticket->id} submitted successfully. You can track its status here.",
            'ticket' => $this->transform($ticket->fresh(), $tenant),
        ]);
    }

    public function reply(Request $request, MaintenanceTicket $ticket): JsonResponse
    {
        $tenant = $request->attributes->get('tenant') ?? $request->user()->tenant;

        abort_unless($ticket->tenant_id === $tenant->id, 404);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'tenant_id' => $tenant->id,
            'message' => $data['message'],
        ]);

        return response()->json([
            'message' => 'Reply sent.',
            'ticket' => $this->transform($ticket->fresh(), $tenant),
        ]);
    }

    private function transform(MaintenanceTicket $ticket, Tenant $tenant): array
    {
        $ticket->loadMissing(['reportedTenant', 'replies' => fn ($q) => $q->with(['user.role', 'user.privileges', 'tenant'])->oldest('created_at')]);

        $messages = collect([[
            'id' => 'original',
            'message' => $ticket->description,
            'author' => $tenant->full_name,
            'is_admin' => false,
            'created_at' => $ticket->created_at->format('M j, Y g:ia'),
        ]])->concat($ticket->replies->map(fn ($r) => [
            'id' => $r->id,
            'message' => $r->message,
            'author' => $r->tenant_id ? ($r->tenant->full_name ?? $tenant->full_name) : ($r->user->name ?? 'Administrator'),
            'is_admin' => is_null($r->tenant_id),
            'author_tag' => $r->tenant_id ? null : ($r->user?->roleTag() ?? 'admin'),
            'created_at' => $r->created_at->format('M j, Y g:ia'),
        ]));

        return [
            'id' => $ticket->id,
            'title' => $ticket->title,
            'category_label' => $ticket->category_label,
            'reported_tenant_name' => $ticket->reportedTenant?->full_name,
            'report_reason_label' => $ticket->report_reason_label,
            'priority' => $ticket->priority,
            'priority_label' => $ticket->priority_label,
            'revised_due_at' => $ticket->isClosed() ? null : $ticket->revised_due_at?->format('M j, Y g:ia'),
            'delay_reason' => $ticket->isClosed() ? null : $ticket->delay_reason,
            'status' => $ticket->status,
            'status_label' => $ticket->status_label,
            'attachment_urls' => $ticket->attachment_urls,
            'submitted_at' => $ticket->created_at->format('M j, Y'),
            'messages' => $messages->values(),
        ];
    }
}