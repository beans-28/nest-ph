<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\BillingStatement;
use App\Models\Floor;
use App\Models\MaintenanceTicket;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * GET /dashboard — shared entry point for both tenant and admin logins.
     * Admins see the Admin Dashboard, tenants see the Tenant Dashboard.
     * Anyone else (shouldn't normally happen) falls back to the Breeze
     * placeholder so nothing breaks.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user && $user->role && $user->role->role_name === 'admin') {
            return $this->admin();
        }

        // If the user has a tenant relation or their role is explicitly
        // 'tenant', show the tenant dashboard. Tenant relation may be
        // missing for some accounts (legacy import), so tenant() is
        // defensive and will render a safe empty state.
        if ($user && ($user->tenant || ($user->role && $user->role->role_name === 'tenant'))) {
            return $this->tenant();
        }

        // No dashboard view exists named `dashboard` (we use
        // `admindashboard` and `tenantdashboard`), so redirect to login
        // if we can't decide — this avoids the "View [dashboard] not
        // found" exception.
        return redirect()->route('login');
    }

    private function admin()
    {
        $floorsQuery = Floor::with('rooms.beds');

        if (Schema::hasColumn('floors', 'floor_number')) {
            $floorsQuery->orderBy('floor_number');
        } else {
            $floorsQuery->orderBy('id');
        }

        $floors = $floorsQuery->get();

        // REAL DATA — pulled from the same Floor/Room/Bed tables Vacancy
        // Monitoring already uses.
        $occupancy = $floors->map(function ($floor) {
            $beds = $floor->rooms->flatMap->beds;

            // Determine a numeric floor value if available, otherwise fall back
            // to display name fields. This prevents passing null to ordinal().
            $num = null;
            if (property_exists($floor, 'floor_number') && $floor->floor_number !== null) {
                $num = (int) $floor->floor_number;
            } elseif (property_exists($floor, 'sort_order') && $floor->sort_order !== null) {
                $num = (int) $floor->sort_order;
            }

            if ($num !== null) {
                $label = $this->ordinal($num) . ' Floor';
            } else {
                $label = $floor->floor_name ?? $floor->name ?? 'Floor';
            }

            return [
                'label' => $label,
                'occupied' => $beds->where('status', 'occupied')->count(),
                'total' => $beds->count(),
            ];
        })->values();

        $allBeds = Bed::all();
        $totalBeds = $allBeds->count();
        $vacantBeds = $allBeds->where('status', 'vacant')->count();
        $vacancyRate = $totalBeds > 0 ? (int) round(($vacantBeds / $totalBeds) * 100) : 0;

        return view('admindashboard', compact('occupancy', 'vacancyRate', 'vacantBeds', 'totalBeds'));
    }

    private function tenant()
    {
        $tenant = auth()->user()->tenant;

        if (! $tenant) {
            // Defensive empty state when the user has a tenant role but
            // no Tenant model attached (legacy/imported accounts).
            return view('tenantdashboard', [
                'tenant' => null,
                'contract' => null,
                'balanceDue' => 0,
                'nextDueDate' => null,
                'daysUntilDue' => null,
                'recentBills' => collect(),
                'openTicketsCount' => 0,
                'inProgressCount' => 0,
                'recentTickets' => collect(),
            ]);
        }

        $contract = $tenant->contracts()
            ->where('status', 'active')
            ->with('bed.room')
            ->first();

        // Balance due — same "outstanding across unpaid/partial/overdue
        // statements" logic as the Billing page, kept in sync deliberately.
        $openBills = BillingStatement::where('tenant_id', $tenant->id)
            ->whereIn('status', ['unpaid', 'partial', 'overdue'])
            ->with('payments')
            ->orderBy('due_date')
            ->get();

        $balanceDue = $openBills->sum(function ($bill) {
            $approved = $bill->payments->where('status', 'approved')->sum('amount_paid');
            return $bill->total_amount - $approved;
        });

        $nextDueDate = $openBills->first()?->due_date;
        $daysUntilDue = $nextDueDate ? now()->startOfDay()->diffInDays($nextDueDate, false) : null;

        // Recent billing — last 4 statements, newest first.
        $recentBills = BillingStatement::where('tenant_id', $tenant->id)
            ->with('payments')
            ->latest('due_date')
            ->take(4)
            ->get()
            ->map(function ($bill) {
                $approved = $bill->payments->where('status', 'approved')->sum('amount_paid');
                return [
                    'id' => $bill->id,
                    'month_label' => $bill->billing_period_start?->format('M Y'),
                    'total_amount' => $bill->total_amount,
                    'balance' => round($bill->total_amount - $approved, 2),
                    'status' => $bill->status,
                    'due_date' => $bill->due_date,
                ];
            });

        // Tickets — table/model already exist (built ahead of the full
        // ticketing UI, which is Week 7 scope); safe to read from here.
        $openTicketsCount = MaintenanceTicket::where('tenant_id', $tenant->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        $inProgressCount = MaintenanceTicket::where('tenant_id', $tenant->id)
            ->where('status', 'in_progress')
            ->count();

        $recentTickets = MaintenanceTicket::where('tenant_id', $tenant->id)
            ->latest('submitted_at')
            ->take(4)
            ->get(['id', 'title', 'status', 'submitted_at']);

        return view('tenantdashboard', [
            'tenant' => $tenant,
            'contract' => $contract,
            'balanceDue' => round($balanceDue, 2),
            'nextDueDate' => $nextDueDate,
            'daysUntilDue' => $daysUntilDue,
            'recentBills' => $recentBills,
            'openTicketsCount' => $openTicketsCount,
            'inProgressCount' => $inProgressCount,
            'recentTickets' => $recentTickets,
        ]);
    }

    /**
     * "1st", "2nd", "3rd", "4th", "11th", etc.
     */
    private function ordinal(int $number): string
    {
        if (in_array($number % 100, [11, 12, 13], true)) {
            return $number . 'th';
        }

        return $number . match ($number % 10) {
            1 => 'st',
            2 => 'nd',
            3 => 'rd',
            default => 'th',
        };
    }
}