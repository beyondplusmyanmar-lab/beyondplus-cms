<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class Language
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
        if (Session::has('applocale')) {
            App::setLocale(Session::get('applocale'));
        } else {
            // Default the admin to the app's primary locale (app.locale, e.g. mm),
            // matching the front-end, until the admin picks a language via the
            // EN / မြန်မာ toggle (their choice then persists in the session).
            App::setLocale(Config::get('app.locale'));
            Session::put('applocale', Config::get('app.locale'));
        }
        return $next($request);
    }
}