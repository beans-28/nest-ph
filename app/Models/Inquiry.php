<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'contact_number',
        'email',
        'message',
        'preferred_room_type',
        'status',
    ];

    public function contracts(): HasMany
    {
        return $this->hasMany(LeaseContract::class);
    }
}
