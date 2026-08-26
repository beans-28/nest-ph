<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\BillingStatement;
use App\Models\Floor;
use Illuminate\Http\Request;

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

        return view('admindashboard', compact('occupancy', 'vacancyRate', 'vacantBeds', 'totalBeds'));
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
            'contract', 'balanceDue', 'nextDueDate', 'daysUntilDue',
            'recentBills', 'recentTickets', 'openTicketsCount', 'inProgressCount'
        ));
    }
}