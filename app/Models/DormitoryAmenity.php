<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DormitoryAmenity extends Model
{
    protected $fillable = [
        'key',
        'label',
        'is_enabled',
        'sort_order',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];
}
