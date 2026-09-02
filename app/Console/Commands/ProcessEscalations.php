<?php

namespace App\Console\Commands;

use App\Services\EscalationService;
use Illuminate\Console\Command;

class ProcessEscalations extends Command
{
    protected $signature = 'escalation:process';

    protected $description = 'Advance every overdue tenant through the delinquency escalation stages (Use Case Report Tables 23-28) and resolve any that have since paid.';

    public function handle(EscalationService $escalation): int
    {
        $processed = $escalation->processAll();

        if (empty($processed)) {
            $this->info('No overdue billing statements needed processing.');

            return self::SUCCESS;
        }

        $this->info(count($processed) . ' overdue billing statement(s) processed: ' . implode(', ', $processed));
        $this->comment('Check storage/logs/laravel.log (search "[escalation]") and the escalation_logs table for details.');

        return self::SUCCESS;
    }
}
