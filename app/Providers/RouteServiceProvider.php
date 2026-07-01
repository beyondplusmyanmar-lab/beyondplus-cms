<?php

namespace App\Providers;

/**
 * Routing itself is configured in bootstrap/app.php (withRouting).
 * This class is retained only to expose the "home" route path used by
 * the guest/redirect middleware.
 */
class RouteServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';
}
