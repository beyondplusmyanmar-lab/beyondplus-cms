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
        return view('bp-admin.plugin.index', ['plugins' => Plugin::all()]);
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

    public function activate(Request $request)
    {
        $result = Plugin::activate((string) $request->input('slug'));

        if (! empty($result['blocked'])) {
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

    public function deactivate(Request $request)
    {
        Plugin::deactivate((string) $request->input('slug'));

        return redirect()->back()->with('success', 'Plugin deactivated (its data is kept).');
    }

    public function uninstall(Request $request)
    {
        Plugin::uninstall((string) $request->input('slug'));

        return redirect()->back()->with('success', 'Plugin uninstalled and its data removed.');
    }
}
