<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\BillingStatement;
use App\Models\Floor;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Payment;
use App\Services\ActivityFeedService;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{
    /**
     * /dashboard is shared by both roles after login (see AuthController —
     * both admin and tenant login JS redirect here). Branch by role so each
     * gets the right view.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user?->role?->role_name === 'admin';

        return $isAdmin
            ? $this->adminDashboard()
            : $this->tenantDashboard($request);
    }

    private function adminDashboard()
    {
        BillingStatement::syncOverdueStatuses();

        $floors = Floor::with('rooms.beds')->orderBy('floor_number')->get();

        $occupancy = $floors->map(function ($floor) {
            $beds = $floor->rooms->flatMap->beds;

            return [
                'label' => 'Floor ' . $floor->floor_number,
                'total' => $beds->count(),
                'occupied' => $beds->where('status', 'occupied')->count(),
            ];
        })->values();

        $allBeds = Bed::all();
        $totalBeds = $allBeds->count();
        $vacantBeds = $allBeds->where('status', 'vacant')->count();
        $vacancyRate = $totalBeds > 0 ? round(($vacantBeds / $totalBeds) * 100) : 0;

        // --- Total Tenants ---
        $totalTenants = Tenant::where('status', 'active')->count();

        // --- Revenue this month (approved payments only) ---
        $now = now();
        $revenueThisMonth = (float) Payment::where('status', 'approved')
            ->whereYear('payment_date', $now->year)
            ->whereMonth('payment_date', $now->month)
            ->sum('amount_paid');

        // --- Delinquent accounts (distinct tenants with an overdue bill) ---
        $delinquentCount = BillingStatement::where('status', 'overdue')
            ->distinct('tenant_id')
            ->count('tenant_id');

        // --- Worst overdue account, for the alert banner ---
        $topDelinquent = null;
        $worstBill = BillingStatement::where('status', 'overdue')
            ->whereNotNull('due_date')
            ->orderBy('due_date') // oldest due_date = most overdue
            ->with('tenant.activeContract.bed.room')
            ->first();

        if ($worstBill && $worstBill->tenant) {
            $room = $worstBill->tenant->activeContract?->bed?->room;

            $topDelinquent = [
                'name' => $worstBill->tenant->full_name,
                'room_no' => $room?->room_no,
                'days_overdue' => (int) Carbon::parse($worstBill->due_date)->diffInDays(now()),
            ];
        }

        // --- Recent Activities preview: latest 10 from the full feed.
        // See "View All" -> activityLog() below for the complete, paginated list.
        $recentActivities = app(ActivityFeedService::class)->all()->take(10);

        return view('admindashboard', compact(
            'occupancy',
            'vacancyRate',
            'vacantBeds',
            'totalBeds',
            'totalTenants',
            'revenueThisMonth',
            'delinquentCount',
            'topDelinquent',
            'recentActivities'
        ));
    }

    /**
     * The full Activity Log page ("View All" from the dashboard's Recent
     * Activities card). Pulls the same unified feed as the dashboard
     * preview, but paginated instead of capped at 10.
     */
    public function activityLog(Request $request)
    {
        $perPage = 20;
        $page = max(1, (int) $request->query('page', 1));

        $all = app(ActivityFeedService::class)->all();

        $items = $all->slice(($page - 1) * $perPage, $perPage)->values();

        $activities = new LengthAwarePaginator(
            $items,
            $all->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('activitylog', compact('activities'));
    }

    private function tenantDashboard(Request $request)
    {
        $tenant = $request->attributes->get('tenant') ?? $user = $request->user()->tenant;

        if (! $tenant) {
            // Account has no linked tenant record (e.g. created without an
            // email at approval time — see ApplicationController). No real
            // dashboard to show; fall back to the placeholder rather than
            // crashing on a null tenant.
            return view('dashboard');
        }

        $contract = $tenant->contracts()
            ->where('status', 'active')
            ->with('bed.room.floor')
            ->first();

        $bills = BillingStatement::where('tenant_id', $tenant->id)
            ->with('payments')
            ->latest('billing_period_start')
            ->take(10)
            ->get();

        $currentBill = $bills->firstWhere(fn ($b) => in_array($b->status, ['unpaid', 'partial', 'overdue']));

        $balanceDue = 0;
        $nextDueDate = null;
        $daysUntilDue = null;

        if ($currentBill) {
            $paid = $currentBill->payments->where('status', 'approved')->sum('amount_paid');
            $balanceDue = round($currentBill->total_amount - $paid, 2);
            $nextDueDate = $currentBill->due_date;
            $daysUntilDue = now()->startOfDay()->diffInDays($nextDueDate, false);
        }

        $recentBills = $bills->take(5)->map(fn ($b) => [
            'month_label' => $b->billing_period_start->format('M Y'),
            'total_amount' => $b->total_amount,
            'status' => $b->status,
        ])->values();

        // No ticketing system built yet (Week 7 scope) — the dashboard
        // template already has an empty-state fallback for this, so an
        // empty collection here is honest, not a placeholder hack.
        $recentTickets = collect();
        $openTicketsCount = 0;
        $inProgressCount = 0;

        return view('tenantdashboard', compact(
            'tenant', 'contract', 'balanceDue', 'nextDueDate', 'daysUntilDue',
            'recentBills', 'recentTickets', 'openTicketsCount', 'inProgressCount'
        ));
    }
}