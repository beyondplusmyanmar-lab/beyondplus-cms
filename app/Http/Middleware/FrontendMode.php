<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Controls what the public front-end serves, per the `frontend_mode` option
 * on the admin Configuration page:
 *   - theme    : render the server theme (default)
 *   - spa      : redirect to the configured SPA URL (headless front-end)
 *   - headless : redirect to the SPA URL if set, else show an API-only notice
 *
 * Only affects the public front-end group; bp-admin and /api are untouched.
 */
class FrontendMode
{
    public function handle($request, Closure $next)
    {
        // The admin panel and the customer auth pages are always server-rendered,
        // even in SPA / headless mode — never redirect them.
        $path = preg_replace('#^[a-z]{2}/#', '', ltrim($request->path(), '/'));
        if (str_starts_with($path, 'bp-admin') || str_starts_with($path, 'customer')) {
            return $next($request);
        }

        try {
            $mode = bp_option('frontend_mode', 'theme');

            if ($mode === 'spa' || $mode === 'headless') {
                $spaUrl = trim((string) bp_option('spa_url', ''));

                if ($spaUrl !== '') {
                    return redirect()->away($spaUrl);
                }

                if ($mode === 'headless') {
                    return response('This site is served headlessly through its API.', 200)
                        ->header('Content-Type', 'text/plain');
                }
                // spa mode without a URL configured — fall back to the theme.
            }
        } catch (\Throwable $e) {
            // DB unavailable or misconfigured — render the theme normally.
        }

        return $next($request);
    }
}
