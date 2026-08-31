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
        'amenities',
        'monthly_rate',
        'status',
        'vr_asset_path',
        'vr_caption',
        'vr_visibility',
    ];

    protected $casts = [
        'monthly_rate' => 'decimal:2',
        'amenities' => 'array',
    ];

    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }

    /**
     * A room's monthly_rate is the rent for the WHOLE room, not any one
     * tenant's share — a 4-bed room split 4 ways means each tenant pays a
     * quarter, not the full room rate. This is computed live from bed count
     * rather than stored separately, so it can never drift out of sync if a
     * bed is later added or removed from the room.
     */
    public function perBedRate(): float
    {
        $bedCount = $this->relationLoaded('beds') ? $this->beds->count() : $this->beds()->count();

        return $bedCount > 0
            ? round((float) $this->monthly_rate / $bedCount, 2)
            : (float) $this->monthly_rate;
    }

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(RoomPhoto::class)->orderBy('sort_order');
    }

    /**
     * Panorama scenes making up this room's multi-scene VR tour.
     */
    public function vrScenes(): HasMany
    {
        return $this->hasMany(VrScene::class)->orderBy('sort_order');
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