<?php

namespace App\Services;

use App\Models\BillingStatement;
use App\Models\EscalationLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EscalationService
{
    /**
     * How many days a tenant stays at each of Stages 3-6 before
     * auto-advancing to the next stage, if still unpaid (and the prior
     * stage actually completed -- see canProceedPastStage()).
     *
     * The use cases (Tables 25-28) don't specify a day count for these --
     * only Stage 1 (day 0) and Stage 2 (day 1/3/7) have explicit numbers in
     * the manuscript. Set to 1 for fast testing/demo purposes per the team's
     * decision (Sep 2 planning). Raise this once a real policy is settled
     * for the actual defense -- this single constant retimes every later
     * stage at once (see stageDayThresholds() below).
     */
    private const DAYS_PER_STAGE = 1;

    /** Stage 2 SMS reminder days -- fixed by Table 24, not editable here. */
    private const STAGE_2_DAYS = [1, 3, 7];

    /** A log row in this status counts as "this action actually succeeded." */
    private const COMPLETE_STATUSES = ['sent', 'resolved'];

    public function __construct(private TextbeeService $sms)
    {
    }

    /**
     * Entry point. Run this once a day (via the scheduler) or manually
     * (php artisan escalation:process) against every currently-overdue
     * billing statement. Advances each tenant through every stage their
     * current days-overdue count now qualifies them for, but only past a
     * stage whose prior stage has genuinely COMPLETED (not just "a log row
     * exists") -- matching Table 28's exception path. Failed sends are
     * retried on the next run rather than silently treated as done.
     *
     * Also resolves escalations for any statement that's since been paid.
     *
     * Returns the list of billing statement IDs that were checked, for the
     * console command to report back.
     */
    public function processAll(): array
    {
        BillingStatement::syncOverdueStatuses();

        $processed = [];

        $overdueBills = BillingStatement::where('status', 'overdue')
            ->with('tenant')
            ->get();

        foreach ($overdueBills as $bill) {
            if (! $bill->tenant || $bill->tenant->is_blacklisted) {
                continue; // Stage 6 is permanent (Table 28) -- nothing left to do.
            }

            $daysOverdue = (int) Carbon::parse($bill->due_date)->diffInDays(now(), false);

            if ($daysOverdue < 0) {
                continue; // Not actually overdue yet -- shouldn't happen given the query above, but safe.
            }

            $this->advance($bill, $daysOverdue);
            $processed[] = $bill->id;
        }

        $this->resolveSettledEscalations();

        return $processed;
    }

    /**
     * Applies every stage this tenant now qualifies for, in order. Each
     * stage's own method internally decides whether it's already complete,
     * needs a first attempt, or needs a retry -- this method just tries
     * every stage whose day threshold has passed; the gating on "did the
     * PRIOR stage actually finish" happens explicitly before Stage 6, per
     * Table 28's exception. (Stages 2-5 are timing-gated only, since the
     * manuscript's exception language about blocked/incomplete stages is
     * specific to Stage 6 -- Tables 24-27 only ever say "retry the failed
     * action itself," not "block later stages until this one succeeds.")
     */
    private function advance(BillingStatement $bill, int $daysOverdue): void
    {
        [$stage3Day, $stage4Day, $stage5Day, $stage6Day] = $this->stageDayThresholds();

        $this->stage1Flag($bill);

        foreach (self::STAGE_2_DAYS as $day) {
            if ($daysOverdue >= $day) {
                $this->stage2Reminder($bill, $day);
            }
        }

        if ($daysOverdue >= $stage3Day) {
            $this->stage3PortalRestriction($bill);
        }

        if ($daysOverdue >= $stage4Day) {
            $this->stage4EmergencyContact($bill);
        }

        if ($daysOverdue >= $stage5Day) {
            $this->stage5DemandLetter($bill);
        }

        if ($daysOverdue >= $stage6Day) {
            $this->stage6Blacklist($bill);
        }
    }

    /**
     * Cumulative day thresholds for Stages 3-6, built from the single
     * DAYS_PER_STAGE constant above. With DAYS_PER_STAGE = 1: Stage 3 at
     * day 8, Stage 4 at day 9, Stage 5 at day 10, Stage 6 at day 11.
     */
    private function stageDayThresholds(): array
    {
        $stage3Day = max(self::STAGE_2_DAYS) + self::DAYS_PER_STAGE;
        $stage4Day = $stage3Day + self::DAYS_PER_STAGE;
        $stage5Day = $stage4Day + self::DAYS_PER_STAGE;
        $stage6Day = $stage5Day + self::DAYS_PER_STAGE;

        return [$stage3Day, $stage4Day, $stage5Day, $stage6Day];
    }

    /** The existing log row for this billing statement + action, if any. */
    private function findLog(BillingStatement $bill, string $actionType): ?EscalationLog
    {
        return EscalationLog::where('billing_id', $bill->id)
            ->where('action_type', $actionType)
            ->first();
    }

    /** Did this specific action actually succeed, not just "get attempted"? */
    private function isComplete(?EscalationLog $log): bool
    {
        return $log && in_array($log->status, self::COMPLETE_STATUSES, true);
    }

    /**
     * Stage 1 -- Table 23: flagged the moment the due date passes.
     * Synchronous, nothing to send or retry -- 'resolved' here just means
     * "this action is complete," not "the escalation is over." The
     * billing statement's own 'overdue' status IS the flag itself.
     */
    private function stage1Flag(BillingStatement $bill): void
    {
        if ($this->isComplete($this->findLog($bill, 'account_flagged'))) {
            return;
        }

        EscalationLog::create([
            'tenant_id' => $bill->tenant_id,
            'billing_id' => $bill->id,
            'stage' => 1,
            'action_type' => 'account_flagged',
            'status' => 'resolved',
        ]);

        // Table 23, step 7: "notify the administrator." No admin
        // notification channel (email/in-app) exists anywhere in this
        // project yet -- same stub pattern already used in
        // ApplicationController::notify() and BillingController's Step 8.
        Log::info('[escalation] Stage 1: account flagged overdue', [
            'tenant_id' => $bill->tenant_id,
            'billing_id' => $bill->id,
        ]);
    }

    /**
     * Stage 2 -- Table 24: SMS reminder on Day 1, 3, or 7.
     * Retries on the next run if the previous attempt failed (step 4.1:
     * "if SMS gateway is unavailable, log failure and retry"), updating
     * the existing row rather than creating a duplicate.
     */
    private function stage2Reminder(BillingStatement $bill, int $day): void
    {
        $actionType = "sms_reminder_day{$day}";
        $log = $this->findLog($bill, $actionType);

        if ($this->isComplete($log)) {
            return;
        }

        $tenant = $bill->tenant;
        $message = $this->stage2Message($bill, $day);
        $sent = $this->sms->send($tenant->contact_number ?? '', $message);

        $this->saveLog($log, [
            'tenant_id' => $tenant->id,
            'billing_id' => $bill->id,
            'stage' => 2,
            'action_type' => $actionType,
            'message_content' => $message,
            'status' => $sent ? 'sent' : 'pending',
        ]);
    }

    private function stage2Message(BillingStatement $bill, int $day): string
    {
        $urgency = $day >= 7 ? 'URGENT' : 'Reminder';
        $balance = number_format((float) $bill->total_amount, 2);

        return "{$urgency}: Your account with " . TextbeeService::BRAND_NAME
            . " is now {$day} day(s) overdue. Outstanding balance (incl. penalties): PHP {$balance}. "
            . 'Please pay via the tenant portal to avoid further account restrictions.';
    }

    /**
     * Stage 3 -- Table 25: restrict portal access to the payment link only.
     * Retries the SMS on a later run if it failed, without re-applying the
     * restriction flag redundantly (harmless, but update() is idempotent
     * either way).
     */
    private function stage3PortalRestriction(BillingStatement $bill): void
    {
        $log = $this->findLog($bill, 'portal_restricted');

        if ($this->isComplete($log)) {
            return;
        }

        $tenant = $bill->tenant;
        $tenant->update(['portal_restricted' => true]);

        $message = 'Your account access has been restricted due to unpaid balance. '
            . 'Please settle your balance to restore full access. - ' . TextbeeService::BRAND_NAME;

        $sent = $this->sms->send($tenant->contact_number ?? '', $message);

        $this->saveLog($log, [
            'tenant_id' => $tenant->id,
            'billing_id' => $bill->id,
            'stage' => 3,
            'action_type' => 'portal_restricted',
            'message_content' => $message,
            'status' => $sent ? 'sent' : 'pending',
        ]);

        Log::info('[escalation] Stage 3: portal restricted', ['tenant_id' => $tenant->id]);
    }

    /**
     * Stage 4 -- Table 26: notify the tenant's registered emergency
     * contact. Retries on a later run whether the previous failure was a
     * missing contact number (Table 26's own exception path -- an admin
     * may have since added one) or a failed send.
     */
    private function stage4EmergencyContact(BillingStatement $bill): void
    {
        $log = $this->findLog($bill, 'emergency_contact_notified');

        if ($this->isComplete($log)) {
            return;
        }

        $tenant = $bill->tenant;

        if (empty($tenant->emergency_contact_number)) {
            // Table 26 exception: missing emergency contact -- notify admin
            // (stub, same pattern as elsewhere), log as pending so it's
            // visible on the escalation history AND retried automatically
            // once the number is added.
            Log::warning('[escalation] Stage 4: no emergency contact on file, cannot notify', [
                'tenant_id' => $tenant->id,
            ]);

            $this->saveLog($log, [
                'tenant_id' => $tenant->id,
                'billing_id' => $bill->id,
                'stage' => 4,
                'action_type' => 'emergency_contact_notified',
                'status' => 'pending',
            ]);

            return;
        }

        $daysOverdue = (int) Carbon::parse($bill->due_date)->diffInDays(now(), false);
        $balance = number_format((float) $bill->total_amount, 2);
        $message = "This is to inform you that {$tenant->full_name}'s account at "
            . TextbeeService::BRAND_NAME . " is {$daysOverdue} days overdue, balance PHP {$balance}. "
            . 'Please encourage them to settle it as soon as possible.';

        $sent = $this->sms->send($tenant->emergency_contact_number, $message);

        $this->saveLog($log, [
            'tenant_id' => $tenant->id,
            'billing_id' => $bill->id,
            'stage' => 4,
            'action_type' => 'emergency_contact_notified',
            'message_content' => $message,
            'status' => $sent ? 'sent' : 'pending',
        ]);

        if ($sent) {
            // Table 26, step 6: "Administrator is notified that Stage 4
            // notification has been sent." Same stub pattern as Stage 1/3.
            Log::info('[escalation] Stage 4: emergency contact notified', ['tenant_id' => $tenant->id]);
        }
    }

    /**
     * Stage 5 -- Table 27: a formal demand letter PDF, compiling the
     * tenant's full escalation history. PDF generation isn't built yet
     * (Thursday's task builds the Eviction Notice PDF -- the natural place
     * to add this alongside it, since both are PDF-generation work). This
     * logs the stage transition honestly as 'pending' rather than
     * pretending a letter was generated when it wasn't. Because Stage 6
     * requires this to reach 'sent'/'resolved' before it can fire (see
     * canProceedToStage6()), Stage 6 will correctly stay dormant until
     * Thursday's PDF work marks this row complete.
     */
    private function stage5DemandLetter(BillingStatement $bill): void
    {
        if ($this->isComplete($this->findLog($bill, 'demand_letter_generated'))) {
            return;
        }

        $log = $this->findLog($bill, 'demand_letter_generated');

        if (! $log) {
            EscalationLog::create([
                'tenant_id' => $bill->tenant_id,
                'billing_id' => $bill->id,
                'stage' => 5,
                'action_type' => 'demand_letter_generated',
                'status' => 'pending',
            ]);

            Log::info('[escalation] Stage 5 reached: demand letter PDF generation not yet built, logged as pending', [
                'tenant_id' => $bill->tenant_id,
                'billing_id' => $bill->id,
            ]);
        }
        // If a pending row already exists, leave it as-is -- nothing new to
        // do until Thursday's real PDF generation updates it to 'sent'.
    }

    /**
     * Stage 6 -- Table 28: permanently flag the tenant Delinquent and
     * blacklist them. Table 28's own exception path is explicit: "One or
     * more prior escalation stages are incomplete or unverified; system
     * blocks Stage 6 from triggering and notifies the administrator." So
     * this checks that every prior stage actually completed -- not just
     * that enough days have passed -- before doing anything.
     *
     * `escalation_logs` (this row plus every prior stage's rows for this
     * tenant) already serves as the "blacklist record with full escalation
     * audit trail" the use case calls for -- no separate blacklist table
     * needed, matching the same reasoning already applied to the Eviction
     * Notice correction elsewhere in this project.
     *
     * Note: Table 28 step 4 ("restrict future inquiry form submissions
     * from this tenant") is NOT yet wired into InquiryController -- the
     * is_blacklisted flag is set here, but nothing currently checks it on
     * the public inquiry form. Flagged as a follow-up.
     */
    private function stage6Blacklist(BillingStatement $bill): void
    {
        if ($this->isComplete($this->findLog($bill, 'delinquent_blacklisted'))) {
            return;
        }

        if (! $this->canProceedToStage6($bill)) {
            Log::warning('[escalation] Stage 6 blocked: one or more prior stages incomplete or unverified', [
                'tenant_id' => $bill->tenant_id,
                'billing_id' => $bill->id,
            ]);

            return;
        }

        $tenant = $bill->tenant;
        $tenant->update(['is_blacklisted' => true]);

        EscalationLog::create([
            'tenant_id' => $tenant->id,
            'billing_id' => $bill->id,
            'stage' => 6,
            'action_type' => 'delinquent_blacklisted',
            'status' => 'resolved',
        ]);

        Log::info('[escalation] Stage 6: tenant flagged delinquent and blacklisted', [
            'tenant_id' => $tenant->id,
        ]);
    }

    /**
     * Table 28's exception path, made concrete: every stage from 1 through
     * 5 must have a log row for this billing statement in a COMPLETE
     * status. Stage 2 checks specifically for the Day 7 reminder, since
     * that's the reminder that closes out Stage 2 in the manuscript.
     */
    private function canProceedToStage6(BillingStatement $bill): bool
    {
        $requiredActionTypes = [
            'account_flagged',
            'sms_reminder_day7',
            'portal_restricted',
            'emergency_contact_notified',
            'demand_letter_generated',
        ];

        foreach ($requiredActionTypes as $actionType) {
            if (! $this->isComplete($this->findLog($bill, $actionType))) {
                return false;
            }
        }

        return true;
    }

    /** Create a new log row, or update an existing pending one on retry. */
    private function saveLog(?EscalationLog $existing, array $attributes): void
    {
        if ($existing) {
            $existing->update($attributes);

            return;
        }

        EscalationLog::create($attributes);
    }

    /**
     * Table 23/24/25/26 exception paths ("if payment is received, cancel
     * remaining reminders / lift restriction / close the escalation"): for
     * any billing statement that's since been paid but still has open
     * (non-resolved) escalation log entries, mark them resolved and lift
     * the portal restriction. Deliberately does NOT touch Stage 6 --
     * Table 28 treats blacklisting as permanent, unlike the earlier stages.
     */
    public function resolveSettledEscalations(): void
    {
        $settledBills = BillingStatement::where('status', 'paid')
            ->whereHas('escalationLogs', fn ($q) => $q->where('status', '!=', 'resolved'))
            ->with('tenant')
            ->get();

        foreach ($settledBills as $bill) {
            EscalationLog::where('billing_id', $bill->id)
                ->where('status', '!=', 'resolved')
                ->update(['status' => 'resolved']);

            if ($bill->tenant && $bill->tenant->portal_restricted) {
                $bill->tenant->update(['portal_restricted' => false]);
            }
        }
    }
}