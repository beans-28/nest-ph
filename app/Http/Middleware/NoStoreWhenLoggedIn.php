<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Logged-in pages must not be kept by the browser. Without this, after
 * logging out on a shared computer, pressing Back could show the previous
 * person's dashboard or billing straight from the browser's memory.
 * Downloads (PDFs, exports) are left alone.
 */
class NoStoreWhenLoggedIn
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user()
            && ! $response instanceof BinaryFileResponse
            && ! $response instanceof StreamedResponse) {
            $response->headers->set('Cache-Control', 'no-store, private');
        }

        return $response;
    }
}
