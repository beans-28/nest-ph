<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * One expense the dorm paid (see the itemized expenses migration), e.g.
 * "Electric fan for Room 204", P1,850, paid in cash, with a receipt photo.
 * Reports add these up per month (Financial, Forecast, Expenses & Profit)
 * and per day (Daily Collection).
 */
class Expense extends Model
{
    /** Category key => label shown on screen and in exports. */
    public const CATEGORIES = [
        'electricity' => 'Electricity (Meralco)',
        'water' => 'Water',
        'internet' => 'Internet / WiFi',
        'salaries' => 'Employee Salaries',
        'repairs' => 'Maintenance & Repairs',
        'equipment' => 'Furniture, Appliances & Equipment',
        'supplies' => 'Supplies',
        'other' => 'Others',
    ];

    /** Bills that go up and down with the season (used by the Forecast). */
    public const UTILITIES = ['electricity', 'water', 'internet'];

    public const PAYMENT_METHODS = [
        'cash' => 'Cash',
        'gcash' => 'GCash',
        'bank_transfer' => 'Bank Transfer',
        'other' => 'Other',
    ];

    protected $fillable = [
        'category',
        'description',
        'amount',
        'date_paid',
        'payment_method',
        'receipt_path',
        'recurring_expense_id',
        'recorded_by',
    ];

    protected $casts = [
        'amount' => 'float',
        'date_paid' => 'date',
    ];

    /** Expenses paid from $from to $to, both days included. */
    public function scopePaidBetween($query, $from, $to)
    {
        return $query->whereDate('date_paid', '>=', Carbon::parse($from)->toDateString())
            ->whereDate('date_paid', '<=', Carbon::parse($to)->toDateString());
    }

    public function recurringExpense(): BelongsTo
    {
        return $this->belongsTo(RecurringExpense::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function receiptUrl(): ?string
    {
        return $this->receipt_path ? Storage::disk('public')->url($this->receipt_path) : null;
    }

    /** What the admin page and the exports need for one expense. */
    public function toPayload(): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'category_label' => self::CATEGORIES[$this->category] ?? 'Others',
            'description' => $this->description,
            'amount' => $this->amount,
            'date_paid' => $this->date_paid->toDateString(),
            'date_label' => $this->date_paid->format('M j, Y'),
            'payment_method' => $this->payment_method,
            'payment_method_label' => self::PAYMENT_METHODS[$this->payment_method] ?? 'Other',
            'receipt_url' => $this->receiptUrl(),
            'is_recurring' => $this->recurring_expense_id !== null,
        ];
    }

    /**
     * Expenses added up per month for every month between $from and $to
     * that has at least one expense. Keyed by 'Y-m'; each entry has one
     * amount per category, plus 'utilities' and 'total'.
     */
    public static function totalsByMonth(Carbon $from, Carbon $to): Collection
    {
        return self::paidBetween($from->copy()->startOfMonth(), $to->copy()->endOfMonth())
            ->get(['category', 'amount', 'date_paid'])
            ->groupBy(fn (Expense $e) => $e->date_paid->format('Y-m'))
            ->map(fn (Collection $rows) => self::summarize($rows));
    }

    /** One month's totals (null if nothing was recorded that month). */
    public static function totalsForMonth(Carbon $month): ?array
    {
        return self::totalsByMonth($month, $month)->get($month->format('Y-m'));
    }

    /** Adds up a set of expenses: one amount per category, plus utilities and total. */
    public static function summarize(Collection $rows): array
    {
        $sums = [];
        foreach (array_keys(self::CATEGORIES) as $key) {
            $sums[$key] = round((float) $rows->where('category', $key)->sum('amount'), 2);
        }
        $sums['utilities'] = round(array_sum(array_intersect_key($sums, array_flip(self::UTILITIES))), 2);
        $sums['total'] = round((float) $rows->sum('amount'), 2);

        return $sums;
    }
}
