<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

/**
 * One admin sign-in, shown in the Login Tracker on the Admin Privileges
 * page. Tenants are never recorded here.
 */
class AdminLoginSession extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'session_id', 'ip_address', 'user_agent', 'logged_in_at', 'logged_out_at'];

    protected $casts = [
        'logged_in_at' => 'datetime',
        'logged_out_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Call right after a successful login (after the session id is regenerated). */
    public static function recordLogin(?User $user, Request $request): void
    {
        if ($user?->role?->role_name !== 'admin') {
            return;
        }

        static::create([
            'user_id' => $user->id,
            'session_id' => $request->session()->getId(),
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 512),
            'logged_in_at' => now(),
        ]);
    }

    /** Call just before Auth::logout(), while we still know who the user is. */
    public static function recordLogout(?User $user, Request $request): void
    {
        if ($user?->role?->role_name !== 'admin') {
            return;
        }

        // Close the row for this exact session; fall back to their newest
        // open row if the session id couldn't be matched.
        $open = static::where('user_id', $user->id)->whereNull('logged_out_at');
        $row = (clone $open)->where('session_id', $request->session()->getId())->latest('logged_in_at')->first()
            ?? $open->latest('logged_in_at')->first();

        $row?->update(['logged_out_at' => now()]);
    }

    /** Short "Chrome on Windows" style label from the browser's user agent. */
    public function deviceLabel(): string
    {
        $ua = (string) $this->user_agent;

        $browser = match (true) {
            str_contains($ua, 'Edg/') => 'Edge',
            str_contains($ua, 'OPR/') => 'Opera',
            str_contains($ua, 'Chrome/') => 'Chrome',
            str_contains($ua, 'Firefox/') => 'Firefox',
            str_contains($ua, 'Safari/') => 'Safari',
            default => 'Unknown browser',
        };

        $os = match (true) {
            str_contains($ua, 'Android') => 'Android',
            str_contains($ua, 'iPhone') || str_contains($ua, 'iPad') => 'iOS',
            str_contains($ua, 'Windows') => 'Windows',
            str_contains($ua, 'Mac OS') => 'macOS',
            str_contains($ua, 'Linux') => 'Linux',
            default => null,
        };

        return $os ? "$browser on $os" : $browser;
    }
}
