<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VrHotspot extends Model
{
    protected $fillable = [
        'vr_scene_id',
        'target_scene_id',
        'pitch',
        'yaw',
        'label',
    ];

    protected $casts = [
        'pitch' => 'float',
        'yaw' => 'float',
    ];

    /**
     * The scene this hotspot sits inside.
     */
    public function scene(): BelongsTo
    {
        return $this->belongsTo(VrScene::class, 'vr_scene_id');
    }

    /**
     * The scene this hotspot takes the visitor to.
     */
    public function targetScene(): BelongsTo
    {
        return $this->belongsTo(VrScene::class, 'target_scene_id');
    }
}