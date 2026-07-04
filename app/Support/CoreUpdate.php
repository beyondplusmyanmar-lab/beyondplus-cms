<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Core update readiness — GitHub-release based.
 *
 * Checks the project's GitHub repository for the latest published release and
 * compares its tag with the version running, so the admin can be told when a new
 * version is out and shown the release notes as a "what's new" announcement.
 *
 * This is the *check + announce* half only — downloading and applying a core
 * update is a deliberate, out-of-band step (git pull / composer / a future
 * updater), never an automatic file swap. Every call degrades gracefully:
 * disabled, offline, no releases yet, or a bad response never throws.
 */
class CoreUpdate
{
    private const CACHE_KEY = 'bp_core_update';
    private const DEFAULT_REPO = 'beyondplusmyanmar-lab/beyondplus-cms';

    /** The version currently running. */
    public static function current(): string
    {
        return Plugin::CMS_VERSION;
    }

    /** "owner/repo" to check — overridable via the update_repo option. */
    public static function repo(): string
    {
        return trim((string) bp_option('update_repo', '')) ?: self::DEFAULT_REPO;
    }

    /**
     * Check GitHub for the latest release. Returns:
     *   ['configured' => bool, 'current' => string, 'latest' => ?string,
     *    'update_available' => ?bool, 'notes' => ?string, 'url' => ?string,
     *    'published_at' => ?string, 'none' => ?bool, 'error' => ?bool]
     */
    public static function check(bool $force = false): array
    {
        $current = self::current();

        if (bp_option('update_check', 'yes') !== 'yes') {
            return ['configured' => false, 'current' => $current];
        }

        if ($force) {
            Cache::forget(self::CACHE_KEY);
        }

        $data = Cache::remember(self::CACHE_KEY, now()->addHours(6), fn () => self::fetchLatest());

        if (! empty($data['none'])) {
            return ['configured' => true, 'current' => $current, 'none' => true];
        }
        if (empty($data['version'])) {
            return ['configured' => true, 'current' => $current, 'error' => true];
        }

        return [
            'configured'       => true,
            'current'          => $current,
            'latest'           => $data['version'],
            'update_available' => version_compare($data['version'], $current, '>'),
            'notes'            => $data['notes'] ?? null,
            'url'              => $data['url'] ?? null,
            'published_at'     => $data['published_at'] ?? null,
        ];
    }

    /** Fetch the latest GitHub release for the configured repo. */
    private static function fetchLatest(): array
    {
        try {
            $response = Http::acceptJson()
                ->timeout(8)
                ->withHeaders(['User-Agent' => 'BeyondPlusCMS'])
                ->get('https://api.github.com/repos/'.self::repo().'/releases/latest');

            if ($response->status() === 404) {
                return ['none' => true];                 // no published release yet
            }
            if (! $response->successful()) {
                return ['__error' => true];
            }

            $json = $response->json();

            return [
                'version'      => ltrim((string) ($json['tag_name'] ?? ''), 'vV'),
                'notes'        => $json['body'] ?? null,
                'url'          => $json['html_url'] ?? null,
                'published_at' => $json['published_at'] ?? null,
            ];
        } catch (\Throwable $e) {
            return ['__error' => true];
        }
    }
}
