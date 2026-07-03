<?php

namespace App\Support;

use App\Models\Bp_options;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/**
 * A small hook-based plugin system (actions & filters).
 *
 * - Plugins live in the /plugins directory, one folder each, with a plugin.json
 *   manifest and a main PHP file that registers hooks.
 * - Active plugins are stored in the `active_plugins` option (a JSON array of
 *   slugs) and their main files are require()d on boot.
 * - Plugins extend the app through hooks: actions (side effects) and filters
 *   (value transforms), via the bp_add_action/bp_do_action/bp_add_filter/
 *   bp_apply_filters helpers.
 */
class Plugin
{
    protected static array $actions = [];
    protected static array $filters = [];

    // ---- hooks -----------------------------------------------------------

    public static function addAction(string $hook, callable $cb, int $priority = 10): void
    {
        self::$actions[$hook][] = ['cb' => $cb, 'priority' => $priority];
    }

    public static function doAction(string $hook, ...$args): void
    {
        foreach (self::sorted(self::$actions[$hook] ?? []) as $h) {
            ($h['cb'])(...$args);
        }
    }

    public static function addFilter(string $hook, callable $cb, int $priority = 10): void
    {
        self::$filters[$hook][] = ['cb' => $cb, 'priority' => $priority];
    }

    public static function applyFilters(string $hook, $value, ...$args)
    {
        foreach (self::sorted(self::$filters[$hook] ?? []) as $h) {
            $value = ($h['cb'])($value, ...$args);
        }
        return $value;
    }

    protected static function sorted(array $hooks): array
    {
        usort($hooks, fn ($a, $b) => $a['priority'] <=> $b['priority']);
        return $hooks;
    }

    // ---- plugin registry -------------------------------------------------

    public static function path(): string
    {
        return base_path('plugins');
    }

    /** Discover every installed plugin (from its folder + manifest). */
    public static function all(): array
    {
        $active = self::active();
        $plugins = [];

        foreach (glob(self::path().'/*', GLOB_ONLYDIR) as $dir) {
            $slug = basename($dir);
            $meta = [];
            if (is_file($dir.'/plugin.json')) {
                $meta = json_decode(file_get_contents($dir.'/plugin.json'), true) ?: [];
            }

            $plugins[$slug] = [
                'slug'        => $slug,
                'name'        => $meta['name'] ?? ucfirst($slug),
                'description' => $meta['description'] ?? 'No description provided.',
                'version'     => $meta['version'] ?? '1.0.0',
                'author'      => $meta['author'] ?? '',
                'main'        => $meta['main'] ?? $slug.'.php',
                'active'      => in_array($slug, $active, true),
                'migrations'  => is_dir($dir.'/migrations'),
            ];
        }

        ksort($plugins);
        return $plugins;
    }

    /** A plugin's manifest (plugin.json) as an array. */
    public static function meta(string $slug): array
    {
        $file = self::path().'/'.basename($slug).'/plugin.json';
        return is_file($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
    }

    /** Slugs of the active plugins. */
    public static function active(): array
    {
        try {
            $list = json_decode(bp_option('active_plugins', '[]'), true);
            return is_array($list) ? $list : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    protected static function setActive(array $slugs): void
    {
        Bp_options::updateOrCreate(
            ['option_name' => 'active_plugins'],
            ['option_value' => json_encode(array_values(array_unique($slugs))), 'autoload' => 'yes']
        );
    }

    public static function activate(string $slug): void
    {
        // basename() guards the slug against path traversal.
        $slug = basename($slug);
        if (is_dir(self::path().'/'.$slug)) {
            $active = self::active();
            $active[] = $slug;
            self::setActive($active);

            // Run the plugin's own migrations (creates its tables). Laravel's
            // migration history means already-run migrations are skipped, so a
            // plugin update only runs its new migrations.
            self::migrate($slug);

            // Register its admin menu (sidebar link + access) if declared.
            self::registerMenu($slug);
        }
    }

    /** Deactivate the plugin — its data / tables are kept (use uninstall to remove). */
    public static function deactivate(string $slug): void
    {
        self::setActive(array_filter(self::active(), fn ($s) => $s !== basename($slug)));
        self::unregisterMenu($slug);
    }

    /**
     * Register a plugin's admin page in the sidebar (bp_modules) and grant the
     * same roles that can see the Plugins page access to it (bp_access). Driven
     * by the optional "admin_menu" object in plugin.json.
     */
    protected static function registerMenu(string $slug): void
    {
        $menu = self::meta($slug)['admin_menu'] ?? null;
        if (! $menu) {
            return;
        }
        $link = $menu['link'] ?? $slug;
        if (DB::table('bp_modules')->where('module_link', $link)->exists()) {
            return;
        }

        $moduleId = DB::table('bp_modules')->insertGetId([
            'module_name'    => $menu['title'] ?? ucfirst($slug),
            'module_name_mm' => $menu['title'] ?? ucfirst($slug),
            'module_link'    => $link,
            'module_weight'  => $menu['weight'] ?? 99,
            'module_icon'    => $menu['icon'] ?? 'fa fa-plug',
            'parent_id'      => $menu['parent'] ?? 8,
            'section'        => 1,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // Copy the Plugins page's access grants so the same roles can reach it.
        $ref = DB::table('bp_modules')->where('module_link', 'plugins')->first();
        if ($ref) {
            foreach (DB::table('bp_access')->where('module_id', $ref->module_id)->get() as $a) {
                DB::table('bp_access')->insert([
                    'module_id' => $moduleId,
                    'usertype'  => $a->usertype,
                    'canshow'   => $a->canshow,
                    'cancreate' => $a->cancreate,
                    'canedit'   => $a->canedit,
                    'candelete' => $a->candelete,
                ]);
            }
        }
    }

    /** Remove a plugin's admin menu module + access rows. */
    protected static function unregisterMenu(string $slug): void
    {
        $menu = self::meta($slug)['admin_menu'] ?? null;
        if (! $menu) {
            return;
        }
        $link = $menu['link'] ?? $slug;
        $module = DB::table('bp_modules')->where('module_link', $link)->first();
        if ($module) {
            DB::table('bp_access')->where('module_id', $module->module_id)->delete();
            DB::table('bp_modules')->where('module_id', $module->module_id)->delete();
        }
    }

    /** True if the plugin ships a migrations/ directory. */
    public static function hasMigrations(string $slug): bool
    {
        return is_dir(self::path().'/'.basename($slug).'/migrations');
    }

    /** Run a plugin's pending migrations (up). */
    public static function migrate(string $slug): void
    {
        $slug = basename($slug);
        if (self::hasMigrations($slug)) {
            Artisan::call('migrate', ['--path' => 'plugins/'.$slug.'/migrations', '--force' => true]);
        }
    }

    /**
     * Uninstall a plugin: deactivate it, roll back its migrations (dropping its
     * tables), then run an optional uninstall.php for any remaining cleanup.
     * This is deliberately separate from deactivate(), which keeps data.
     */
    public static function uninstall(string $slug): void
    {
        $slug = basename($slug);
        self::deactivate($slug);

        if (self::hasMigrations($slug)) {
            Artisan::call('migrate:rollback', ['--path' => 'plugins/'.$slug.'/migrations', '--force' => true]);
        }

        $script = self::path().'/'.$slug.'/uninstall.php';
        if (is_file($script)) {
            require $script;
        }
    }

    /**
     * Boot every active plugin: register its view namespace (view('<slug>::x'))
     * and load its main file so it can register hooks. Plugin routes are loaded
     * separately during routing (see bootstrap/app.php).
     */
    public static function boot(): void
    {
        try {
            foreach (self::all() as $plugin) {
                if (! $plugin['active']) {
                    continue;
                }
                $dir = self::path().'/'.$plugin['slug'];

                if (is_dir($dir.'/views')) {
                    \Illuminate\Support\Facades\View::addNamespace($plugin['slug'], $dir.'/views');
                }

                $main = $dir.'/'.basename($plugin['main']);
                if (is_file($main)) {
                    require_once $main;
                }
            }
        } catch (\Throwable $e) {
            // DB not ready (pre-migration) or a bad plugin — don't break the app.
        }
    }

    /**
     * Load the route files of every active plugin (called during routing).
     * Plugins declare their own middleware ('web' for front-end pages, 'admins'
     * for admin pages), so the loader must not wrap them in another group.
     */
    public static function bootRoutes(): void
    {
        try {
            foreach (self::active() as $slug) {
                $routeFile = self::path().'/'.basename($slug).'/routes.php';
                if (is_file($routeFile)) {
                    \Illuminate\Support\Facades\Route::namespace('App\Http\Controllers')
                        ->group($routeFile);
                }
            }
        } catch (\Throwable $e) {
            // DB not ready or bad route file — skip plugin routes.
        }
    }
}
