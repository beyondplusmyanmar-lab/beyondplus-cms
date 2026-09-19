<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use ReflectionMethod;
use Tests\TestCase;

/**
 * Every route that names a controller action must resolve to a public method.
 *
 * Routes are registered from files, but the links that reach them are largely
 * DB-driven in this CMS (bp_modules for the admin sidebar, bp_menus and the
 * "/{name}" catch-all for the front end). That means a route pointing at a
 * method which does not exist cannot be ruled out by grepping templates: a
 * single admin-editable row can start sending traffic at it. Laravel does not
 * validate the action until dispatch, so the failure surfaces as a 500
 * (BadMethodCallException) rather than anything visible at boot or in
 * route:list.
 *
 * This guard turns that whole class of defect into a test failure.
 */
class RouteIntegrityTest extends TestCase
{
    public function test_every_controller_route_resolves_to_a_public_method(): void
    {
        $broken = [];

        foreach (Route::getRoutes() as $route) {
            $uses = $route->getAction('uses');

            if (! is_string($uses) || ! str_contains($uses, '@')) {
                continue; // closure route
            }

            [$class, $method] = explode('@', $uses, 2);
            $where = $route->methods()[0].' /'.$route->uri();

            if (! class_exists($class)) {
                $broken[] = "$where -> $class (class does not exist)";

                continue;
            }

            if (! method_exists($class, $method)) {
                $broken[] = "$where -> {$class}::{$method}() (method does not exist)";

                continue;
            }

            if (! (new ReflectionMethod($class, $method))->isPublic()) {
                $broken[] = "$where -> {$class}::{$method}() (method is not public)";
            }
        }

        $this->assertSame([], $broken, "Routes pointing at unresolvable actions:\n  ".implode("\n  ", $broken));
    }

    public function test_the_guard_actually_inspects_routes(): void
    {
        // Non-vacuity: if route collection ever came back empty, the assertion
        // above would pass while checking nothing at all.
        $controllerRoutes = 0;

        foreach (Route::getRoutes() as $route) {
            $uses = $route->getAction('uses');
            if (is_string($uses) && str_contains($uses, '@')) {
                $controllerRoutes++;
            }
        }

        $this->assertGreaterThan(300, $controllerRoutes);
    }

    /**
     * A plugin's routes.php is loaded only while routes are being REGISTERED. Once
     * `route:cache` has run - as it does on every installed storefront - the file is
     * never required again, so a function declared in it simply does not exist and
     * any route that calls it is a 500. Tests register routes the uncached way, so
     * only a structural check can see this.
     */
    public function test_no_plugin_route_file_declares_a_function(): void
    {
        $files = glob(base_path('plugins/*/routes.php'));
        $declared = [];

        foreach ($files as $file) {
            $tokens = token_get_all(file_get_contents($file));

            foreach ($tokens as $i => $token) {
                if (! is_array($token) || $token[0] !== T_FUNCTION) {
                    continue;
                }
                $next = $tokens[$i + 1] ?? null;
                $name = $tokens[$i + 2] ?? null;
                if (is_array($next) && $next[0] === T_WHITESPACE && is_array($name) && $name[0] === T_STRING) {
                    $declared[] = str_replace(base_path().'/', '', $file).': '.$name[1].'() on line '.$name[2];
                }
            }
        }

        $this->assertNotEmpty($files, 'no plugin route files were found - the guard inspected nothing');
        $this->assertSame([], $declared, "Declare these in the plugin's main file instead:\n  ".implode("\n  ", $declared));
    }
}
