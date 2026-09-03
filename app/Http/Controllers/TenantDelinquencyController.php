<?php

namespace App\Http\Controllers;

use App\Models\BillingStatement;
use App\Models\EscalationLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Tenant-facing view of Delinquency Escalation (Week 6, Tenant Side).
 * Shows the logged-in tenant their OWN escalation timeline and balance --
 * never another tenant's. Stage names/colors are kept in sync by hand
 * with DelinquencyController::STAGES (admin side); if you ever rename a
 * stage there, update it here too.
 */
class TenantDelinquencyController extends Controller
{
    // Colors match the approved Figma frames exactly (not the admin
    // side's own separate palette in DelinquencyController::STAGES --
    // that's a deliberately different scheme for the admin dashboard).
    // 'text' is the stage-number color: dark green on the lighter early
    // stages, white once the backgrounds get dark enough that white text
    // reads better (4+).
    private const STAGES = [
        1 => ['name' => 'Account Flagged', 'accent' => '#ffec60', 'text' => '#004f0f'],
        2 => ['name' => 'SMS Reminders', 'accent' => '#f87542', 'text' => '#004f0f'],
        3 => ['name' => 'Portal Restricted', 'accent' => '#fe424b', 'text' => '#004f0f'],
        4 => ['name' => 'Emergency Contact Notified', 'accent' => '#a24346', 'text' => '#ffffff'],
        5 => ['name' => 'Demand Letter Issued', 'accent' => '#645d5d', 'text' => '#ffffff'],
        6 => ['name' => 'Blacklisted', 'accent' => '#000000', 'text' => '#ffffff'],
    ];

    public function page(Request $request)
    {
        $tenant = $request->attributes->get('tenant') ?? $request->user()->tenant;

        BillingStatement::syncOverdueStatuses();

        $overdueBills = BillingStatement::where('tenant_id', $tenant->id)
            ->where('status', 'overdue')
            ->orderBy('due_date')
            ->get();

        // Admin override entries (Table 29) are internal admin notes, not
        // a stage the tenant was actually flagged/reminded/restricted at
        // -- excluded here the same way DelinquencyController excludes
        // them from "current stage" on the admin side.
        $rawLogs = $tenant->escalationLogs()
            ->orderBy('created_at')
            ->get()
            ->reject(fn (EscalationLog $log) => str_starts_with((string) $log->action_type, 'admin_override_'));

        $inEscalation = $rawLogs->isNotEmpty() || $tenant->is_blacklisted;

        $currentStage = $rawLogs->isNotEmpty() ? (int) $rawLogs->max('stage') : 0;
        $currentStageName = self::STAGES[$currentStage]['name'] ?? null;

        // One row per STAGE, not per individual log entry -- Stage 2 alone
        // can have up to 3 rows (Day 1/3/7 SMS reminders) that should read
        // to the tenant as a single "SMS Reminders Sent" entry with a date
        // range, matching the approved design, rather than three separate
        // near-identical rows.
        $stages = $rawLogs->groupBy('stage')->map(function ($stageLogs, $stage) {
            $stage = (int) $stage;

            return [
                'stage' => $stage,
                'stage_name' => self::STAGES[$stage]['name'] ?? "Stage {$stage}",
                'stage_accent' => self::STAGES[$stage]['accent'] ?? '#9f9f9f',
                'stage_text' => self::STAGES[$stage]['text'] ?? '#ffffff',
                'earliest' => $stageLogs->min('created_at'),
                'latest' => $stageLogs->max('created_at'),
            ];
        })->sortBy('stage')->values();

        // Known simplification, matching the admin side: sums total_amount
        // on overdue bills directly, without subtracting partial payments
        // already made against an overdue-status bill.
        $balance = round((float) $overdueBills->sum('total_amount'), 2);
        $totalPenalties = round((float) $overdueBills->sum('penalty_amount'), 2);
        $oldestDueDate = $overdueBills->min('due_date');

        $daysOverdue = $oldestDueDate ? Carbon::parse($oldestDueDate)->diffInDays(now()) : 0;
        $monthsOverdue = intdiv($daysOverdue, 30);

        // Stage 4 (Table 26): the actual SMS sent to the tenant's emergency
        // contact, pulled from the real log row rather than re-typed here --
        // if EscalationService::stage4EmergencyContact()'s wording ever
        // changes, this stays accurate without a second place to update.
        $emergencyContactLog = $rawLogs->first(fn (EscalationLog $log) => $log->action_type === 'emergency_contact_notified');

        // Table 27 step 8's tenant-facing equivalent: whether a real PDF is
        // actually sitting on disk yet, so the button only shows once
        // there's something real to download -- never a broken link.
        $demandLetterReady = $rawLogs->contains(
            fn (EscalationLog $log) => $log->action_type === 'demand_letter_generated' && $log->status === 'sent'
        );

        $emergencyContactInitials = null;
        if ($tenant->emergency_contact_name) {
            $parts = preg_split('/\s+/', trim($tenant->emergency_contact_name));
            $emergencyContactInitials = strtoupper(substr($parts[0] ?? '', 0, 1).substr($parts[1] ?? '', 0, 1));
        }

        $dormProfile = \App\Models\DormitoryProfile::current();

        return view('tenantdelinquency', [
            'tenant' => $tenant,
            'inEscalation' => $inEscalation,
            'currentStage' => $currentStage,
            'currentStageName' => $currentStageName,
            'stages' => $stages,
            'dormContactEmail' => $dormProfile->contact_email,
            'dormContactNumber' => $dormProfile->contact_number,
            'overdueBills' => $overdueBills,
            'balance' => $balance,
            'totalPenalties' => $totalPenalties,
            'oldestDueDate' => $oldestDueDate,
            'monthsOverdue' => $monthsOverdue,
            'isBlacklisted' => (bool) $tenant->is_blacklisted,
            'portalRestricted' => (bool) $tenant->portal_restricted,
            'emergencyContactMessage' => $emergencyContactLog?->message_content,
            'emergencyContactInitials' => $emergencyContactInitials,
            'demandLetterReady' => $demandLetterReady,
        ]);
    }

    /**
     * Tenant's own copy of Table 27 step 8 -- the same PDF the admin side
     * can download, but scoped strictly to the logged-in tenant's own
     * record. Never accepts a tenant ID from the request, so there's no
     * way to reach another tenant's letter by guessing a URL.
     */
    public function downloadDemandLetter(Request $request)
    {
        $tenant = $request->attributes->get('tenant') ?? $request->user()->tenant;

        $log = $tenant->escalationLogs()
            ->where('action_type', 'demand_letter_generated')
            ->where('status', 'sent')
            ->latest('created_at')
            ->first();

        abort_unless(
            $log && $log->message_content && \Illuminate\Support\Facades\Storage::disk('public')->exists($log->message_content),
            404,
            'No demand letter has been generated for your account yet.'
        );

        return \Illuminate\Support\Facades\Storage::disk('public')->download(
            $log->message_content,
            "Demand-Letter-{$tenant->full_name}.pdf"
        );
    }
}