<?php

namespace App\Console\Commands;

use App\Models\BillingStatement;
use App\Services\BillingEmailService;
use Illuminate\Console\Command;

/**
 * Pureza revision (2026-11-14): email reminders BEFORE a bill turns
 * overdue. Runs daily from the scheduler (routes/console.php):
 *   - "due soon": from 3 days before the due date (BillingEmailService::DUE_SOON_DAYS)
 *   - "grace":    the due date has passed but the grace period hasn't
 * Overdue emails (after the grace period) are sent by the escalation
 * engine together with its SMS, see EscalationService.
 *
 * Each email is sent only once per bill (billing_email_logs), so running
 * this more than once a day is harmless. Test locally with
 *   php artisan billing:send-reminders
 */
class SendBillingReminders extends Command
{
    protected $signature = 'billing:send-reminders';

    protected $description = 'Email tenants whose bills are due soon or in the grace period.';

    public function handle(BillingEmailService $emails): int
    {
        $today = now()->startOfDay();
        $sent = 0;

        $bills = BillingStatement::owed()
            ->whereIn('status', ['unpaid', 'partial'])
            ->whereNotNull('due_date')
            ->where('due_date', '<=', $today->copy()->addDays(BillingEmailService::DUE_SOON_DAYS))
            ->with('tenant')
            ->withApprovedPaid()
            ->get();

        foreach ($bills as $bill) {
            if (! $bill->tenant || $bill->tenant->status === 'inactive' || $bill->remainingBalance() <= 0) {
                continue;
            }

            $label = $bill->type === 'move_in' ? 'Your move-in fee' : 'Your rent for ' . $bill->billing_period_start->format('F Y');
            $days = (int) $today->diffInDays($bill->due_date->copy()->startOfDay(), false);

            if ($days >= 0) {
                $when = $days === 0 ? 'today' : "in {$days} day" . ($days === 1 ? '' : 's');
                $ok = $emails->toTenant($bill, 'due_soon', 'Bill due ' . $when, "{$label} is due {$when}",
                    "This is a friendly reminder that {$label} is due on " . $bill->due_date->format('F j, Y') . '.');
            } elseif ($bill->type === 'monthly' && ! $bill->isPastGrace()) {
                $ok = $emails->toTenant($bill, 'grace', 'Bill past due - grace period', "{$label} is past due",
                    "The due date was " . $bill->due_date->format('F j, Y') . '. Please pay by '
                        . $bill->graceDeadline()->format('F j, Y') . ' (the end of the grace period) to avoid the one-time late penalty.');
            } else {
                continue;
            }

            $sent += $ok ? 1 : 0;
        }

        $this->info("Billing reminder emails sent or already sent: {$sent}");

        return self::SUCCESS;
    }
}
