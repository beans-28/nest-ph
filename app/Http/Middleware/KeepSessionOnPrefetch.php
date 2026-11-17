<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pages fetched ahead on hover (public/js/smooth-nav.js, browser "prefetch")
 * may never be shown. Laravel already skips them when remembering the
 * previous URL; this also keeps one-time flash messages ("Saved.", form
 * errors) for the page the person actually opens next.
 */
class KeepSessionOnPrefetch
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->prefetch() && $request->hasSession()) {
            $request->session()->reflash();
        }

        return $response;
    }
}
