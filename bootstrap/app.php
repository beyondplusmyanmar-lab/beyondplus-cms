<?php

use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\ApiEnabled;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckApiToken;
use App\Http\Middleware\CustomerApiToken;
use App\Http\Middleware\FrontendMode;
use App\Http\Middleware\Language;
use App\Http\Middleware\Locale;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Support\Plugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        using: function () {
            // LOAD-BEARING: every route file below uses the string action form,
            // 'PostController@index'. Laravel dropped the implicit controller
            // namespace in 8.0, so the ->namespace('App\Http\Controllers') calls
            // here are the only reason those strings resolve to a class at all.
            // Remove one and that entire group stops routing at once rather than
            // degrading — convert its route file to [Controller::class, 'method']
            // arrays first, then drop the namespace() call.

            // API routes (stateless, token-guarded).
            Route::prefix('api')
                ->middleware('api')
                ->namespace('App\Http\Controllers')
                ->group(base_path('routes/api.php'));

            // Base web routes.
            Route::middleware('web')
                ->namespace('App\Http\Controllers')
                ->group(base_path('routes/web.php'));

            // Routes shipped by active plugins. Registered BEFORE the CMS routes
            // so a plugin can own a front-end URL (e.g. /shop): the CMS ends with
            // a single-segment catch-all "/{name}", and first-registered wins, so
            // plugin pages would otherwise be shadowed by it. Plugins declare
            // distinct paths, so they don't collide with the specific CMS routes.
            Plugin::bootRoutes();

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
        // Trust proxies only when explicitly configured (behind nginx / a load
        // balancer / Cloudflare) so the real client IP is used for rate limiting
        // and logging. Off by default to avoid X-Forwarded-* spoofing on direct
        // connections. Set TRUSTED_PROXIES=* or a comma list of IPs/CIDRs.
        $proxies = env('TRUSTED_PROXIES');
        if ($proxies !== null && $proxies !== '') {
            $middleware->trustProxies(
                at: $proxies === '*' ? '*' : array_map('trim', explode(',', $proxies)),
                headers: Request::HEADER_X_FORWARDED_FOR
                    | Request::HEADER_X_FORWARDED_HOST
                    | Request::HEADER_X_FORWARDED_PORT
                    | Request::HEADER_X_FORWARDED_PROTO
                    | Request::HEADER_X_FORWARDED_AWS_ELB
            );
        }

        // Append locale detection to the default web stack.
        $middleware->web(append: [
            Locale::class,
        ]);

        // Admin area: full web stack plus admin auth + language.
        $middleware->group('admins', [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            ValidateCsrfToken::class,
            SubstituteBindings::class,
            AdminAuth::class,
            Language::class,
        ]);

        // API: throttled and token-checked.
        $middleware->group('api', [
            'throttle:60,1',
            CheckApiToken::class,
            SubstituteBindings::class,
        ]);

        $middleware->alias([
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,
            'customer.token' => CustomerApiToken::class,
            'frontend.mode' => FrontendMode::class,
            'api.enabled' => ApiEnabled::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Always return JSON for API requests (rate limits, 404s, validation, etc.)
        // so SPA clients never receive an HTML error page.
        $exceptions->shouldRenderJsonWhen(
            fn ($request, $e) => $request->is('api/*') || $request->expectsJson()
        );
    })->create();
