<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Support\Plugin;
use App\Support\Theme;

/**
 * Security-verify every installed plugin and theme in one pass — static scan,
 * integrity (tamper) check and compatibility check. Intended for CI / cron:
 * exits non-zero if any package has a critical scan finding, was modified since
 * activation, or is incompatible (and, with --strict, on warnings too).
 */
class VerifyPackages extends Command
{
    protected $signature = 'packages:verify {--strict : Treat warnings as failures} {--json : Output machine-readable JSON}';

    protected $description = 'Scan, integrity- and compatibility-check all installed plugins and themes';

    public function handle(): int
    {
        $rows = [];

        foreach (Plugin::all() as $p) {
            $rows[] = $this->inspect('plugin', $p['slug'], $p['active'],
                Plugin::scan($p['slug']),
                $p['active'] && Plugin::isTampered($p['slug']),
                Plugin::checkRequirements($p['slug']));
        }

        foreach (Theme::all() as $t) {
            $rows[] = $this->inspect('theme', $t['slug'], $t['active'],
                Theme::scan($t['slug']),
                $t['active'] && Theme::isTampered($t['slug']),
                Theme::checkRequirements($t['slug']));
        }

        $strict = (bool) $this->option('strict');
        $failed = array_filter($rows, fn ($r) => $r['critical'] > 0 || $r['tampered']
            || ! empty($r['requirements']) || ($strict && $r['warning'] > 0));

        if ($this->option('json')) {
            $this->line(json_encode(['packages' => array_values($rows), 'failed' => count($failed)], JSON_PRETTY_PRINT));
            return $failed ? self::FAILURE : self::SUCCESS;
        }

        $this->table(
            ['Type', 'Package', 'Active', 'Critical', 'Warn', 'Tampered', 'Compat', 'Status'],
            array_map(fn ($r) => [
                $r['type'],
                $r['slug'],
                $r['active'] ? 'yes' : '—',
                $r['critical'] ?: '',
                $r['warning'] ?: '',
                $r['tampered'] ? 'YES' : '',
                empty($r['requirements']) ? 'ok' : 'FAIL',
                $r['status'],
            ], $rows)
        );

        // Detail lines for anything that isn't clean.
        foreach ($rows as $r) {
            foreach ($r['scan']['critical'] as $c) {
                $this->line("  <fg=red>CRITICAL</> {$r['type']}/{$r['slug']}: {$c['file']} — {$c['reason']}");
            }
            foreach ($r['requirements'] as $req) {
                $this->line("  <fg=red>INCOMPATIBLE</> {$r['type']}/{$r['slug']}: {$req}");
            }
            if ($r['tampered']) {
                $this->line("  <fg=red>MODIFIED</> {$r['type']}/{$r['slug']}: files changed since activation");
            }
        }

        if ($failed) {
            $this->error(count($failed).' package(s) failed verification.');
            return self::FAILURE;
        }

        $this->info('All '.count($rows).' package(s) verified — no critical issues.');
        return self::SUCCESS;
    }

    protected function inspect(string $type, string $slug, bool $active, array $scan, bool $tampered, array $requirements): array
    {
        $critical = count($scan['critical']);
        $warning = count($scan['warning']);

        $status = 'OK';
        if ($critical || $tampered || $requirements) {
            $status = 'FAIL';
        } elseif ($warning) {
            $status = 'WARN';
        }

        return compact('type', 'slug', 'active', 'scan', 'critical', 'warning', 'tampered', 'requirements', 'status');
    }
}
