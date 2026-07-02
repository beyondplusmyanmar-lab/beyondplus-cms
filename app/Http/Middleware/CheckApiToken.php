<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Apikeytable;

class CheckApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Respect the "API enabled" configuration toggle (defaults to enabled).
        if (bp_option('api_enabled', 'yes') === 'no') {
            return response()->json([ "data" => [ "status" => 503, "message" => "API disabled" ] ], 503);
        }

        $segment = "m";

        if($request->segment(2)) {
                $segment = $request->segment(2);
        }

        // Public CMS content endpoints (/api/m/*) are intentionally unauthenticated.
        if($segment == "m") {
            return $next($request);
        }

        // All other API endpoints require a valid token.
        if ($request->hasHeader('X-BP-Token')) {
            $token = trim($request->header('X-BP-Token'));

            if($token !== '' && Apikeytable::where('api_token', $token)->exists()){
                return $next($request);
            }
        }

        return response()->json([ "data" => [ "status" => 401] ], 401);
    }
}
