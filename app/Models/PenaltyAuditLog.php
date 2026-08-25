<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenaltyAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'penalty_id',
        'action',
        'performed_by',
        'reason',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function penalty(): BelongsTo
    {
        return $this->belongsTo(Penalty::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
