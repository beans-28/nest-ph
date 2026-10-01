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
        'room_type_id',
        'room_type',
        'amenities',
        'monthly_rate',
        'monthly_utility_cost',
        'monthly_wifi_cost',
        'status',
        'vr_asset_path',
        'vr_caption',
        'vr_visibility',
    ];

    protected $casts = [
        'monthly_rate' => 'decimal:2',
        'monthly_utility_cost' => 'decimal:2',
        'monthly_wifi_cost' => 'decimal:2',
        'amenities' => 'array',
    ];

    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }

    /**
     * What ONE tenant pays per month for a bed in this room.
     *
     * When the room has a room type (set up in the Dormitory Profile), the
     * price comes straight from it: a "per bed" type charges every tenant
     * the listed rate, a "per room" type (e.g. a solo room) splits the room
     * price across its beds. Rooms without a type fall back to the old rule:
     * monthly_rate is the WHOLE room's rent, split evenly by bed count.
     */
    public function perBedRate(): float
    {
        $bedCount = $this->relationLoaded('beds') ? $this->beds->count() : $this->beds()->count();

        if ($this->room_type_id && $this->roomType) {
            return $this->roomType->perBedRate($bedCount);
        }

        return $bedCount > 0
            ? round((float) $this->monthly_rate / $bedCount, 2)
            : (float) $this->monthly_rate;
    }

    /**
     * Copies the room type's name and price onto this room, so the many
     * pages that read rooms.room_type / rooms.monthly_rate (filters,
     * sorting, reports) stay correct. monthly_rate stays the whole-room
     * total. Call after the type or the number of beds changes.
     */
    public function syncFromRoomType(): void
    {
        $type = $this->roomType()->first();
        if (! $type) {
            return;
        }

        $this->update([
            'room_type' => $type->name,
            'monthly_rate' => $type->wholeRoomRate($this->beds()->count()),
        ]);
    }

    /**
     * Each bed's share of this room's [utilities, wifi] -- split by bed
     * count, same as rent in perBedRate(), so every tenant's share stays
     * fixed no matter how full the room is.
     */
    public function utilityShares(): array
    {
        $bedCount = $this->relationLoaded('beds') ? $this->beds->count() : $this->beds()->count();
        $divisor = max($bedCount, 1);

        return [
            round((float) $this->monthly_utility_cost / $divisor, 2),
            round((float) $this->monthly_wifi_cost / $divisor, 2),
        ];
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
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