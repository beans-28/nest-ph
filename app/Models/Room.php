<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_no',
        'floor_id',
        'room_type',
        'monthly_rate',
        'status',
        'vr_asset_path',
        'vr_caption',
        'vr_visibility',
    ];

    protected $casts = [
        'monthly_rate' => 'decimal:2',
    ];

    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function syncStatusFromBeds(): void
    {
        if ($this->status === 'maintenance') {
            return;
        }

        $hasVacant = $this->beds()->where('status', 'vacant')->exists();
        $this->update(['status' => $hasVacant ? 'available' : 'full']);
    }
}