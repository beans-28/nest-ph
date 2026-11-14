<?php

namespace App\Services;

use App\Mail\BillingNoticeMail;
use App\Models\BillingEmailLog;
use App\Models\BillingStatement;
use App\Models\DormitoryProfile;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Pureza revision (2026-11-14): billing notices by EMAIL, alongside the
 * SMS and the portal bell.
 *
 * Every email is recorded in billing_email_logs. sendOnce() checks that
 * table first, so the daily jobs can run as often as they like without
 * sending the same notice twice. A failed send is retried on the next run.
 */
class BillingEmailService
{
    /** "Due soon" email goes out this many days before the due date. */
    public const DUE_SOON_DAYS = 3;

    /** Sends a notice to the tenant (once per bill + kind). */
    public function toTenant(BillingStatement $bill, string $kind, string $subject, string $heading, string $intro, string $closing = ''): bool
    {
        $tenant = $bill->tenant;
        if (! $tenant?->email) {
            return false;
        }

        $mail = new BillingNoticeMail($subject, $heading, $intro, $this->details($bill),
            $closing ?: 'Upload your payment proof on the Billing page of the tenant portal. If you have already paid, you can ignore this email.');

        return $this->sendOnce($tenant, $bill, $kind, 'tenant', $tenant->email, $mail);
    }

    /**
     * Sends the guardian (emergency contact) the same notice as the tenant,
     * by email, if they agreed to billing reminders and have an email.
     * Tenant Agreement 9.3: only the amount, due date and penalty, never
     * the tenant's name or other details. 9.4: tells them how to opt out.
     */
    public function toGuardian(BillingStatement $bill, string $kind, string $intro): bool
    {
        $tenant = $bill->tenant;
        if (! $tenant || ! $tenant->emergency_billing_reminders || ! $tenant->emergency_contact_email) {
            return false;
        }

        $profile = DormitoryProfile::current();
        $mail = new BillingNoticeMail('Billing reminder', 'Billing reminder', $intro, $this->details($bill, true),
            'You receive this because you agreed to receive billing reminders as an emergency contact.'
                . ($profile->contact_email ? " To stop, email {$profile->contact_email}." : ''),
            true);

        return $this->sendOnce($tenant, $bill, $kind, 'guardian', $tenant->emergency_contact_email, $mail);
    }

    /** True if this notice was already emailed successfully. */
    public function alreadySent(BillingStatement $bill, string $kind, string $recipientType): bool
    {
        return BillingEmailLog::where('billing_id', $bill->id)->where('kind', $kind)
            ->where('recipient_type', $recipientType)->where('status', 'sent')->exists();
    }

    private function sendOnce(Tenant $tenant, BillingStatement $bill, string $kind, string $recipientType, string $email, BillingNoticeMail $mail): bool
    {
        if ($this->alreadySent($bill, $kind, $recipientType)) {
            return true;
        }

        $log = BillingEmailLog::firstOrNew(['billing_id' => $bill->id, 'kind' => $kind, 'recipient_type' => $recipientType]);
        $log->fill(['tenant_id' => $tenant->id, 'email' => $email]);

        try {
            Mail::to($email)->send($mail);
            $log->fill(['status' => 'sent', 'error' => null])->save();

            return true;
        } catch (\Throwable $e) {
            $log->fill(['status' => 'failed', 'error' => mb_substr($e->getMessage(), 0, 500)])->save();
            Log::warning('[billing-email] send failed', ['tenant_id' => $tenant->id, 'billing_id' => $bill->id, 'kind' => $kind, 'error' => $e->getMessage()]);

            return false;
        }
    }

    /** The bill facts shown in the email's grey box. */
    private function details(BillingStatement $bill, bool $forGuardian = false): array
    {
        $rows = [];
        if (! $forGuardian) {
            $rows['Bill'] = $bill->type === 'move_in' ? 'Move-in fee' : 'Rent for ' . $bill->billing_period_start?->format('F Y');
        }
        $rows['Amount due'] = '₱' . number_format($bill->remainingBalance(), 2);
        if ((float) $bill->penalty_amount > 0) {
            $rows['Includes penalty'] = '₱' . number_format((float) $bill->penalty_amount, 2);
        }
        $rows['Due date'] = $bill->due_date?->format('F j, Y') ?? '—';
        if (! $forGuardian && $bill->type === 'monthly' && ($grace = $bill->graceDeadline())) {
            $rows['Grace period ends'] = $grace->format('F j, Y');
        }

        return $rows;
    }
}
