<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Http\Controllers\Api\BillingController;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Use Case Report Table 19 ("Generate Billing Statement"): billing
 * statements should be generated automatically once each tenant's billing
 * cycle date is reached, not only when an admin clicks a button.
 *
 * IMPORTANT: Laravel's scheduler only actually runs when something calls
 * `php artisan schedule:run` on a timer. There's no cron on Windows/XAMPP,
 * so this task will just sit here unused during normal local development
 * unless you either:
 *   - run `php artisan schedule:work` in a spare terminal while testing, or
 *   - (for a real deployment on a Linux server) add a system cron entry:
 *       * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
 *
 * Until one of those is set up, use the "+ Generate This Month's Billing"
 * button on the admin Billing Overview page instead (routes/web.php,
 * POST /billing/generate) -- it calls this exact same logic on demand, and
 * that's what you should use for local testing and demos.
 */
Schedule::call(function () {
    app(BillingController::class)->generate(new \Illuminate\Http\Request());
})->daily();

/**
 * Use Case Report Tables 23-28 (Delinquency Escalation): overdue tenants
 * should advance through the SMS/portal-restriction/blacklist ladder
 * automatically each day, not only when someone runs the command by hand.
 *
 * Same inert-without-a-real-cron situation as the billing schedule above --
 * see that comment for local dev options (schedule:work, or a real cron
 * entry on deployment).
 *
 * IMPORTANT — read before this ever actually runs on a live schedule:
 * EscalationService::DAYS_PER_STAGE is still set to 1 (a placeholder for
 * fast local testing/demo). Once this is wired to a real cron on a real
 * server, that constant is what decides how fast a genuinely overdue tenant
 * gets SMS'd, portal-restricted, and blacklisted -- 1 day per stage means a
 * tenant who misses a payment could be fully blacklisted in about a week.
 * This MUST be revisited with a real policy decision (see
 * app/Services/EscalationService.php) before deploying anywhere tenants
 * actually see it -- don't let this schedule entry go live with the
 * placeholder value still in place.
 */
Schedule::command('escalation:process')->daily();