<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        using: function () {
            // API routes (stateless, token-guarded).
            Route::prefix('api')
                ->middleware('api')
                ->namespace('App\Http\Controllers')
                ->group(base_path('routes/api.php'));

            // Base web routes.
            Route::middleware('web')
                ->namespace('App\Http\Controllers')
                ->group(base_path('routes/web.php'));

            // Locale-prefixed CMS routes. "mm" is the un-prefixed default and
            // must be registered LAST, otherwise its catch-all "/{name}" route
            // shadows the prefixed routes (e.g. /en).
            $locales = collect(config('app.locales'))
                ->sortBy(fn ($locale) => $locale === 'mm' ? 1 : 0);

            foreach ($locales as $locale) {
                Route::middleware('web')
                    ->prefix($locale === 'mm' ? '' : $locale)
                    ->namespace('App\Http\Controllers')
                    ->group(base_path('routes/beyondplus-cms.php'));
            }
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Append locale detection to the default web stack.
        $middleware->web(append: [
            \App\Http\Middleware\Locale::class,
        ]);

        // Admin area: full web stack plus admin auth + language.
        $middleware->group('admins', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\AdminAuth::class,
            \App\Http\Middleware\Language::class,
        ]);

        // API: throttled and token-checked.
        $middleware->group('api', [
            'throttle:60,1',
            \App\Http\Middleware\CheckApiToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'customer.token' => \App\Http\Middleware\CustomerApiToken::class,
            'frontend.mode' => \App\Http\Middleware\FrontendMode::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Always return JSON for API requests (rate limits, 404s, validation, etc.)
        // so SPA clients never receive an HTML error page.
        $exceptions->shouldRenderJsonWhen(
            fn ($request, $e) => $request->is('api/*') || $request->expectsJson()
        );
    })->create();
