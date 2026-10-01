<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One row of the "Other Charges" table in the Payments and Fees Schedule
 * (lost key P50, hazardous item P500, damage at repair cost...). The
 * Schedule says no charge applies unless it is listed there, so admins
 * pick from this list when adding a penalty.
 */
class DormitoryCharge extends Model
{
    protected $fillable = [
        'name',
        'amount',
        'amount_note',
        'when_applies',
        'sort_order',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public static function ordered()
    {
        return static::orderBy('sort_order')->orderBy('id')->get();
    }

    /** "P50", "P500 per month", or the note alone when there's no fixed amount. */
    public function amountLabel(): string
    {
        if ($this->amount === null) {
            return $this->amount_note ?: 'Amount to be set';
        }

        return trim('₱' . number_format((float) $this->amount, 2) . ' ' . ($this->amount_note ?? ''));
    }

    public function toClientArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'amount' => $this->amount !== null ? (float) $this->amount : null,
            'amount_note' => $this->amount_note,
            'when_applies' => $this->when_applies,
            'amount_label' => $this->amountLabel(),
        ];
    }
}
