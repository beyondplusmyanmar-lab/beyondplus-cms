<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Blocks access when the JSON API is turned off on the admin Configuration page.
 * Used to gate the API documentation so it tracks the same api_enabled toggle
 * as the API itself.
 */
class ApiEnabled
{
    public function handle($request, Closure $next)
    {
        try {
            if (bp_option('api_enabled', 'yes') === 'no') {
                return response(
                    'The API is currently disabled. Enable it on the admin Configuration page.',
                    503
                )->header('Content-Type', 'text/plain');
            }
        } catch (\Throwable $e) {
            // DB unavailable — allow access rather than hard-failing.
        }

        return $next($request);
    }
}
