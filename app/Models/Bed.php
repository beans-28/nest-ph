<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bed extends Model
{
    use HasFactory;

    protected $fillable = ['room_id', 'bed_label', 'status'];

    const STATUSES = ['vacant', 'occupied', 'maintenance'];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
