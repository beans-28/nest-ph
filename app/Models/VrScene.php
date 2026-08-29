<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VrScene extends Model
{
    protected $fillable = [
        'room_id',
        'title',
        'panorama_path',
        'is_default',
        'sort_order',
        'haov',
        'vaov',
        'v_offset',
        'is_partial',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_partial' => 'boolean',
        'haov' => 'float',
        'vaov' => 'float',
        'v_offset' => 'float',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Hotspots placed inside this scene (arrows leading somewhere else).
     */
    public function hotspots(): HasMany
    {
        return $this->hasMany(VrHotspot::class, 'vr_scene_id');
    }

    /**
     * Hotspots in OTHER scenes that point at this one. Needed so deleting a
     * scene also clears the arrows leading to it — otherwise the tour would
     * have dead links pointing at a scene that no longer exists.
     */
    public function incomingHotspots(): HasMany
    {
        return $this->hasMany(VrHotspot::class, 'target_scene_id');
    }
}