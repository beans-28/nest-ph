<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Floor extends Model
{
    protected $fillable = [
        'floor_name',
        'floor_number',
        'description',
        'monthly_utility_cost',
        'monthly_wifi_cost',
    ];

    protected $casts = [
        'monthly_utility_cost' => 'decimal:2',
        'monthly_wifi_cost' => 'decimal:2',
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}