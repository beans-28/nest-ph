<?php

namespace App\Console\Commands;

use App\Mail\OverdueApplicationsMail;
use App\Models\Application;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Emails every admin one summary of pending applications that have been
 * waiting DAYS or more. Each application is only emailed about once
 * (applications.overdue_notified_at); the admin bell keeps showing them
 * until they're approved or rejected (AdminNotificationController).
 */
class NotifyOverdueApplications extends Command
{
    public const DAYS = 3;

    protected $signature = 'applications:notify-overdue';

    protected $description = 'Email admins about pending applications that have been waiting 3+ days.';

    public function handle(): int
    {
        $applications = Application::where('status', 'pending')
            ->where('created_at', '<=', now()->subDays(self::DAYS))
            ->whereNull('overdue_notified_at')
            ->orderBy('created_at')
            ->get();

        if ($applications->isEmpty()) {
            $this->info('No newly overdue applications.');

            return self::SUCCESS;
        }

        $emails = User::whereHas('role', fn ($q) => $q->where('role_name', 'admin'))
            ->whereNotNull('email')
            ->pluck('email');

        $sent = 0;
        foreach ($emails as $email) {
            try {
                Mail::to($email)->send(new OverdueApplicationsMail($applications));
                $sent++;
            } catch (\Throwable $e) {
                Log::warning('Overdue applications email failed to send.', [
                    'email' => $email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Only mark them if at least one admin actually got the email, so a
        // mail outage means tomorrow's run tries again.
        if ($sent > 0) {
            Application::whereIn('id', $applications->pluck('id'))->update(['overdue_notified_at' => now()]);
        }

        $this->info($applications->count() . ' overdue application(s) emailed to ' . $sent . ' admin(s).');

        return self::SUCCESS;
    }
}
