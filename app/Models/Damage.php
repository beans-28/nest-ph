<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Damage extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'room_id',
        'bed_id',
        'description',
        'cost',
        'date_incurred',
        'photo_path',
        'created_by',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'date_incurred' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function penalty(): HasOne
    {
        return $this->hasOne(Penalty::class);
    }
}
