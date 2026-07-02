<?php

namespace App\Http\Controllers\BpAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Bp_options;

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
        $active = bp_option('theme', 'default');

        $themes = collect(glob(resource_path('views/theme/*'), GLOB_ONLYDIR))
            ->map(function ($path) {
                $slug = basename($path);
                $meta = [];
                if (is_file($path.'/theme.json')) {
                    $meta = json_decode(file_get_contents($path.'/theme.json'), true) ?: [];
                }

                return [
                    'slug'        => $slug,
                    'name'        => $meta['name'] ?? ucfirst($slug),
                    'description' => $meta['description'] ?? 'No description provided.',
                    'version'     => $meta['version'] ?? '1.0.0',
                    'author'      => $meta['author'] ?? '',
                    'preview'     => file_exists(public_path('theme-previews/'.$slug.'.png'))
                        ? 'theme-previews/'.$slug.'.png' : null,
                ];
            })->values();

        return view('bp-admin.theme.index', compact('themes', 'active'));
    }

    /**
     * Set the active front-end theme.
     */
    public function activate(Request $request)
    {
        // basename() guards against path traversal in the submitted slug.
        $slug = basename((string) $request->input('theme'));

        if ($slug === '' || ! is_dir(resource_path('views/theme/'.$slug))) {
            return redirect()->back()->withErrors('That theme could not be found.');
        }

        Bp_options::updateOrCreate(
            ['option_name' => 'theme'],
            ['option_value' => $slug]
        );

        return redirect()->back()->with('flash_message', 'Active theme updated.');
    }
}
