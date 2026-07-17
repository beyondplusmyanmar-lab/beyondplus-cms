<?php

namespace App\Support;

use App\Models\Bp_options;
use App\Support\PackageGuard;
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
    /** CMS version plugins declare compatibility against (minCmsVersion). */
    public const CMS_VERSION = '2.5.0';

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
        $mm = app()->getLocale() === 'mm';
        $plugins = [];

        foreach (glob(self::path().'/*', GLOB_ONLYDIR) as $dir) {
            $slug = basename($dir);
            $meta = [];
            if (is_file($dir.'/plugin.json')) {
                $meta = json_decode(file_get_contents($dir.'/plugin.json'), true) ?: [];
            }

            $isActive = in_array($slug, $active, true);
            $version   = $meta['version'] ?? '1.0.0';
            $installed = self::installedVersion($slug);
            $updateAvailable = $isActive && $installed && version_compare($version, $installed, '>');
            $plugins[$slug] = [
                'slug'         => $slug,
                'id'           => $meta['id'] ?? $slug,
                'type'         => $meta['type'] ?? 'plugin',
                'name'         => $meta['name'] ?? ucfirst($slug),
                'category'     => $meta['category'] ?? 'General',
                'description'  => ($mm && ! empty($meta['description_mm']))
                    ? $meta['description_mm']
                    : ($meta['description'] ?? 'No description provided.'),
                'version'          => $version,
                'installed_version'=> $installed,
                'update_available' => $updateAvailable,
                'author'       => $meta['author'] ?? '',
                'homepage'     => $meta['homepage'] ?? '',
                'license'      => $meta['license'] ?? '',
                'minCmsVersion'=> $meta['minCmsVersion'] ?? '',
                'main'         => $meta['main'] ?? $slug.'.php',
                'active'       => $isActive,
                'migrations'   => is_dir($dir.'/migrations'),
                // A version bump legitimately changes files, so show "update"
                // rather than "modified" in that case.
                'tampered'     => $isActive && ! $updateAvailable && self::isTampered($slug),
                'settings'     => ! empty($meta['settings']),
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

    /** A plugin's declared settings fields (from plugin.json "settings"). */
    public static function settingsSchema(string $slug): array
    {
        $settings = self::meta($slug)['settings'] ?? [];
        return is_array($settings) ? $settings : [];
    }

    /** Option key a plugin's setting is stored under. */
    public static function settingKey(string $slug, string $name): string
    {
        return 'plugin.'.basename($slug).'.'.$name;
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

    /**
     * Activate a plugin. Returns a result array:
     *  - ['blocked' => true, 'scan' => [...]] if the security scan found
     *    high-risk code (the plugin is NOT activated), or
     *  - ['activated' => true, 'scan' => [...]] on success (scan warnings, if
     *    any, are informational).
     */
    public static function activate(string $slug): array
    {
        // basename() guards the slug against path traversal.
        $slug = basename($slug);
        if (! is_dir(self::path().'/'.$slug)) {
            return ['blocked' => true, 'scan' => ['critical' => [['file' => $slug, 'reason' => 'plugin not found']], 'warning' => []]];
        }

        // Compatibility gate: don't install a plugin this environment can't run.
        $problems = self::checkRequirements($slug);
        // Dependency gate: every required plugin must already be active, so a
        // package never boots against a capability that is not there.
        foreach (self::missingDependencies($slug) as $dep) {
            $problems[] = "needs plugin: {$dep} (activate it first)";
        }
        if ($problems) {
            return ['blocked' => true, 'requirements' => $problems];
        }

        // Security gate: never load a plugin whose code contains high-risk
        // constructs (arbitrary code execution, shell access, obfuscation, …).
        $scan = self::scan($slug);
        if (! empty($scan['critical'])) {
            \Illuminate\Support\Facades\Log::warning("Plugin activation BLOCKED by security scan: {$slug}", $scan['critical']);
            return ['blocked' => true, 'scan' => $scan];
        }

        $active = self::active();
        $active[] = $slug;
        self::setActive($active);

        // Run the plugin's own migrations (creates its tables). Laravel's
        // migration history means already-run migrations are skipped, so a
        // plugin update only runs its new migrations.
        self::migrate($slug);

        // Register its admin menu (sidebar link + access) if declared.
        self::registerMenu($slug);

        // Record an integrity baseline + the installed version, clear any failure.
        self::storeFingerprint($slug);
        self::setInstalledVersion($slug, self::meta($slug)['version'] ?? '1.0.0');
        self::clearFailure($slug);

        self::audit('activated', $slug);
        return ['activated' => true, 'scan' => $scan];
    }

    /**
     * Static security scan of a plugin's PHP files. Returns
     * ['critical' => [...], 'warning' => [...]] where each entry is
     * ['file' => ..., 'reason' => ...]. Critical matches block activation;
     * warnings are surfaced but allowed. This is a heuristic safety net, not a
     * sandbox — only install plugins from sources you trust.
     */
    public static function scan(string $slug): array
    {
        return PackageGuard::scan(self::path().'/'.basename($slug));
    }

    /** Audit-log a plugin lifecycle action with the acting admin. */
    protected static function audit(string $action, string $slug): void
    {
        $who = optional(auth('admins')->user())->email ?? 'system';
        \Illuminate\Support\Facades\Log::info("Plugin {$action}: {$slug} (by {$who})");

        // Also record on the dashboard activity feed (best-effort).
        try {
            activity('plugin')
                ->causedBy(auth('admins')->user())
                ->log(sprintf('%s the plugin “%s”', $action, $slug));
        } catch (\Throwable $e) {
            // activity_log unavailable (e.g. pre-migration) — the Log line stands.
        }
    }

    // ---- dependency / compatibility -------------------------------------

    /**
     * Check a plugin's declared requirements against this environment. Returns a
     * list of unmet requirements (empty = compatible). Reads the manifest's
     * minCmsVersion and requires{php, extensions}.
     */
    public static function checkRequirements(string $slug): array
    {
        return PackageGuard::checkRequirements(self::meta($slug), self::CMS_VERSION);
    }

    // ---- capabilities & inter-plugin dependencies -----------------------

    /**
     * The capability tokens a plugin declares it PROVIDES (manifest `capabilities`),
     * e.g. ["orders.create", "orders.read"]. Free-form strings; the CMS treats them
     * as opaque identifiers other packages can require.
     *
     * @return array<int,string>
     */
    public static function capabilitiesOf(string $slug): array
    {
        $caps = self::meta(basename($slug))['capabilities'] ?? [];

        return is_array($caps) ? array_values(array_filter(array_map('strval', $caps))) : [];
    }

    /**
     * The plugin ids a package declares it REQUIRES to be active. Accepts either
     * `requires.plugins: [...]` (alongside php/extensions) or a flat top-level
     * `requires: [...]` array (the shape themes use), so both manifest styles work.
     *
     * @param array<string,mixed>|null $meta Pass a theme's meta to reuse this for themes.
     * @return array<int,string>
     */
    public static function requiredPlugins(string $slug, ?array $meta = null): array
    {
        $meta ??= self::meta(basename($slug));
        $req = $meta['requires'] ?? [];
        // Flat array form: requires: ["doeh-commerce"].
        if (is_array($req) && array_is_list($req)) {
            $list = $req;
        } else {
            $list = is_array($req) && isset($req['plugins']) && is_array($req['plugins']) ? $req['plugins'] : [];
        }

        return array_values(array_filter(array_map(fn ($s) => basename((string) $s), $list)));
    }

    /**
     * Required plugin ids that are NOT currently active — the unmet dependencies
     * that must block activation (and boot). Empty = all satisfied.
     *
     * @param array<string,mixed>|null $meta
     * @return array<int,string>
     */
    public static function missingDependencies(string $slug, ?array $meta = null): array
    {
        $active = self::active();

        return array_values(array_diff(self::requiredPlugins($slug, $meta), $active));
    }

    /**
     * Active plugins that require $slug — i.e. would break if it were deactivated.
     *
     * @return array<int,string>
     */
    public static function dependents(string $slug): array
    {
        $slug = basename($slug);
        $out = [];
        foreach (self::active() as $other) {
            if ($other !== $slug && in_array($slug, self::requiredPlugins($other), true)) {
                $out[] = $other;
            }
        }

        return $out;
    }

    /**
     * Capability token => list of active plugin slugs providing it. A discovery
     * map so a theme or plugin can ask "who provides orders.create?".
     *
     * @return array<string,array<int,string>>
     */
    public static function capabilityRegistry(): array
    {
        $map = [];
        foreach (self::active() as $slug) {
            foreach (self::capabilitiesOf($slug) as $cap) {
                $map[$cap][] = $slug;
            }
        }

        return $map;
    }

    // ---- integrity (tamper detection) -----------------------------------

    /** A SHA-256 fingerprint over all the plugin's PHP files + its manifest. */
    public static function fingerprint(string $slug): string
    {
        return PackageGuard::fingerprint(self::path().'/'.basename($slug));
    }

    protected static function storeFingerprint(string $slug): void
    {
        $map = json_decode(bp_option('plugin_hashes', '{}'), true) ?: [];
        $map[$slug] = self::fingerprint($slug);
        Bp_options::updateOrCreate(
            ['option_name' => 'plugin_hashes'],
            ['option_value' => json_encode($map), 'autoload' => 'yes']
        );
    }

    /** True if an installed plugin's files changed since it was activated. */
    public static function isTampered(string $slug): bool
    {
        $map = json_decode(bp_option('plugin_hashes', '{}'), true) ?: [];
        return isset($map[$slug]) && $map[$slug] !== self::fingerprint($slug);
    }

    // ---- recovery mode --------------------------------------------------

    /** Auto-disable a plugin that failed to boot and record why. */
    protected static function recordFailure(string $slug, string $reason): void
    {
        self::setActive(array_filter(self::active(), fn ($s) => $s !== $slug));

        $failures = json_decode(bp_option('plugin_failures', '{}'), true) ?: [];
        $failures[$slug] = $reason;
        Bp_options::updateOrCreate(
            ['option_name' => 'plugin_failures'],
            ['option_value' => json_encode($failures), 'autoload' => 'yes']
        );
        \Illuminate\Support\Facades\Log::error("Plugin auto-disabled after boot failure: {$slug} — {$reason}");
    }

    /** Slug => reason for plugins auto-disabled by recovery mode. */
    public static function failures(): array
    {
        return json_decode(bp_option('plugin_failures', '{}'), true) ?: [];
    }

    protected static function clearFailure(string $slug): void
    {
        $failures = self::failures();
        if (isset($failures[$slug])) {
            unset($failures[$slug]);
            Bp_options::updateOrCreate(
                ['option_name' => 'plugin_failures'],
                ['option_value' => json_encode($failures), 'autoload' => 'yes']
            );
        }
    }

    /** Deactivate the plugin — its data / tables are kept (use uninstall to remove). */
    public static function deactivate(string $slug): void
    {
        $slug = basename($slug);
        // Dependency guard: refuse to pull a plugin out from under active packages
        // that require it (the controller warns first, but keep the invariant here
        // too so no caller can leave a dependent booting against a missing dep).
        if (self::dependents($slug)) {
            return;
        }
        self::setActive(array_filter(self::active(), fn ($s) => $s !== $slug));
        self::unregisterMenu($slug);
        self::audit('deactivated', $slug);
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

    // ---- versions / updates ---------------------------------------------

    /** The version recorded when the plugin was last activated/updated. */
    public static function installedVersion(string $slug): ?string
    {
        $map = json_decode(bp_option('plugin_versions', '{}'), true) ?: [];
        return $map[basename($slug)] ?? null;
    }

    protected static function setInstalledVersion(string $slug, string $version): void
    {
        $map = json_decode(bp_option('plugin_versions', '{}'), true) ?: [];
        $map[basename($slug)] = $version;
        Bp_options::updateOrCreate(
            ['option_name' => 'plugin_versions'],
            ['option_value' => json_encode($map), 'autoload' => 'yes']
        );
    }

    /**
     * Apply an available update: run the plugin's new migrations (Laravel skips
     * ones already applied), re-record its version and re-baseline integrity.
     */
    public static function update(string $slug): void
    {
        $slug = basename($slug);
        if (! in_array($slug, self::active(), true)) {
            return; // only active plugins are updated
        }
        self::migrate($slug);                                        // runs only new migrations
        self::setInstalledVersion($slug, self::meta($slug)['version'] ?? '1.0.0');
        self::storeFingerprint($slug);                               // new files → new baseline
        self::clearFailure($slug);
        self::audit('updated to '.(self::meta($slug)['version'] ?? '?'), $slug);
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
            // Re-scan before running the plugin's own cleanup: a plugin tampered
            // after activation must not be able to execute destructive code (e.g.
            // deleting core files) during uninstall.
            $scan = self::scan($slug);
            if (! empty($scan['critical'])) {
                self::audit('uninstall.php skipped — failed security re-scan', $slug);
                \Illuminate\Support\Facades\Log::warning("Plugin uninstall.php SKIPPED by security scan: {$slug}", $scan['critical']);
            } else {
                require $script;
            }
        }

        self::audit('uninstalled', $slug);
    }

    /**
     * Boot every active plugin: register its view namespace (view('<slug>::x'))
     * and load its main file so it can register hooks. Plugin routes are loaded
     * separately during routing (see bootstrap/app.php).
     */
    public static function boot(): void
    {
        try {
            $plugins = self::all();
        } catch (\Throwable $e) {
            return; // DB not ready (pre-migration) — skip plugins entirely.
        }

        foreach ($plugins as $plugin) {
            if (! $plugin['active']) {
                continue;
            }
            // Defensive: skip a plugin whose required plugins are not active (e.g.
            // a dependency was force-removed). Its routes are likewise skipped
            // because bootRoutes reads the same active set.
            if (self::missingDependencies($plugin['slug'])) {
                continue;
            }
            try {
                $dir = self::path().'/'.$plugin['slug'];

                if (is_dir($dir.'/views')) {
                    \Illuminate\Support\Facades\View::addNamespace($plugin['slug'], $dir.'/views');
                }

                $main = $dir.'/'.basename($plugin['main']);
                if (is_file($main)) {
                    require_once $main;
                }
            } catch (\Throwable $e) {
                // Recovery mode: a plugin that throws on load (fatal, parse error,
                // missing dependency) is auto-disabled so the site stays up.
                self::recordFailure($plugin['slug'], $e->getMessage());
            }
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
