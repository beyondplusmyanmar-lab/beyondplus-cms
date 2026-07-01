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
        $segment = "m";

        if($request->segment(2)) {
                $segment = $request->segment(2);
        }

        // Public CMS content endpoints (/api/m/*) are intentionally unauthenticated.
        if($segment == "m") {
            return $next($request);
        }

        // All other API endpoints require a valid token.
        if ($request->hasHeader('X-Trident-Token')) {
            $XTridentToken = trim($request->header('X-Trident-Token'));

            if($XTridentToken !== '' && Apikeytable::where('api_token', $XTridentToken)->exists()){
                return $next($request);
            }
        }

        return response()->json([ "data" => [ "status" => 401] ], 401);
    }
}
