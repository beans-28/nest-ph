<?php

namespace App\Console\Commands;

use App\Models\RecurringExpense;
use Illuminate\Console\Command;

/**
 * Pureza revision (2026-11-14): records recurring expenses (e.g. staff
 * salaries every payday) on the day they fall due. See RecurringExpense.
 * The Reports page also does this when it loads, so a missed day (no cron
 * running) is caught up the next time an admin opens Reports.
 */
class PostRecurringExpenses extends Command
{
    protected $signature = 'expenses:post-recurring';

    protected $description = 'Record recurring expenses (e.g. salaries) that are due today or earlier';

    public function handle(): int
    {
        $count = RecurringExpense::postDue();
        $this->info("Recorded {$count} recurring expense(s).");

        return self::SUCCESS;
    }
}
