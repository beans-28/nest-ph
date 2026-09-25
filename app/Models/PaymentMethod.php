<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PaymentMethod extends Model
{
    public const TYPES = ['ewallet', 'bank', 'cash'];

    protected $fillable = [
        'type',
        'name',
        'account_name',
        'account_number',
        'qr_path',
        'instructions',
        'sort_order',
    ];

    public static function ordered()
    {
        return static::orderBy('sort_order')->orderBy('id')->get();
    }

    public function isOnline(): bool
    {
        return $this->type !== 'cash';
    }

    /**
     * The coarse payments.payment_method enum value this maps to -- what
     * reports and admin filters group by.
     */
    public function paymentEnum(): string
    {
        return match (true) {
            $this->type === 'cash' => 'cash',
            $this->type === 'bank' => 'bank_transfer',
            str_contains(strtolower($this->name), 'gcash') => 'gcash',
            default => 'other',
        };
    }

    /**
     * Known brands keep their colors on tenant screens; anything else
     * falls back to the app's green.
     */
    public function brand(): string
    {
        $name = strtolower($this->name);

        foreach (['gcash', 'maya', 'bdo', 'bpi'] as $brand) {
            if (str_contains($name, $brand)) {
                return $brand;
            }
        }

        return $this->type === 'cash' ? 'cash' : 'default';
    }

    public function toClientArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'account_name' => $this->account_name,
            'account_number' => $this->account_number,
            'instructions' => $this->instructions,
            'qr_url' => $this->qr_path ? Storage::disk('public')->url($this->qr_path) : null,
            'brand' => $this->brand(),
        ];
    }
}
