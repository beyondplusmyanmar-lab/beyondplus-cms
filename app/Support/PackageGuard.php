<?php

namespace App\Support;

/**
 * Shared security utilities for installable packages (plugins and themes):
 * static code scanning, integrity fingerprints and compatibility checks. Both
 * App\Support\Plugin and App\Support\Theme delegate to this so the rules stay
 * in one place.
 */
class PackageGuard
{
    /**
     * Static security scan of a package directory. Returns
     * ['critical' => [...], 'warning' => [...]] of ['file' => ..., 'reason' => ...].
     * Critical matches should block activation; warnings are informational.
     *
     * For Blade/HTML files, inline <script> blocks are stripped first so that
     * client-side JavaScript (which legitimately uses eval, backticks, etc.)
     * doesn't trip the server-side PHP heuristics.
     */
    public static function scan(string $dir, array $exts = ['php']): array
    {
        $critical = [
            '/\beval\s*\(/i'                                              => 'eval() — executes arbitrary code',
            '/\b(exec|shell_exec|system|passthru|proc_open|popen)\s*\(/i' => 'shell / process execution',
            '/`[^`\n]*`/'                                                 => 'backtick shell execution',
            '/\bassert\s*\(\s*[\'"]/i'                                    => 'assert() on a string — executes code',
            '/\bcreate_function\s*\(/i'                                   => 'create_function() — executes code',
            '/preg_replace\s*\(\s*([\'"]).*\1\s*[.,]?\s*[\'"][^\'"]*e/i'  => 'preg_replace /e — executes code',
            '/(eval|assert)\s*\(\s*(base64_decode|gzinflate|gzuncompress|str_rot13)/i' => 'obfuscated code execution',
            '/(include|require)(_once)?\s*\(?\s*[\'"]https?:\/\//i'       => 'remote code inclusion',
            // Deleting files/directories is blocked outright — a plugin must not be
            // able to remove core or other plugins' files.
            '/\b(unlink|rmdir)\s*\(/i'                                    => 'file / directory deletion',
            '/\b(File|Storage)::(delete|deleteDirectory|deleteDirectories|cleanDirectory)\s*\(/' => 'file / directory deletion',
        ];
        $warning = [
            '/\bbase64_decode\s*\(/i'                    => 'base64_decode — can hide payloads',
            '/\b(file_put_contents|fwrite|fopen)\s*\(/i' => 'writes to the filesystem',
            '/\bcurl_exec\s*\(/i'                        => 'raw cURL request',
            '/\bmove_uploaded_file\s*\(/i'               => 'handles uploaded files',
            '/\b(putenv|ini_set)\s*\(/i'                 => 'changes the runtime environment',
        ];

        $hit = ['critical' => [], 'warning' => []];
        foreach (self::files($dir, $exts) as $file) {
            $code = @file_get_contents($file);
            if ($code === false) {
                continue;
            }
            $code = self::sanitize($code, self::isMarkup($file));
            $rel = ltrim(str_replace($dir, '', $file), '/\\');
            foreach ($critical as $re => $reason) {
                if (preg_match($re, $code)) {
                    $hit['critical'][] = ['file' => $rel, 'reason' => $reason];
                }
            }
            foreach ($warning as $re => $reason) {
                if (preg_match($re, $code)) {
                    $hit['warning'][] = ['file' => $rel, 'reason' => $reason];
                }
            }
        }
        return $hit;
    }

    /** SHA-256 fingerprint over a package's code files + its manifest. */
    public static function fingerprint(string $dir, array $exts = ['php']): string
    {
        $parts = [];
        foreach (self::files($dir, $exts) as $f) {
            $parts[str_replace($dir, '', $f)] = hash_file('sha256', $f);
        }
        foreach (['plugin.json', 'theme.json'] as $manifest) {
            if (is_file($dir.'/'.$manifest)) {
                $parts[$manifest] = hash_file('sha256', $dir.'/'.$manifest);
            }
        }
        ksort($parts);
        return hash('sha256', json_encode($parts));
    }

    /** Files under $dir whose name ends with one of $exts (so 'php' covers .blade.php). */
    public static function files(string $dir, array $exts): array
    {
        if (! is_dir($dir)) {
            return [];
        }
        $out = [];
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($it as $f) {
            if (! $f->isFile()) {
                continue;
            }
            $name = strtolower($f->getFilename());
            foreach ($exts as $ext) {
                if (str_ends_with($name, '.'.strtolower($ext))) {
                    $out[] = $f->getPathname();
                    break;
                }
            }
        }
        return $out;
    }

    /** Compatibility check against the environment. Returns unmet requirements. */
    public static function checkRequirements(array $meta, string $cmsVersion): array
    {
        $problems = [];
        if (! empty($meta['minCmsVersion']) && version_compare($cmsVersion, $meta['minCmsVersion'], '<')) {
            $problems[] = "needs CMS >= {$meta['minCmsVersion']} (this is {$cmsVersion})";
        }
        $req = $meta['requires'] ?? [];
        if (! empty($req['php']) && version_compare(PHP_VERSION, $req['php'], '<')) {
            $problems[] = "needs PHP >= {$req['php']} (this is ".PHP_VERSION.')';
        }
        foreach ($req['extensions'] ?? [] as $ext) {
            if (! extension_loaded($ext)) {
                $problems[] = "needs PHP extension: {$ext}";
            }
        }
        return $problems;
    }

    protected static function isMarkup(string $file): bool
    {
        $f = strtolower($file);
        return str_ends_with($f, '.blade.php') || str_ends_with($f, '.html') || str_ends_with($f, '.htm');
    }

    /**
     * Reduce a file to just its executable code before scanning, so comments and
     * client-side script don't cause false positives (e.g. `send_sms` in a
     * docblock, or JS eval/backticks). Real backtick/eval in PHP is preserved.
     */
    protected static function sanitize(string $code, bool $markup): string
    {
        if ($markup) {
            // Blade/HTML: drop inline <script>, HTML comments and Blade comments.
            $code = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $code);
            $code = preg_replace('#<!--.*?-->#s', '', $code);
            $code = preg_replace('#\{\{--.*?--\}\}#s', '', $code);
            return $code;
        }

        // Pure PHP: tokenize and drop comments (keeps strings and real backticks).
        try {
            $out = '';
            foreach (token_get_all($code) as $t) {
                if (is_array($t)) {
                    if ($t[0] === T_COMMENT || $t[0] === T_DOC_COMMENT) {
                        continue;
                    }
                    $out .= $t[1];
                } else {
                    $out .= $t;
                }
            }
            return $out;
        } catch (\Throwable $e) {
            return $code; // parse error — scan raw (safer to over-flag)
        }
    }
}
