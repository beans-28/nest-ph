<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnouncementComment extends Model
{
    public $timestamps = false;

    protected $fillable = ['announcement_id', 'user_id', 'tenant_id', 'body', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function getAuthorNameAttribute(): string
    {
        if ($this->user_id) {
            return $this->user?->name ?? 'NEST PH Admin';
        }

        return $this->tenant?->full_name ?? '(no name on record)';
    }

    public function getAuthorInitialsAttribute(): string
    {
        return Announcement::initialsFromName($this->authorName);
    }

    public function getIsAdminAttribute(): bool
    {
        return (bool) $this->user_id;
    }
}