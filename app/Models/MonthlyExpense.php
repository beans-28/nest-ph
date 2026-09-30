<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One month of the dorm's own expenses (see the monthly_expenses migration).
 */
class MonthlyExpense extends Model
{
    protected $fillable = [
        'month',
        'electricity',
        'water',
        'internet',
        'salaries',
        'other',
        'other_notes',
        'recorded_by',
    ];

    protected $casts = [
        'month' => 'date',
        'electricity' => 'float',
        'water' => 'float',
        'internet' => 'float',
        'salaries' => 'float',
        'other' => 'float',
    ];

    public function total(): float
    {
        return round($this->electricity + $this->water + $this->internet + $this->salaries + $this->other, 2);
    }
}
