<?php

/**
 * Created by Beyond Plus <bplusmyanmar@hotmail.com>
 * User: Beyond Plus
 * Date: D/M/Y
 * Time: MM:HH PM
 */

namespace App\Http\Controllers\BpAdmin;

use App\Models\Bp_options;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('admins');
    }

    public function index()
    {

        $options = Bp_options::pluck('option_value', 'option_name');

        return view('bp-admin.settings.general', ['options' => $options]);
    }

    public function edit($id)
    {
        try {
            $category = Bp_category::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return 'Category Not Found';
        }
        $categories = Bp_category::lists('category_name', 'category_id');

        return view('bp-admin.settings.edit', ['category' => $category, 'categories' => $categories]);
    }

    public function generaledit(Request $request)
    {

        $inputs = $request->except('_token', 'save');
        foreach ($inputs as $name => $value) {
            // Cast to string: empty fields arrive as null (ConvertEmptyStringsToNull)
            // and option_value is NOT NULL. (Also avoids the old loop stopping early
            // on the first empty value.)
            Bp_options::where('option_name', $name)->update(['option_value' => (string) $value]);
        }

        return redirect()->back()->withSuccess('Successfully edited');
    }
}
