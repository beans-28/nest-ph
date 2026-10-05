<?php

namespace App\Console\Commands;

use Database\Seeders\DemoDataSeeder;
use Illuminate\Console\Command;

/**
 * Runs on every Laravel Cloud deploy (Settings > Deployments > Deploy
 * commands, after migrate). Rebuilds the defense demo data ONLY when
 * DEMO_RESET_ON_DEPLOY=true is set on the environment; otherwise it does
 * nothing. Turn that variable off (or delete it) before real tenants use
 * the site, or every deploy would wipe them.
 */
class ResetDemoData extends Command
{
    protected $signature = 'demo:reset {--force : Run even when DEMO_RESET_ON_DEPLOY is off}';

    protected $description = 'Wipe and re-seed the defense demo data (only if DEMO_RESET_ON_DEPLOY=true).';

    public function handle(): int
    {
        if (! config('app.demo_reset_on_deploy') && ! $this->option('force')) {
            $this->info('DEMO_RESET_ON_DEPLOY is off: demo data left as is.');

            return self::SUCCESS;
        }

        $this->warn('Resetting demo data: every tenant, bill, payment and ticket is replaced.');
        $this->call('db:seed', ['--class' => DemoDataSeeder::class, '--force' => true]);

        return self::SUCCESS;
    }
}
