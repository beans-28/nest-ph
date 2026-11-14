<?php

namespace App\Http\Middleware;

use App\Models\DormitoryProfile;
use App\Support\BrandTheme;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds the dorm's chosen color palette to every HTML page, just before
 * </head>, so it wins over each page's own default (green) colors.
 * Doing it here means no page has to remember to include it.
 *
 * Skipped for the NEST.PH platform pages: those are our brand, not the dorm's.
 */
class InjectBrandTheme
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->routeIs('platform*', 'login.platform')
            || ! str_contains((string) $response->headers->get('Content-Type'), 'text/html')
            || $response->getStatusCode() >= 300 && $response->getStatusCode() < 400) {
            return $response;
        }

        $content = $response->getContent();
        if (! is_string($content) || ! str_contains($content, '</head>')) {
            return $response;
        }

        try {
            $style = BrandTheme::styleTag(DormitoryProfile::current()->brand_color);
        } catch (\Throwable $e) {
            return $response; // DB down: keep the default colors, never break the page.
        }

        if ($style !== '') {
            $pos = strripos($content, '</head>');
            $response->setContent(substr_replace($content, $style, $pos, 0));
        }

        return $response;
    }
}
