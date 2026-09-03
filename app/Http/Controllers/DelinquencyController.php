<?php

namespace App\Http\Controllers;

use App\Models\BillingStatement;
use App\Models\EscalationLog;
use App\Models\Tenant;
use App\Services\TextbeeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DelinquencyController extends Controller
{
    /**
     * Stage labels and colors, matching the manuscript exactly (Tables
     * 23-28) -- not the generic "Warning/Critical/Final Warning" ladder
     * from the original prototype, which didn't correspond to any real
     * stage's actual behavior. Key = escalation_logs.stage.
     */
    // Colors match the approved Figma frames exactly, same palette now
    // used on the tenant side (TenantDelinquencyController::STAGES) --
    // previously this used a different, unrelated scheme. 'text' is the
    // stage-number/label color: dark green on the lighter early stages,
    // white once the backgrounds get dark enough for white to read better.
    private const STAGES = [
        1 => ['name' => 'Account Flagged', 'accent' => '#ffec60', 'text' => '#004f0f'],
        2 => ['name' => 'SMS Reminders', 'accent' => '#f87542', 'text' => '#004f0f'],
        3 => ['name' => 'Portal Restricted', 'accent' => '#fe424b', 'text' => '#004f0f'],
        4 => ['name' => 'Emergency Contact', 'accent' => '#a24346', 'text' => '#ffffff'],
        5 => ['name' => 'Demand Letter', 'accent' => '#645d5d', 'text' => '#ffffff'],
        6 => ['name' => 'Blacklisted', 'accent' => '#000000', 'text' => '#ffffff'],
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
                'text' => $meta['text'],
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

        // Only genuine ladder stages (Tables 23-28) count toward "current
        // stage." Table 29 override entries carry a stage number too (for
        // audit context), but they're administrative notes, not the tenant
        // actually being flagged/reminded/restricted -- an override must
        // never make a tenant look further along the ladder than they are.
        $stage = (int) ($tenant->escalationLogs
            ->reject(fn (EscalationLog $log) => str_starts_with((string) $log->action_type, 'admin_override_'))
            ->max('stage') ?? 0);
        $stageMeta = self::STAGES[$stage] ?? ['name' => 'Not Yet Flagged', 'accent' => '#9f9f9f', 'text' => '#5b6b60'];

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
            'stage_text' => $stageMeta['text'],
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

        // Captured BEFORE Reset wipes the log history -- this audit entry
        // should record what stage the tenant WAS at when overridden, not
        // silently fall back to a fake "Stage 1" once nothing's left.
        $previousStage = (int) ($tenant->escalationLogs()->max('stage') ?? 0);

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
            'stage' => $previousStage,
            'action_type' => 'admin_override_'.$data['action'],
            'message_content' => $data['reason'],
            'status' => 'resolved',
            'performed_by' => $request->user()?->id,
        ]);

        return response()->json([
            'message' => 'Escalation override applied.',
        ]);
    }

    /**
     * Wednesday's stage-detail modal: a tenant's full escalation
     * history/timeline (Tables 23-28), plus every Table 29 override
     * that's been applied. Ordered oldest-first so the modal reads top
     * to bottom like a real timeline.
     */
    public function history(Tenant $tenant): JsonResponse
    {
        $logs = $tenant->escalationLogs()
            ->with('performedBy:id,name')
            ->orderBy('created_at')
            ->get()
            ->map(fn (EscalationLog $log) => [
                'id' => $log->id,
                'stage' => $log->stage,
                'stage_name' => self::STAGES[$log->stage]['name'] ?? "Stage {$log->stage}",
                'stage_accent' => self::STAGES[$log->stage]['accent'] ?? '#9f9f9f',
                'action_type' => $log->action_type,
                'message_content' => $log->message_content,
                'status' => $log->status,
                'is_override' => str_starts_with((string) $log->action_type, 'admin_override_'),
                'performed_by' => $log->performedBy?->name,
                'created_at' => optional($log->created_at)->format('M j, Y g:i A'),
            ])
            ->values();

        $currentStage = (int) ($tenant->escalationLogs
            ->reject(fn (EscalationLog $log) => str_starts_with((string) $log->action_type, 'admin_override_'))
            ->max('stage') ?? 0);

        // These two flags let the frontend decide which button state to
        // show (disabled stub / real download / issue-notice form) without
        // needing to inspect action_type strings itself.
        $demandLetterReady = $logs->contains(
            fn ($l) => $l['action_type'] === 'demand_letter_generated' && $l['status'] === 'sent'
        );
        $evictionNoticeIssued = $logs->contains(
            fn ($l) => $l['action_type'] === 'eviction_notice_issued'
        );

        return response()->json([
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->full_name,
                'room' => $tenant->activeContract?->bed?->room?->room_no ?? '—',
                'current_stage' => $currentStage,
                'current_stage_name' => self::STAGES[$currentStage]['name'] ?? 'Not Yet Flagged',
                'is_blacklisted' => (bool) $tenant->is_blacklisted,
                'escalation_paused' => (bool) $tenant->escalation_paused,
                'demand_letter_ready' => $demandLetterReady,
                'eviction_notice_issued' => $evictionNoticeIssued,
            ],
            'logs' => $logs,
        ]);
    }

    /**
     * Table 27, step 8: serves a tenant's most recently generated Stage 5
     * demand letter PDF for download. The letter is system-generated
     * (EscalationService::stage5DemandLetter()) -- this endpoint only
     * ever reads what's already on disk, it never generates one itself.
     */
    public function downloadDemandLetter(Tenant $tenant)
    {
        $log = $tenant->escalationLogs()
            ->where('action_type', 'demand_letter_generated')
            ->where('status', 'sent')
            ->latest('created_at')
            ->first();

        abort_unless(
            $log && $log->message_content && Storage::disk('public')->exists($log->message_content),
            404,
            'No demand letter has been generated for this tenant yet.'
        );

        return Storage::disk('public')->download($log->message_content, "Demand-Letter-{$tenant->full_name}.pdf");
    }

    /**
     * Table 51 -- Issue Eviction Notice. Admin-discretionary, and only
     * available once the tenant has actually reached Stage 6
     * (Delinquent/Blacklisted) -- this is a discretionary action that
     * happens AFTER Stage 6, not Stage 6 itself (see the manuscript
     * correction: Table 51 originally mislabeled itself as updating the
     * escalation record "to Stage 6," which Figure 26 contradicts --
     * Stage 6 is Table 28's automatic blacklist).
     */
    public function issueEvictionNotice(Request $request, Tenant $tenant): JsonResponse
    {
        if (! $tenant->is_blacklisted) {
            return response()->json([
                'message' => 'Eviction notices can only be issued once a tenant has reached Stage 6 (Blacklisted).',
            ], 409);
        }

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
            'notice_date' => ['required', 'date'],
        ], [
            'reason.required' => 'A reason for eviction is required.',
            'notice_date.required' => 'A notice date is required.',
        ]);

        $room = $tenant->activeContract?->bed?->room?->room_no ?? 'N/A';
        $noticeDate = Carbon::parse($data['notice_date'])->format('F j, Y');

        $pdf = Pdf::loadView('pdfs.eviction-notice', [
            'tenant' => $tenant,
            'room' => $room,
            'reason' => $data['reason'],
            'noticeDate' => $noticeDate,
        ]);

        $path = 'eviction-notices/'.$tenant->id.'_'.now()->timestamp.'.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        // Table 51 exception path: "textbee.dev SMS gateway is unavailable;
        // system displays an error and logs the failed send attempt." The
        // PDF and log entry are still created either way -- only the SMS
        // delivery status differs (status: 'pending' vs 'sent') -- matching
        // the same retry-safe philosophy used everywhere else in the
        // escalation system.
        $message = 'NOTICE: This serves as your formal Eviction Notice from '
            . TextbeeService::BRAND_NAME . ' dated ' . $noticeDate
            . '. Please contact the dormitory administrator immediately regarding your account.';

        $sent = app(TextbeeService::class)->send($tenant->contact_number ?? '', $message);

        EscalationLog::create([
            'tenant_id' => $tenant->id,
            'billing_id' => null,
            'stage' => 6,
            'action_type' => 'eviction_notice_issued',
            'message_content' => $path,
            'status' => $sent ? 'sent' : 'pending',
            'performed_by' => $request->user()?->id,
        ]);

        return response()->json([
            'message' => $sent
                ? 'Eviction notice generated and sent to the tenant.'
                : 'Eviction notice generated, but the SMS could not be sent. It has been logged for retry.',
        ]);
    }

    /**
     * Serves a tenant's most recently issued eviction notice PDF.
     */
    public function downloadEvictionNotice(Tenant $tenant)
    {
        $log = $tenant->escalationLogs()
            ->where('action_type', 'eviction_notice_issued')
            ->latest('created_at')
            ->first();

        abort_unless(
            $log && $log->message_content && Storage::disk('public')->exists($log->message_content),
            404,
            'No eviction notice has been issued for this tenant yet.'
        );

        return Storage::disk('public')->download($log->message_content, "Eviction-Notice-{$tenant->full_name}.pdf");
    }
}