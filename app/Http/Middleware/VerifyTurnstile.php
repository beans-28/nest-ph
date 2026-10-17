<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cloudflare Turnstile bot check for public forms (login, apply, inquiry, ...).
 *
 * The page's Turnstile widget gives the browser a one-time token, which the
 * page's JavaScript sends as "cf_turnstile_response". We ask Cloudflare if
 * the token is real; if not, the request is stopped here before the
 * controller runs.
 *
 * If TURNSTILE_SECRET_KEY is not set (e.g. in tests), the check is skipped.
 */
class VerifyTurnstile
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.turnstile.secret_key');

        if (! $secret) {
            if (app()->isProduction()) {
                Log::warning('Turnstile is not configured: TURNSTILE_SECRET_KEY is empty, bot check skipped.');
            }

            return $next($request);
        }

        $token = (string) $request->input('cf_turnstile_response', '');

        if ($token === '' || ! $this->passes($token, $secret, $request->ip())) {
            return response()->json([
                'message' => 'Please complete the security check and try again.',
                'errors' => ['cf_turnstile_response' => ['Please complete the security check and try again.']],
            ], 422);
        }

        return $next($request);
    }

    private function passes(string $token, string $secret, ?string $ip): bool
    {
        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $ip,
                ]);

            return $response->ok() && $response->json('success') === true;
        } catch (\Throwable $e) {
            Log::error('Turnstile verification failed: ' . $e->getMessage());

            return false;
        }
    }
}
