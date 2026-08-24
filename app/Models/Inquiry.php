<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'contact_number',
        'email',
        'room_id',
        'message',
        'preferred_room_type',
        'dpa_consent',
        'status',
    ];

    protected $casts = [
        'dpa_consent' => 'boolean',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(LeaseContract::class);
    }
}