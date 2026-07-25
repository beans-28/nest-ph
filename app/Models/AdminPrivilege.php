<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminPrivilege extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'privilege_name', 'granted_at'];

    protected $casts = [
        'granted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
