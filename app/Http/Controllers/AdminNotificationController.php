<?php

namespace App\Http\Controllers;

use App\Console\Commands\NotifyOverdueApplications;
use App\Models\Application;
use App\Models\BillingStatement;
use App\Models\Inquiry;
use App\Models\LeaseContract;
use App\Models\MaintenanceTicket;
use App\Models\Payment;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * GET /admin/notifications (v39) — the admin bell. Unlike the tenant panel,
 * these are live counts of work waiting for staff, not stored messages:
 * each item disappears on its own once the work is done, so there's
 * nothing to mark as read.
 */
class AdminNotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // Open emergencies (Critical tickets) that are this admin's job:
        // assigned to them, or assigned to nobody yet. The bell pops these
        // up on screen until someone responds.
        $me = $request->user()->id;
        $emergencies = MaintenanceTicket::with('bed.room', 'assignees:id,name')
            ->where('priority', 'critical')
            ->whereNotIn('status', MaintenanceTicket::CLOSED_STATUSES)
            ->whereNull('responded_at')
            ->latest()
            ->get()
            ->filter(fn ($t) => $t->assignees->isEmpty() || $t->assignees->contains('id', $me))
            ->values();

        $overdueTickets = MaintenanceTicket::overdueSummary();
        $overdueSince = now()->subDays(NotifyOverdueApplications::DAYS);
        $overdueLabel = 'waiting ' . NotifyOverdueApplications::DAYS . '+ days';

        $items = [
            [
                'key' => 'emergency_mine', 'urgent' => true, 'link' => '/tickets',
                'count' => $emergencies->filter(fn ($t) => $t->assignees->isNotEmpty())->count(),
                'one' => 'emergency assigned to you', 'many' => 'emergencies assigned to you',
            ],
            [
                'key' => 'emergency_unassigned', 'urgent' => true, 'link' => '/tickets',
                'count' => $emergencies->filter(fn ($t) => $t->assignees->isEmpty())->count(),
                'one' => 'emergency with no one assigned', 'many' => 'emergencies with no one assigned',
            ],
            [
                'key' => 'proofs', 'urgent' => false, 'link' => '/payments',
                'count' => Payment::where('status', 'pending')->count(),
                'one' => 'payment proof to review', 'many' => 'payment proofs to review',
            ],
            [
                'key' => 'applications', 'urgent' => false, 'link' => '/applications',
                // Only the recent ones; older ones get their own urgent item below.
                'count' => Application::where('status', 'pending')->where('created_at', '>', $overdueSince)->count(),
                'one' => 'new application', 'many' => 'new applications',
            ],
            [
                'key' => 'applications_overdue', 'urgent' => true, 'link' => '/applications',
                'count' => Application::where('status', 'pending')
                    ->where('created_at', '<=', $overdueSince)
                    ->count(),
                'one' => 'application ' . $overdueLabel, 'many' => 'applications ' . $overdueLabel,
            ],
            [
                'key' => 'reservations_expired', 'urgent' => true, 'link' => '/tenants',
                // Shown for a week after the bed was released (reservations:expire).
                'count' => BillingStatement::where('reservation_expired_at', '>=', now()->subDays(7))->count(),
                'one' => 'reservation expired and its bed was released',
                'many' => 'reservations expired and their beds were released',
            ],
            [
                'key' => 'reservations_expiring', 'urgent' => false, 'link' => '/payments',
                // Half-paid move-in fees whose one-month reservation ends within 7 days.
                'count' => BillingStatement::where('type', 'move_in')->where('status', 'partial')
                    ->whereNull('reservation_expired_at')->get()
                    ->filter(fn ($b) => $b->reservationDeadline()?->lte(now()->addDays(7)))->count(),
                'one' => 'half-paid move-in fee due within 7 days',
                'many' => 'half-paid move-in fees due within 7 days',
            ],
            [
                'key' => 'inquiries', 'urgent' => false, 'link' => '/inquiries',
                'count' => Inquiry::where('status', 'new')->count(),
                'one' => 'inquiry waiting for a reply', 'many' => 'inquiries waiting for a reply',
            ],
            [
                'key' => 'tickets_new', 'urgent' => false, 'link' => '/tickets',
                'count' => MaintenanceTicket::where('status', 'open')->count(),
                'one' => 'pending maintenance ticket', 'many' => 'pending maintenance tickets',
            ],
            [
                'key' => 'tickets_overdue', 'urgent' => true, 'link' => '/tickets',
                'count' => $overdueTickets['total'] ?? 0,
                'one' => 'overdue ticket', 'many' => 'overdue tickets',
            ],
            [
                'key' => 'tickets_due_soon', 'urgent' => true, 'link' => '/tickets',
                'count' => $overdueTickets['due_soon'] ?? 0,
                'one' => 'ticket nearing its deadline', 'many' => 'tickets nearing their deadline',
            ],
            [
                'key' => 'delinquent', 'urgent' => true, 'link' => '/delinquency',
                'count' => BillingStatement::where('status', 'overdue')->distinct('tenant_id')->count('tenant_id'),
                'one' => 'tenant with an overdue bill', 'many' => 'tenants with overdue bills',
            ],
            [
                'key' => 'reviews', 'urgent' => false, 'link' => '/dormitory-profile',
                'count' => Review::where('status', 'hidden')->whereNull('moderated_by')->count(),
                'one' => 'review held by the filter', 'many' => 'reviews held by the filter',
            ],
            [
                'key' => 'leases', 'urgent' => false, 'link' => '/lease-contracts',
                'count' => LeaseContract::whereIn('status', ['active', 'expiring_soon'])
                    ->whereBetween('end_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
                    ->count(),
                'one' => 'lease ending within 30 days', 'many' => 'leases ending within 30 days',
            ],
        ];

        $items = collect($items)
            ->filter(fn ($i) => $i['count'] > 0)
            ->sortByDesc('urgent') // overdue work at the top
            ->map(fn ($i) => [
                'key' => $i['key'],
                'urgent' => $i['urgent'],
                'link' => $i['link'],
                'count' => $i['count'],
                'label' => $i['count'] . ' ' . ($i['count'] === 1 ? $i['one'] : $i['many']),
            ])
            ->values();

        return response()->json([
            'total' => $items->sum('count'),
            'items' => $items,
            'emergencies' => $emergencies->map(fn ($t) => [
                'id' => $t->id,
                'title' => $t->title,
                'category' => $t->category_label,
                'room' => $t->bed?->room?->room_no,
                'mine' => $t->assignees->isNotEmpty(),
                'team' => $t->assignees->pluck('name')->values(),
                'filed' => $t->created_at->diffForHumans(),
            ]),
        ]);
    }
}
