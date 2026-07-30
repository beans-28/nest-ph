<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_no',
        'floor',
        'room_type',
        'monthly_rate',
        'status',
    ];

    protected $casts = [
        'monthly_rate' => 'decimal:2',
    ];

    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }
    
    public function floor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }
}
