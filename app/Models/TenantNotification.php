<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

/**
 * One item in a tenant's notification panel (the bell on every tenant page).
 * Create them with TenantNotification::send(), which never throws, so a
 * notification problem can never break the action that triggered it.
 */
class TenantNotification extends Model
{
    protected $fillable = ['tenant_id', 'type', 'title', 'body', 'link', 'dedupe_key', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Adds a notification. With a $dedupeKey, a second call with the same
     * key does nothing (used for automatic reminders).
     */
    public static function send(?int $tenantId, string $type, string $title, ?string $body = null, ?string $link = null, ?string $dedupeKey = null): void
    {
        if (! $tenantId) {
            return;
        }

        try {
            $attributes = compact('type', 'title', 'body', 'link') + ['tenant_id' => $tenantId];

            $dedupeKey
                ? static::firstOrCreate(['dedupe_key' => $dedupeKey], $attributes)
                : static::create($attributes);
        } catch (\Throwable $e) {
            Log::warning('[notifications] could not create tenant notification', [
                'tenant_id' => $tenantId,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function toClientArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'body' => $this->body,
            'link' => $this->link,
            'read' => $this->read_at !== null,
            'created_at' => $this->created_at?->toIso8601String(),
            'time_ago' => $this->created_at?->diffForHumans(),
        ];
    }
}
