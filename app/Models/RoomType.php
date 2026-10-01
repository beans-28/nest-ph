<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A kind of room the dorm offers and its monthly price, set up in the
 * Dormitory Profile (e.g. "Room with AC, 4 persons - P4,200 per bed").
 * Every dorm lists its own, since not all dorms have the same room types.
 *
 * pricing_mode decides what monthly_rate means:
 *   - per_bed:  each tenant pays monthly_rate (most dorm rooms)
 *   - per_room: the whole room costs monthly_rate, split across its beds
 *               (e.g. a solo room, where the one tenant pays it all)
 */
class RoomType extends Model
{
    public const PRICING_MODES = ['per_bed', 'per_room'];

    protected $fillable = [
        'name',
        'location',
        'description',
        'pricing_mode',
        'monthly_rate',
        'min_capacity',
        'max_capacity',
        'has_aircon',
        'sort_order',
    ];

    protected $casts = [
        'monthly_rate' => 'decimal:2',
        'has_aircon' => 'boolean',
    ];

    public static function ordered()
    {
        return static::orderBy('sort_order')->orderBy('id')->get();
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /** What one tenant pays per month in a room of this type with $bedCount beds. */
    public function perBedRate(int $bedCount): float
    {
        if ($this->pricing_mode === 'per_room') {
            return round((float) $this->monthly_rate / max($bedCount, 1), 2);
        }

        return (float) $this->monthly_rate;
    }

    /** The whole room's monthly total, kept on rooms.monthly_rate for sorting and reports. */
    public function wholeRoomRate(int $bedCount): float
    {
        return $this->pricing_mode === 'per_room'
            ? (float) $this->monthly_rate
            : round((float) $this->monthly_rate * max($bedCount, 1), 2);
    }

    /** "P4,200 per bed" / "P4,500 per room" */
    public function priceLabel(): string
    {
        return '₱' . number_format((float) $this->monthly_rate, 0) . ($this->pricing_mode === 'per_room' ? ' per room' : ' per bed');
    }

    /** "4 persons", "10-16 persons" or null. */
    public function capacityLabel(): ?string
    {
        if (! $this->min_capacity && ! $this->max_capacity) {
            return null;
        }
        if ($this->min_capacity && $this->max_capacity && $this->min_capacity !== $this->max_capacity) {
            return "{$this->min_capacity}–{$this->max_capacity} persons";
        }
        $n = $this->max_capacity ?: $this->min_capacity;

        return $n . ($n == 1 ? ' person' : ' persons');
    }

    public function toClientArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'location' => $this->location,
            'description' => $this->description,
            'pricing_mode' => $this->pricing_mode,
            'monthly_rate' => (float) $this->monthly_rate,
            'min_capacity' => $this->min_capacity,
            'max_capacity' => $this->max_capacity,
            'has_aircon' => $this->has_aircon,
            'price_label' => $this->priceLabel(),
            'capacity_label' => $this->capacityLabel(),
            'rooms_count' => $this->rooms_count ?? $this->rooms()->count(),
        ];
    }
}
