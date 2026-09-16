<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Announcement extends Model
{
    protected $fillable = ['user_id', 'body', 'comments_restricted'];

    protected $casts = [
        'comments_restricted' => 'boolean',
    ];

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(AnnouncementComment::class)->oldest();
    }

    public function getPosterNameAttribute(): string
    {
        return $this->poster?->name ?? 'NEST PH Admin';
    }

    public function getPosterInitialsAttribute(): string
    {
        return self::initialsFromName($this->posterName);
    }

    public static function initialsFromName(string $name): string
    {
        $words = array_filter(explode(' ', trim($name)));
        $initials = array_map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)), array_slice($words, 0, 2));

        return $initials ? implode('', $initials) : 'A';
    }
}