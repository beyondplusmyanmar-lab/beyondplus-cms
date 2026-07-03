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
