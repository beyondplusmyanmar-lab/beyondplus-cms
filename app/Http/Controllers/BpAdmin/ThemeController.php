<?php

namespace App\Http\Controllers\BpAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Support\Theme;

class ThemeController extends Controller
{
    public function __construct()
    {
        $this->middleware('admins');
    }

    /**
     * List the installed front-end themes with their metadata and preview.
     */
    public function index()
    {
        return view('bp-admin.theme.index', [
            'themes' => Theme::all(),
            'active' => Theme::active(),
        ]);
    }

    /** Show the static security scan report for a theme before activating it. */
    public function scan(Request $request)
    {
        $slug = basename((string) $request->input('theme'));

        return view('bp-admin.theme.scan', [
            'slug' => $slug,
            'meta' => Theme::meta($slug),
            'scan' => Theme::scan($slug),
        ]);
    }

    /**
     * Render the "Customize" content form for a theme from its declared
     * settings schema. Values pre-fill from the stored options (falling back to
     * the schema default), so the form works whether or not seeding has run.
     */
    public function customize(Request $request)
    {
        $slug = basename((string) $request->input('theme', Theme::active()));
        $schema = Theme::settingsSchema($slug);
        abort_if(empty($schema), 404, 'This theme has no editable settings.');

        $values = [];
        foreach ($schema as $field) {
            $name = $field['name'] ?? null;
            if (! $name) { continue; }
            if (($field['type'] ?? 'text') === 'repeater') {
                $decoded = json_decode(bp_option($name, ''), true);
                $values[$name] = is_array($decoded) ? $decoded : (array) ($field['default'] ?? []);
            } else {
                $values[$name] = bp_option($name, $field['default'] ?? '');
            }
        }

        return view('bp-admin.theme.customize', [
            'slug'   => $slug,
            'meta'   => Theme::meta($slug),
            'schema' => $schema,
            'values' => $values,
        ]);
    }

    /** Persist the Customize form — only fields the theme's schema declares. */
    public function saveCustomize(Request $request)
    {
        $slug = basename((string) $request->input('theme'));
        $schema = Theme::settingsSchema($slug);
        abort_if(empty($schema), 404);

        foreach ($schema as $field) {
            $name = $field['name'] ?? null;
            if (! $name) { continue; }
            $type = $field['type'] ?? 'text';

            if ($type === 'repeater') {
                $sub = array_map(fn ($f) => $f['name'], $field['fields'] ?? []);
                $rows = [];
                foreach ((array) $request->input($name, []) as $row) {
                    $row = is_array($row) ? $row : [];
                    $clean = [];
                    foreach ($sub as $k) { $clean[$k] = (string) ($row[$k] ?? ''); }
                    // Drop rows the owner left completely blank.
                    if (implode('', $clean) !== '') { $rows[] = $clean; }
                }
                $value = json_encode(array_values($rows), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                $value = (string) $request->input($name, '');
            }

            \App\Models\Bp_options::updateOrCreate(
                ['option_name' => $name],
                ['option_value' => $value, 'autoload' => 'yes']
            );
        }

        return redirect()->back()->with('success', 'Theme content saved.');
    }

    /**
     * Set the active front-end theme — gated by the compatibility and security
     * checks (a theme that fails the scan is never made active).
     */
    public function activate(Request $request)
    {
        $result = Theme::activate((string) $request->input('theme'));

        if (! empty($result['blocked'])) {
            if (! empty($result['error'])) {
                return redirect()->back()->withErrors($result['error']);
            }
            if (! empty($result['requirements'])) {
                return redirect()->back()->withErrors(array_merge(
                    ['Activation blocked — this theme is not compatible with your environment:'],
                    $result['requirements']
                ));
            }

            $reasons = collect($result['scan']['critical'] ?? [])
                ->map(fn ($f) => $f['file'].' — '.$f['reason'])
                ->all();

            return redirect()->back()->withErrors(array_merge(
                ['Activation blocked — the security scan flagged high-risk code:'],
                $reasons
            ));
        }

        return redirect()->back()->with('success', 'Active theme updated.');
    }
}
