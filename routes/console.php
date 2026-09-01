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