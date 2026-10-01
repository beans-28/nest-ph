<?php

namespace App\Services;

use App\Models\BillingStatement;
use App\Models\LeaseContract;
use App\Models\Tenant;
use App\Models\TenantNotification;
use Illuminate\Support\Collection;

/**
 * Automatic tenant notifications that depend on dates rather than on an
 * admin action: bill reminders and lease-ending notices. They're created
 * when the tenant opens their notifications (and at login for the pop-up),
 * each only once thanks to its dedupe key, so no scheduler is needed.
 */
class TenantNotificationService
{
    /** Reminders start this many days before a bill's due date. */
    public const REMINDER_DAYS = 10;

    /** Lease-ending notice starts this many days before the end date. */
    public const LEASE_NOTICE_DAYS = 30;

    /**
     * Unpaid bills that are due within REMINDER_DAYS, or already overdue,
     * each with its remaining balance. Used by the login pop-up and the
     * reminder notifications.
     */
    public function billsNeedingAttention(Tenant $tenant): Collection
    {
        $today = now()->startOfDay();
        $cutoff = $today->copy()->addDays(self::REMINDER_DAYS);
        $profile = \App\Models\DormitoryProfile::current();

        return BillingStatement::where('tenant_id', $tenant->id)
            ->whereIn('status', ['unpaid', 'partial', 'overdue'])
            ->whereNotNull('due_date')
            ->where('due_date', '<=', $cutoff)
            ->withApprovedPaid()
            ->orderBy('due_date')
            ->get()
            ->map(function (BillingStatement $bill) use ($today, $profile) {
                $days = (int) $today->diffInDays($bill->due_date->copy()->startOfDay(), false);
                // Payments and Fees Schedule 5.1: past the due date but still
                // within the grace period is not "overdue" yet.
                $inGrace = $days < 0 && $bill->type === 'monthly' && ! $bill->isPastGrace($profile) && $bill->status !== 'overdue';

                return [
                    'id' => $bill->id,
                    'label' => $bill->type === 'move_in'
                        ? 'Move-in fee'
                        : 'Rent for ' . $bill->billing_period_start->format('F Y'),
                    'due_date' => $bill->due_date->format('F j, Y'),
                    'days_left' => $days, // negative = days overdue
                    'is_overdue' => $bill->status === 'overdue' || ($days < 0 && ! $inGrace),
                    'in_grace' => $inGrace,
                    'grace_until' => $bill->graceDeadline($profile)?->format('F j, Y'),
                    'balance' => $bill->remainingBalance(),
                ];
            })
            ->filter(fn ($b) => $b['balance'] > 0)
            ->values();
    }

    /** Creates any date-based notifications that are now due. Idempotent. */
    public function syncAutomatic(Tenant $tenant): void
    {
        foreach ($this->billsNeedingAttention($tenant) as $bill) {
            $amount = '₱' . number_format($bill['balance'], 2);

            if ($bill['in_grace']) {
                TenantNotification::send($tenant->id, 'bill_grace',
                    "{$bill['label']} was due on {$bill['due_date']}",
                    "Pay {$amount} by {$bill['grace_until']} to avoid the one-time late penalty.",
                    '/billing', "bill_grace:{$bill['id']}");
            } elseif ($bill['is_overdue']) {
                TenantNotification::send($tenant->id, 'bill_overdue',
                    "{$bill['label']} is overdue",
                    "{$amount} was due on {$bill['due_date']}. Please pay as soon as possible to avoid penalties and account restrictions.",
                    '/billing', "bill_overdue:{$bill['id']}");
            } else {
                $when = $bill['days_left'] === 0 ? 'today' : "in {$bill['days_left']} day" . ($bill['days_left'] === 1 ? '' : 's');
                TenantNotification::send($tenant->id, 'bill_due',
                    "{$bill['label']} is due {$when}",
                    "{$amount} is due on {$bill['due_date']}.",
                    '/billing', "bill_due:{$bill['id']}");
            }
        }

        $contract = LeaseContract::where('tenant_id', $tenant->id)->whereIn('status', ['active', 'expiring_soon'])->latest('id')->first();
        if ($contract && $contract->end_date) {
            $daysLeft = (int) now()->startOfDay()->diffInDays($contract->end_date->copy()->startOfDay(), false);
            if ($daysLeft >= 0 && $daysLeft <= self::LEASE_NOTICE_DAYS) {
                TenantNotification::send($tenant->id, 'lease_ending',
                    'Your lease ends on ' . $contract->end_date->format('F j, Y'),
                    'Please talk to the dormitory office if you plan to renew or move out.',
                    '/account', "lease_ending:{$contract->id}:{$contract->end_date->toDateString()}");
            }
        }
    }
}
