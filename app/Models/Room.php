<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['room_no', 'floor', 'room_type', 'monthly_rate', 'status'];

    protected $casts = [
        'monthly_rate' => 'decimal:2',
    ];

    const STATUSES = ['available', 'full', 'maintenance'];

    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }

    /**
     * Keep `status` in sync with the beds underneath it.
     * - Any vacant bed  -> "available"
     * - No vacant beds  -> "full"
     * A room manually marked "maintenance" is left alone; unset it explicitly
     * (e.g. via the edit form) to let it resume auto-syncing.
     */
    public function syncStatusFromBeds(): void
    {
        if ($this->status === 'maintenance') {
            return;
        }

        $hasVacant = $this->beds()->where('status', 'vacant')->exists();

        $this->status = $hasVacant ? 'available' : 'full';
        $this->save();
    }
}
