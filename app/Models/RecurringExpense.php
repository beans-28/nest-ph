<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * An expense that repeats, e.g. the guard's salary every payday. Nothing
 * is paid by this row itself: when next_date arrives, postDue() records a
 * normal Expense for it and moves next_date forward.
 *
 * Frequencies:
 *   monthly  the same day every month (the 31st becomes the 30th/28th in
 *            shorter months)
 *   payday   every 15th and every last day of the month, the usual
 *            "kinsenas / katapusan" payroll in the Philippines
 */
class RecurringExpense extends Model
{
    public const FREQUENCIES = [
        'monthly' => 'Every month',
        'payday' => 'Every payday (15th and end of month)',
    ];

    protected $fillable = [
        'category',
        'description',
        'amount',
        'payment_method',
        'frequency',
        'day_of_month',
        'next_date',
        'active',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'float',
        'next_date' => 'date',
        'active' => 'boolean',
    ];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * The first date after $date that this schedule falls on. $day is the
     * day of the month for monthly schedules (the day of the first payment).
     */
    public static function nextAfter(Carbon $date, string $frequency, int $day): Carbon
    {
        $date = $date->copy()->startOfDay();

        if ($frequency === 'payday') {
            if ($date->day < 15) {
                return $date->copy()->day(15);
            }
            if ($date->day < $date->daysInMonth) {
                return $date->copy()->endOfMonth()->startOfDay();
            }

            return $date->copy()->addDay()->day(15);
        }

        $next = $date->copy()->startOfMonth()->addMonthNoOverflow();

        return $next->day(min($day, $next->daysInMonth));
    }

    public function payload(): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'category_label' => Expense::CATEGORIES[$this->category] ?? 'Others',
            'description' => $this->description,
            'amount' => $this->amount,
            'payment_method_label' => Expense::PAYMENT_METHODS[$this->payment_method] ?? 'Other',
            'frequency' => $this->frequency,
            'frequency_label' => self::FREQUENCIES[$this->frequency] ?? $this->frequency,
            'next_date' => $this->next_date->format('M j, Y'),
            'active' => $this->active,
        ];
    }

    /**
     * Records every due occurrence of every active schedule, up to today.
     * Safe to call any number of times: next_date moves past today, so a
     * second call finds nothing to do. Runs daily from the scheduler, and
     * also when the Reports page loads (so it works without a cron too).
     * Returns how many expenses were recorded.
     */
    public static function postDue(?Carbon $today = null): int
    {
        $today = ($today ?? now())->copy()->startOfDay();
        $posted = 0;

        foreach (self::where('active', true)->whereDate('next_date', '<=', $today->toDateString())->get() as $schedule) {
            $day = $schedule->day_of_month ?? $schedule->next_date->day;
            $date = $schedule->next_date->copy();
            while ($date->lte($today)) {
                Expense::create([
                    'category' => $schedule->category,
                    'description' => $schedule->description,
                    'amount' => $schedule->amount,
                    'date_paid' => $date->toDateString(),
                    'payment_method' => $schedule->payment_method,
                    'recurring_expense_id' => $schedule->id,
                    'recorded_by' => $schedule->created_by,
                ]);
                $posted++;
                $date = self::nextAfter($date, $schedule->frequency, $day);
            }
            $schedule->update(['next_date' => $date->toDateString()]);
        }

        return $posted;
    }
}
