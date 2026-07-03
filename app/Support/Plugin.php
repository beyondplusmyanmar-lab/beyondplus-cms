<?php

namespace App\Support;

use App\Models\Bp_options;

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
            ];
        }

        ksort($plugins);
        return $plugins;
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
        }
    }

    public static function deactivate(string $slug): void
    {
        self::setActive(array_filter(self::active(), fn ($s) => $s !== basename($slug)));
    }

    /** Load the main file of every active plugin so it can register hooks. */
    public static function boot(): void
    {
        try {
            foreach (self::all() as $plugin) {
                if (! $plugin['active']) {
                    continue;
                }
                $main = self::path().'/'.$plugin['slug'].'/'.basename($plugin['main']);
                if (is_file($main)) {
                    require_once $main;
                }
            }
        } catch (\Throwable $e) {
            // DB not ready (pre-migration) or a bad plugin — don't break the app.
        }
    }
}
