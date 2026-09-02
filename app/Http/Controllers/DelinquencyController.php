<?php

namespace App\Http\Controllers;

use App\Models\BillingStatement;
use App\Models\EscalationLog;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DelinquencyController extends Controller
{
    /**
     * Stage labels and colors, matching the manuscript exactly (Tables
     * 23-28) -- not the generic "Warning/Critical/Final Warning" ladder
     * from the original prototype, which didn't correspond to any real
     * stage's actual behavior. Key = escalation_logs.stage.
     */
    private const STAGES = [
        1 => ['name' => 'Account Flagged', 'accent' => '#056d05', 'bg' => '#d7efd7'],
        2 => ['name' => 'SMS Reminders', 'accent' => '#ffba52', 'bg' => '#ffecd0'],
        3 => ['name' => 'Portal Restricted', 'accent' => '#ff7957', 'bg' => '#ffd4c9'],
        4 => ['name' => 'Emergency Contact', 'accent' => '#cd0000', 'bg' => '#ffd0d0'],
        5 => ['name' => 'Demand Letter', 'accent' => '#5408da', 'bg' => '#efe3ff'],
        6 => ['name' => 'Blacklisted', 'accent' => '#020202', 'bg' => '#ecebeb'],
    ];

    /**
     * The admin Delinquency page: summary stats, the per-stage overview
     * strip, and the full delinquent-accounts list. All computed live from
     * escalation_logs/billing_statements/tenants -- nothing cached, so
     * there's no risk of this page showing a stale number.
     */
    public function page(Request $request)
    {
        BillingStatement::syncOverdueStatuses();

        // A tenant belongs on this page if they have an overdue bill right
        // now, OR they're permanently blacklisted (Stage 6 doesn't get
        // lifted by resolveSettledEscalations() even if the triggering
        // bill later gets paid -- Table 28 treats it as final).
        $overdueTenantIds = BillingStatement::where('status', 'overdue')->pluck('tenant_id');
        $blacklistedTenantIds = Tenant::where('is_blacklisted', true)->pluck('id');
        $tenantIds = $overdueTenantIds->merge($blacklistedTenantIds)->unique();

        $tenants = Tenant::whereIn('id', $tenantIds)
            ->with(['activeContract.bed.room', 'billingStatements' => function ($q) {
                $q->where('status', 'overdue')->orderByDesc('total_amount');
            }, 'escalationLogs'])
            ->get();

        $accounts = $tenants->map(fn (Tenant $tenant) => $this->transformTenant($tenant))
            ->sortByDesc('days_overdue')
            ->values();

        $stats = [
            'overdue_accounts' => BillingStatement::where('status', 'overdue')->count(),
            'tenants_in_escalation' => $tenants->count(),
            'total_overdue_balance' => $accounts->sum('balance'),
            'avg_days_overdue' => $accounts->isEmpty() ? 0 : (int) round($accounts->avg('days_overdue')),
        ];

        $stageBreakdown = collect(self::STAGES)->map(function ($meta, $stage) use ($accounts) {
            $atThisStage = $accounts->where('stage', $stage);

            return [
                'stage' => $stage,
                'name' => $meta['name'],
                'accent' => $meta['accent'],
                'bg' => $meta['bg'],
                'accounts' => $atThisStage->count(),
                'total_balance' => $atThisStage->sum('balance'),
            ];
        })->values();

        return view('delinquency', [
            'stats' => $stats,
            'stageBreakdown' => $stageBreakdown,
            'accounts' => $accounts,
        ]);
    }

    private function transformTenant(Tenant $tenant): array
    {
        $overdueBills = $tenant->billingStatements; // already filtered to status=overdue above
        $balance = (float) $overdueBills->sum('total_amount');
        $oldestDueDate = $overdueBills->min('due_date');
        $daysOverdue = $oldestDueDate ? (int) Carbon::parse($oldestDueDate)->diffInDays(now()) : 0;

        $stage = (int) ($tenant->escalationLogs->max('stage') ?? 0);
        $stageMeta = self::STAGES[$stage] ?? ['name' => 'Not Yet Flagged', 'accent' => '#9f9f9f', 'bg' => '#f0f0f0'];

        $lastPayment = $tenant->payments()->latest('created_at')->first();

        $room = $tenant->activeContract?->bed?->room?->room_no;

        return [
            'id' => $tenant->id,
            'name' => $tenant->full_name,
            'email' => $tenant->email,
            'room' => $room ?? '—',
            'days_overdue' => $daysOverdue,
            'balance' => $balance,
            'stage' => $stage,
            'stage_name' => $stageMeta['name'],
            'stage_accent' => $stageMeta['accent'],
            'stage_bg' => $stageMeta['bg'],
            'last_payment' => $lastPayment?->created_at?->format('M j, Y'),
            'is_blacklisted' => (bool) $tenant->is_blacklisted,
            'escalation_paused' => (bool) $tenant->escalation_paused,
        ];
    }

    /**
     * Table 29 -- Override Delinquency Escalation Stage. An admin can
     * Pause (stop auto-advancement without touching current stage), Reset
     * (clear all escalation history, back to a clean slate), or Clear
     * (resolve every open log and lift restrictions, without erasing the
     * history itself). Every override is logged, same as every automated
     * stage transition, so the audit trail stays complete either way.
     */
    public function override(Request $request, Tenant $tenant): JsonResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:pause,unpause,reset,clear'],
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'reason.required' => 'A reason is required for any escalation override.',
        ]);

        switch ($data['action']) {
            case 'pause':
                $tenant->update(['escalation_paused' => true]);
                break;

            case 'unpause':
                $tenant->update(['escalation_paused' => false]);
                break;

            case 'reset':
                EscalationLog::where('tenant_id', $tenant->id)->delete();
                $tenant->update([
                    'portal_restricted' => false,
                    'is_blacklisted' => false,
                    'escalation_paused' => false,
                ]);
                break;

            case 'clear':
                EscalationLog::where('tenant_id', $tenant->id)
                    ->where('status', '!=', 'resolved')
                    ->update(['status' => 'resolved']);
                $tenant->update(['portal_restricted' => false]);
                break;
        }

        EscalationLog::create([
            'tenant_id' => $tenant->id,
            'billing_id' => null,
            'stage' => $tenant->escalationLogs()->max('stage') ?? 1,
            'action_type' => 'admin_override_'.$data['action'],
            'message_content' => $data['reason'],
            'status' => 'resolved',
            'performed_by' => $request->user()?->id,
        ]);

        return response()->json([
            'message' => 'Escalation override applied.',
        ]);
    }
}
