<?php

namespace App\Support;

use App\Models\Bp_options;
use Illuminate\Support\Facades\Log;

/**
 * Front-end themes as secure packages — the same host model as plugins:
 * stable metadata, a security scan and compatibility check before a theme is
 * made active, and an integrity fingerprint to detect later tampering.
 *
 * Themes live in resources/views/theme/<slug> with a theme.json manifest. The
 * active theme is stored in the `theme` option.
 */
class Theme
{
    public static function path(): string
    {
        return resource_path('views/theme');
    }

    public static function meta(string $slug): array
    {
        $file = self::path().'/'.basename($slug).'/theme.json';
        return is_file($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
    }

    public static function active(): string
    {
        return bp_option('theme', 'default');
    }

    /** Every installed theme with its metadata, active + tamper status. */
    public static function all(): array
    {
        $active = self::active();
        $themes = [];

        foreach (glob(self::path().'/*', GLOB_ONLYDIR) as $dir) {
            $slug = basename($dir);
            $meta = self::meta($slug);
            $isActive = $slug === $active;

            $themes[$slug] = [
                'slug'          => $slug,
                'id'            => $meta['id'] ?? $slug,
                'type'          => $meta['type'] ?? 'theme',
                'name'          => $meta['name'] ?? ucfirst($slug),
                'description'   => $meta['description'] ?? 'No description provided.',
                'version'       => $meta['version'] ?? '1.0.0',
                'author'        => $meta['author'] ?? '',
                'homepage'      => $meta['homepage'] ?? '',
                'license'       => $meta['license'] ?? '',
                'minCmsVersion' => $meta['minCmsVersion'] ?? '',
                'active'        => $isActive,
                'tampered'      => $isActive && self::isTampered($slug),
                'preview'       => file_exists(public_path('theme-previews/'.$slug.'.png'))
                    ? 'theme-previews/'.$slug.'.png' : null,
            ];
        }

        ksort($themes);
        return $themes;
    }

    /** Scan the theme's PHP/Blade files (client <script> stripped). */
    public static function scan(string $slug): array
    {
        return PackageGuard::scan(self::path().'/'.basename($slug));
    }

    public static function checkRequirements(string $slug): array
    {
        return PackageGuard::checkRequirements(self::meta($slug), Plugin::CMS_VERSION);
    }

    public static function isTampered(string $slug): bool
    {
        $map = json_decode(bp_option('theme_hashes', '{}'), true) ?: [];
        $slug = basename($slug);
        return isset($map[$slug]) && $map[$slug] !== PackageGuard::fingerprint(self::path().'/'.$slug);
    }

    protected static function storeFingerprint(string $slug): void
    {
        $map = json_decode(bp_option('theme_hashes', '{}'), true) ?: [];
        $map[$slug] = PackageGuard::fingerprint(self::path().'/'.$slug);
        Bp_options::updateOrCreate(
            ['option_name' => 'theme_hashes'],
            ['option_value' => json_encode($map), 'autoload' => 'yes']
        );
    }

    /**
     * Make a theme active — but only after it passes the compatibility and
     * security checks. Returns ['blocked' => true, ...] or ['activated' => true].
     */
    public static function activate(string $slug): array
    {
        $slug = basename($slug);
        if ($slug === '' || ! is_dir(self::path().'/'.$slug)) {
            return ['blocked' => true, 'error' => 'That theme could not be found.'];
        }

        $problems = self::checkRequirements($slug);
        if ($problems) {
            return ['blocked' => true, 'requirements' => $problems];
        }

        $scan = self::scan($slug);
        if (! empty($scan['critical'])) {
            Log::warning("Theme activation BLOCKED by security scan: {$slug}", $scan['critical']);
            return ['blocked' => true, 'scan' => $scan];
        }

        Bp_options::updateOrCreate(['option_name' => 'theme'], ['option_value' => $slug]);
        self::storeFingerprint($slug);

        $who = optional(auth('admins')->user())->email ?? 'system';
        Log::info("Theme activated: {$slug} (by {$who})");

        return ['activated' => true, 'scan' => $scan];
    }
}
