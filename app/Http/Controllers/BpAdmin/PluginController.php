<?php

namespace App\Http\Controllers\BpAdmin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Support\Plugin;

class PluginController extends Controller
{
    public function __construct()
    {
        $this->middleware('admins');

        // Managing plugins loads and runs code, so restrict every action —
        // including the POST activate/deactivate/uninstall — to admins who have
        // access to the Plugins module (AdminAuth only checks that on GETs).
        $this->middleware(function ($request, $next) {
            $user = Auth::guard('admins')->user();
            $moduleId = DB::table('bp_modules')->where('module_link', 'plugins')->value('module_id');
            $allowed = $moduleId && DB::table('bp_access')
                ->where('module_id', $moduleId)
                ->where('usertype', $user->role ?? 0)
                ->where('canshow', 1)
                ->exists();
            abort_unless($allowed, 403, 'You do not have permission to manage plugins.');

            return $next($request);
        });
    }

    public function index()
    {
        return view('bp-admin.plugin.index', [
            'grouped'  => collect(Plugin::all())->groupBy('category')->sortKeys(),
            'failures' => Plugin::failures(),
        ]);
    }

    /** Full detail page for a single plugin. */
    public function show(Request $request)
    {
        $slug = basename((string) $request->input('slug'));
        $all = Plugin::all();
        abort_unless(isset($all[$slug]), 404, 'Plugin not found.');

        return view('bp-admin.plugin.view', [
            'plugin'       => $all[$slug],
            'meta'         => Plugin::meta($slug),
            'scan'         => Plugin::scan($slug),
            'requirements' => Plugin::checkRequirements($slug),
            'failure'      => Plugin::failures()[$slug] ?? null,
        ]);
    }

    /** Show the static security scan report for a plugin before activating it. */
    public function scan(Request $request)
    {
        $slug = basename((string) $request->input('slug'));

        return view('bp-admin.plugin.scan', [
            'slug' => $slug,
            'meta' => Plugin::meta($slug),
            'scan' => Plugin::scan($slug),
        ]);
    }

    /** Render a plugin's settings form (from its declared schema). */
    public function settings(Request $request)
    {
        $slug = basename((string) $request->input('slug'));
        $schema = Plugin::settingsSchema($slug);
        abort_if(empty($schema), 404, 'This plugin has no settings.');

        $values = [];
        foreach ($schema as $field) {
            $name = $field['name'];
            $key  = Plugin::settingKey($slug, $name);
            if (($field['type'] ?? 'text') === 'repeater') {
                $decoded = json_decode(bp_option($key, ''), true);
                $values[$name] = is_array($decoded) ? $decoded : (array) ($field['default'] ?? []);
            } else {
                $values[$name] = bp_option($key, $field['default'] ?? '');
            }
        }

        return view('bp-admin.plugin.settings', [
            'slug'   => $slug,
            'meta'   => Plugin::meta($slug),
            'schema' => $schema,
            'values' => $values,
        ]);
    }

    /** Persist a plugin's settings (only fields declared in its schema). */
    public function saveSettings(Request $request)
    {
        $slug = basename((string) $request->input('slug'));
        $schema = Plugin::settingsSchema($slug);
        abort_if(empty($schema), 404);

        foreach ($schema as $field) {
            $name = $field['name'];
            if (($field['type'] ?? 'text') === 'repeater') {
                $sub  = array_map(fn ($f) => $f['name'], $field['fields'] ?? []);
                $rows = [];
                foreach ((array) $request->input($name, []) as $row) {
                    $row   = is_array($row) ? $row : [];
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
                ['option_name' => Plugin::settingKey($slug, $name)],
                ['option_value' => $value, 'autoload' => 'yes']
            );
        }

        return redirect()->back()->with('success', 'Settings saved.');
    }

    /** Send a test message through a provider plugin (uses its declared hook). */
    public function test(Request $request)
    {
        $slug = basename((string) $request->input('slug'));
        $test = Plugin::meta($slug)['test'] ?? null;
        abort_if(! $test, 404);

        $to = trim((string) $request->input('test_to'));
        if ($to === '') {
            return redirect()->back()->withErrors('Enter a recipient to test.');
        }

        $hook = $test['hook'] ?? 'send_sms';
        if ($hook === 'send_mail') {
            $ok = (bool) bp_apply_filters('send_mail', false, $to, 'Test — Beyond Plus CMS', "This is a test email from the {$slug} plugin.");
        } elseif ($hook === 'send_telegram') {
            $ok = (bool) bp_apply_filters('send_telegram', false, "Test message from Beyond Plus CMS ({$slug}): {$to}");
        } else {
            $ok = (bool) bp_apply_filters('send_sms', false, $to, "Test SMS from Beyond Plus CMS ({$slug}).");
        }

        return $ok
            ? redirect()->back()->with('success', 'Test message sent.')
            : redirect()->back()->withErrors('Test failed — check the settings and that the plugin is active.');
    }

    public function activate(Request $request)
    {
        $result = Plugin::activate((string) $request->input('slug'));

        if (! empty($result['blocked'])) {
            if (! empty($result['requirements'])) {
                return redirect()->back()->withErrors(array_merge(
                    ['Activation blocked — this plugin is not compatible with your environment:'],
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

        return redirect()->back()->with('success', 'Plugin activated.');
    }

    public function update(Request $request)
    {
        Plugin::update((string) $request->input('slug'));

        return redirect()->back()->with('success', 'Plugin updated.');
    }

    public function deactivate(Request $request)
    {
        $slug = (string) $request->input('slug');
        $dependents = Plugin::dependents($slug);
        if ($dependents) {
            return redirect()->back()->withErrors(
                'Cannot deactivate — these active plugins depend on it: '.implode(', ', $dependents).
                '. Deactivate them first.'
            );
        }
        Plugin::deactivate($slug);

        return redirect()->back()->with('success', 'Plugin deactivated (its data is kept).');
    }

    public function uninstall(Request $request)
    {
        Plugin::uninstall((string) $request->input('slug'));

        return redirect()->back()->with('success', 'Plugin uninstalled and its data removed.');
    }
}
