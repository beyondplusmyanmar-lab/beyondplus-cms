<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Locale
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        if ($request->method() === 'GET') {
            $segment = $request->segment(1);

            if (in_array($segment, config('app.locales'))) {

                app()->setLocale($request->segment(1));

            }

        }

        return $next($request);
    }
}
