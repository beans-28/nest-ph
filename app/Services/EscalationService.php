<?php

namespace App\Services;

use App\Mail\DelinquencyFinalNoticeMail;
use App\Models\BillingStatement;
use App\Models\EscalationLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class EscalationService
{
    /**
     * Day (counted from the end of the grace period) each of Stages 3-6
     * starts, if the bill is still unpaid. Set by the team on 2026-10-05
     * to fit Pureza's documents, which give no day counts for these steps
     * except that termination needs three months of unpaid rent (Payments
     * and Fees Schedule 5.5, Agreement 3.4):
     *   Stage 3 (portal limited to Billing, Fees 5.3)    day 14
     *   Stage 4 (emergency contact, with consent, 5.4)   day 30, one month unpaid
     *   Stage 5 (demand letter)                          day 75, deadline before 3 months
     *   Stage 6 (blacklist / termination, 5.5)           day 90, three months unpaid
     */
    public const STAGE_DAYS = [3 => 14, 4 => 30, 5 => 75, 6 => 90];

    /**
     * Stage 2 SMS reminder days. Table 23 literally specifies Day 1, 3, 7
     * -- shifted to 2/4/7 so Stage 1 (account flagged) and Stage 2's first
     * reminder never land in the same engine run, while keeping fairly
     * even spacing between reminders and Day 7 as the "full week" urgent
     * final notice. Deliberate team decision, not an oversight -- flagged
     * for BAGUI to reflect in the manuscript alongside STAGE_DAYS.
     */
    public const STAGE_2_DAYS = [2, 4, 7];

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
     * exists") -- matching Table 27's exception path. Failed sends are
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
                continue; // Stage 6 is permanent (Table 27) -- nothing left to do.
            }
            
            if ($bill->tenant->escalation_paused) {
                continue; // Table 28: admin has paused this tenant's escalation.
            }

            $daysOverdue = $this->daysOverdue($bill);

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
     * Testing/demo helper (Admin "Testing Tools" panel): runs the exact
     * same stage logic as processAll(), but scoped to ONE billing
     * statement instead of every overdue bill in the system. Lets an
     * admin push a single test tenant through the ladder on demand
     * without waiting for the scheduler and without touching every other
     * tenant's records. Not part of any use case table (23-29) — purely
     * a testing convenience that reuses the real stage methods, so the
     * behavior being demoed is genuine.
     */
    public function processBillingStatement(BillingStatement $bill): void
    {
        BillingStatement::syncOverdueStatuses();
        $bill->refresh();

        if ($bill->status !== 'overdue' || ! $bill->tenant) {
            return;
        }

        if ($bill->tenant->is_blacklisted || $bill->tenant->escalation_paused) {
            return;
        }

        $daysOverdue = $this->daysOverdue($bill);

        if ($daysOverdue < 0) {
            return;
        }

        $this->advance($bill, $daysOverdue);
    }

    /**
     * Applies every stage this tenant now qualifies for, in order. Each
     * stage's own method internally decides whether it's already complete,
     * needs a first attempt, or needs a retry -- this method just tries
     * every stage whose day threshold has passed; the gating on "did the
     * PRIOR stage actually finish" happens explicitly before Stage 6, per
     * Table 27's exception. (Stages 2-5 are timing-gated only, since the
     * manuscript's exception language about blocked/incomplete stages is
     * specific to Stage 6 -- Tables 23-26 only ever say "retry the failed
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

    /** Start days of Stages 3-6, in order (see STAGE_DAYS). */
    private function stageDayThresholds(): array
    {
        return array_values(self::STAGE_DAYS);
    }

    /**
     * Days since the grace period ended (Payments and Fees Schedule 5.1).
     * A bill due Oct 1 with a 3-day grace is "1 day overdue" on Oct 5, so
     * the ladder never starts while the tenant is still within the grace.
     */
    private function daysOverdue(BillingStatement $bill): int
    {
        $deadline = $bill->graceDeadline();

        return $deadline ? (int) $deadline->copy()->startOfDay()->diffInDays(now()->startOfDay(), false) : 0;
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
     * Stage 1 -- Table 22: flagged the moment the due date passes.
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

        // Table 22, step 7: "notify the administrator." No admin
        // notification channel (email/in-app) exists anywhere in this
        // project yet -- same stub pattern already used in
        // ApplicationController::notify() and BillingController's Step 8.
        Log::info('[escalation] Stage 1: account flagged overdue', [
            'tenant_id' => $bill->tenant_id,
            'billing_id' => $bill->id,
        ]);
    }

    /**
     * Stage 2 -- Table 23: SMS reminder on Day 1, 3, or 7.
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

    /** Stage 5 SMS. Public so the demo seeder sends the same wording. */
    public static function demandLetterSms(float $totalOwed, \Carbon\Carbon $deadline): string
    {
        return 'FORMAL DEMAND: A demand letter has been issued for your unpaid rent of PHP ' . number_format($totalOwed, 2)
            . '. Pay by ' . $deadline->format('M j, Y') . ' to avoid being blacklisted. '
            . 'Pay and view the letter at ' . route('tenant.billing') . ' - ' . TextbeeService::BRAND_NAME;
    }

    /**
     * Stage 6 SMS; $amount is already formatted ("PHP 1,234.00"). A
     * blacklisted tenant can no longer open Billing or pay online, so this
     * sends them to the office instead of the portal.
     */
    public static function blacklistSms(string $amount): string
    {
        return "FINAL NOTICE: Your NEST PH tenant account has been deactivated and blacklisted because {$amount} remained unpaid after the demand letter deadline. "
            . 'You can no longer pay online. The balance is still due: settle it directly with the dormitory office'
            . self::officeContactSuffix() . ' - ' . TextbeeService::BRAND_NAME;
    }

    private static function officeContactSuffix(): string
    {
        $profile = \App\Models\DormitoryProfile::current();
        $contacts = array_filter([$profile->contact_number, $profile->contact_email]);

        return $contacts ? ' (' . implode(' / ', $contacts) . ').' : '.';
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
     * Stage 3 -- Table 24: restrict portal access to the payment link only.
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
        $wasRestricted = $tenant->portal_restricted;
        $tenant->update(['portal_restricted' => true]);

        // Lead with the amount and how to pay, not the lock: the point of
        // this stage is to get the bill paid. Billing stays open on purpose.
        $amount = 'PHP ' . number_format($bill->remainingBalance(), 2);
        $due = $bill->due_date->format('M j, Y');
        if (! $wasRestricted) {
            \App\Models\TenantNotification::send($tenant->id, 'account_restricted',
                "Payment required: {$amount} overdue",
                "Your rent due {$due} is still unpaid. Pay {$amount} now and upload your proof on the Billing page. "
                    . 'Until it is settled, only Billing and Delinquency are open in your portal, and the next step is a formal demand letter.',
                '/billing', "account_restricted:{$bill->id}");
        }

        $message = "PAYMENT REQUIRED: Your rent of {$amount} (due {$due}) is still unpaid. "
            . 'Pay now and upload your proof at ' . route('tenant.billing') . '. '
            . 'Your portal is limited to Billing until this is settled; unpaid balances lead to a formal demand letter. - '
            . TextbeeService::BRAND_NAME;

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
     * Stage 4 -- Table 25: notify the tenant's registered emergency
     * contact. Retries on a later run whether the previous failure was a
     * missing contact number (Table 25's own exception path -- an admin
     * may have since added one) or a failed send.
     */
    private function stage4EmergencyContact(BillingStatement $bill): void
    {
        $log = $this->findLog($bill, 'emergency_contact_notified');

        if ($this->isComplete($log)) {
            return;
        }

        $tenant = $bill->tenant;

        // Tenant Agreement 9.3: an emergency contact gets billing reminders
        // ONLY if they separately agreed (the consent box they tick when
        // signing). Without consent the emergency contact is never
        // contacted -- instead the tenant gets this notice by SMS AND email.
        if (! $tenant->emergency_billing_reminders) {
            $this->stage4TenantFallback($bill, $log);

            return;
        }

        if (empty($tenant->emergency_contact_number)) {
            // Table 25 exception: missing emergency contact -- notify admin
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

        // Agreement 9.3: the message states ONLY the amount due, the due
        // date and any penalty -- no tenant name or other personal details.
        // 9.4: it also tells them how to opt out.
        $profile = \App\Models\DormitoryProfile::current();
        $message = TextbeeService::BRAND_NAME . ' billing reminder: amount due PHP ' . number_format($bill->remainingBalance(), 2)
            . ', due ' . $bill->due_date->format('M j, Y')
            . ((float) $bill->penalty_amount > 0 ? ', includes penalty PHP ' . number_format((float) $bill->penalty_amount, 2) : '')
            . '. You receive this because you agreed to billing reminders as an emergency contact.'
            . ($profile->contact_email ? " To stop, email {$profile->contact_email}." : '');

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
            // Table 25, step 6: "Administrator is notified that Stage 4
            // notification has been sent." Same stub pattern as Stage 1/3.
            Log::info('[escalation] Stage 4: emergency contact notified', ['tenant_id' => $tenant->id]);
        }
    }

    /**
     * Stage 4 fallback when the emergency contact did not consent: text the
     * tenant's own number and email the tenant. Counts as done if either
     * channel got through; if both fail it stays 'pending' and is retried.
     */
    private function stage4TenantFallback(BillingStatement $bill, ?EscalationLog $log): void
    {
        $tenant = $bill->tenant;

        $message = 'Your account with ' . TextbeeService::BRAND_NAME . ' is still overdue. Amount due: PHP '
            . number_format($bill->remainingBalance(), 2)
            . ', due ' . $bill->due_date->format('M j, Y')
            . ((float) $bill->penalty_amount > 0 ? ', includes penalty PHP ' . number_format((float) $bill->penalty_amount, 2) : '')
            . '. Please pay via the tenant portal to avoid a formal demand letter.';

        $smsSent = $this->sms->send($tenant->contact_number ?? '', $message);

        $emailSent = false;
        if ($tenant->email) {
            try {
                Mail::to($tenant->email)->send(new DelinquencyFinalNoticeMail($tenant, $bill));
                $emailSent = true;
            } catch (\Throwable $e) {
                Log::warning('[escalation] Stage 4 fallback email failed to send', [
                    'tenant_id' => $tenant->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->saveLog($log, [
            'tenant_id' => $tenant->id,
            'billing_id' => $bill->id,
            'stage' => 4,
            'action_type' => 'emergency_contact_notified',
            'message_content' => 'Emergency contact has not agreed to billing reminders (Tenant Agreement 9.3), so the tenant was notified instead'
                . ' (SMS: ' . ($smsSent ? 'sent' : 'failed') . ', email: ' . ($emailSent ? 'sent' : 'failed') . '). '
                . $message,
            'status' => ($smsSent || $emailSent) ? 'sent' : 'pending',
        ]);

        Log::info('[escalation] Stage 4: emergency contact not consented, tenant notified instead', [
            'tenant_id' => $tenant->id,
            'sms' => $smsSent,
            'email' => $emailSent,
        ]);
    }

    /**
     * Stage 5 -- Table 26: generates a real formal demand letter PDF,
     * matching the approved Figma design (node 238:1945) -- balance
     * breakdown table, highlighted deadline/blacklist dates, plus the
     * full escalation history the use case requires (step 2). Saves it
     * under storage/app/public/demand-letters/. Logs the stage as 'sent'
     * once genuinely done, which is what unblocks Stage 6's completeness
     * gate (canProceedToStage6()) -- no separate flag needed, the log
     * status IS the gate.
     */
    private function stage5DemandLetter(BillingStatement $bill): void
    {
        if ($this->isComplete($this->findLog($bill, 'demand_letter_generated'))) {
            return;
        }

        $log = $this->findLog($bill, 'demand_letter_generated');
        $tenant = $bill->tenant;

        $bills = BillingStatement::where('tenant_id', $tenant->id)
            ->where('status', 'overdue')
            ->withApprovedPaid()
            ->orderBy('billing_period_start')
            ->get();

        // What's actually still owed: partial payments are subtracted.
        $totalOwed = round($bills->sum(fn ($b) => $b->remainingBalance()), 2);
        $totalPenalties = (float) $bills->sum('penalty_amount');

        $history = $tenant->escalationLogs()->orderBy('created_at')->get();

        // The dates the letter states come from the engine's own timing, so
        // the letter can't promise a deadline the engine won't honour:
        // blacklisting happens when the bill reaches the Stage 6 day, and
        // the payment deadline is the day before. Never earlier than
        // tomorrow, in case the letter is generated late.
        $stage6Day = $this->stageDayThresholds()[3];
        $blacklistDate = $bill->graceDeadline()->copy()->addDays($stage6Day)->startOfDay();
        if ($blacklistDate->lte(now()->startOfDay())) {
            $blacklistDate = now()->addDay()->startOfDay();
        }
        $deadline = $blacklistDate->copy()->subDay();

        $dormName = \App\Models\DormitoryProfile::current()->dorm_name ?? 'NEST PH';

        $pdf = Pdf::loadView('pdfs.demand-letter', [
            'tenant' => $tenant,
            'bills' => $bills,
            'totalOwed' => $totalOwed,
            'totalPenalties' => $totalPenalties,
            'history' => $history,
            'deadline' => $deadline->format('F j, Y'),
            'blacklistDate' => $blacklistDate->format('F j, Y'),
            'dormName' => $dormName,
        ]);

        $path = "demand-letters/{$tenant->id}_{$bill->id}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        $this->saveLog($log, [
            'tenant_id' => $tenant->id,
            'billing_id' => $bill->id,
            'stage' => 5,
            'action_type' => 'demand_letter_generated',
            'message_content' => $path,
            'status' => 'sent',
        ]);

        \App\Models\TenantNotification::send($tenant->id, 'demand_letter',
            'A formal demand letter has been issued',
            'Pay ₱' . number_format($totalOwed, 2) . ' by ' . $deadline->format('F j, Y') . ' to avoid being blacklisted. You can download the letter on the Delinquency page.',
            '/my/delinquency', "demand_letter:{$bill->id}");

        // The letter is the stage's real output, so the SMS gets its own
        // log row: a failed send shows on the timeline without holding
        // back the letter's row (Stage 6 waits on that one).
        $smsMessage = self::demandLetterSms($totalOwed, $deadline);
        $smsSent = $this->sms->send($tenant->contact_number ?? '', $smsMessage);
        EscalationLog::create([
            'tenant_id' => $tenant->id,
            'billing_id' => $bill->id,
            'stage' => 5,
            'action_type' => 'demand_letter_sms',
            'message_content' => $smsMessage,
            'status' => $smsSent ? 'sent' : 'pending',
        ]);

        Log::info('[escalation] Stage 5: demand letter generated', [
            'tenant_id' => $tenant->id,
            'billing_id' => $bill->id,
            'path' => $path,
            'sms' => $smsSent,
        ]);
    }

    /**
     * Stage 6 -- Table 27: permanently flag the tenant Delinquent and
     * blacklist them. Table 27's own exception path is explicit: "One or
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
     * Note: Table 27 step 4 ("restrict future inquiry form submissions
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

        // The balance is still owed after blacklisting, but online payment is
        // closed now, so the tenant is pointed to the office.
        $amount = 'PHP ' . number_format(BillingStatement::where('tenant_id', $tenant->id)->where('status', 'overdue')
            ->withApprovedPaid()->get()->sum(fn ($b) => $b->remainingBalance()), 2);
        \App\Models\TenantNotification::send($tenant->id, 'blacklisted',
            'Your account has been deactivated',
            "{$amount} remained unpaid after the demand letter deadline, so your account has been blacklisted and online payment is no longer available. Settle the balance directly with the dormitory office.",
            '/my/delinquency', "blacklisted:{$bill->id}");
        $smsMessage = self::blacklistSms($amount);
        $smsSent = $this->sms->send($tenant->contact_number ?? '', $smsMessage);
        EscalationLog::create([
            'tenant_id' => $tenant->id,
            'billing_id' => $bill->id,
            'stage' => 6,
            'action_type' => 'blacklist_sms',
            'message_content' => $smsMessage,
            'status' => $smsSent ? 'sent' : 'pending',
        ]);

        Log::info('[escalation] Stage 6: tenant flagged delinquent and blacklisted', [
            'tenant_id' => $tenant->id,
            'sms' => $smsSent,
        ]);
    }

    /**
     * Table 27's exception path, made concrete: every stage from 1 through
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
     * Table 22/23/24/25 exception paths ("if payment is received, cancel
     * remaining reminders / lift restriction / close the escalation"): for
     * any billing statement that's since been paid but still has open
     * (non-resolved) escalation log entries, mark them resolved and lift
     * the portal restriction. Deliberately does NOT touch Stage 6 --
     * Table 27 treats blacklisting as permanent, unlike the earlier stages.
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
                \App\Models\TenantNotification::send($bill->tenant->id, 'account_restored',
                    'Your full portal access is restored',
                    'Thank you for settling your balance.', '/dashboard', "account_restored:{$bill->id}");
            }
        }
    }
}