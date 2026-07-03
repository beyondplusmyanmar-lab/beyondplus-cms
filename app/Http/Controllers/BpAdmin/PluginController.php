<?php

namespace App\Http\Controllers\BpAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Support\Plugin;

class PluginController extends Controller
{
    public function __construct()
    {
        $this->middleware('admins');
    }

    public function index()
    {
        return view('bp-admin.plugin.index', ['plugins' => Plugin::all()]);
    }

    public function activate(Request $request)
    {
        Plugin::activate((string) $request->input('slug'));
        return redirect()->back()->with('flash_message', 'Plugin activated.');
    }

    public function deactivate(Request $request)
    {
        Plugin::deactivate((string) $request->input('slug'));
        return redirect()->back()->with('flash_message', 'Plugin deactivated (its data is kept).');
    }

    public function uninstall(Request $request)
    {
        Plugin::uninstall((string) $request->input('slug'));
        return redirect()->back()->with('flash_message', 'Plugin uninstalled and its data removed.');
    }
}
