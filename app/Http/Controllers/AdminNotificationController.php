<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\BillingStatement;
use App\Models\Inquiry;
use App\Models\LeaseContract;
use App\Models\MaintenanceTicket;
use App\Models\Payment;
use App\Models\Review;
use Illuminate\Http\JsonResponse;

/**
 * GET /admin/notifications (v39) — the admin bell. Unlike the tenant panel,
 * these are live counts of work waiting for staff, not stored messages:
 * each item disappears on its own once the work is done, so there's
 * nothing to mark as read.
 */
class AdminNotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $overdueTickets = MaintenanceTicket::overdueSummary();

        $items = [
            [
                'key' => 'proofs', 'urgent' => false, 'link' => '/payments',
                'count' => Payment::where('status', 'pending')->count(),
                'one' => 'payment proof to review', 'many' => 'payment proofs to review',
            ],
            [
                'key' => 'applications', 'urgent' => false, 'link' => '/applications',
                'count' => Application::where('status', 'pending')->count(),
                'one' => 'new application', 'many' => 'new applications',
            ],
            [
                'key' => 'inquiries', 'urgent' => false, 'link' => '/inquiries',
                'count' => Inquiry::where('status', 'new')->count(),
                'one' => 'inquiry waiting for a reply', 'many' => 'inquiries waiting for a reply',
            ],
            [
                'key' => 'tickets_new', 'urgent' => false, 'link' => '/tickets',
                'count' => MaintenanceTicket::where('status', 'open')->count(),
                'one' => 'new maintenance ticket', 'many' => 'new maintenance tickets',
            ],
            [
                'key' => 'tickets_overdue', 'urgent' => true, 'link' => '/tickets',
                'count' => $overdueTickets['total'] ?? 0,
                'one' => 'overdue ticket', 'many' => 'overdue tickets',
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
        ]);
    }
}
