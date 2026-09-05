<?php

/**
 * Created by Beyond Plus <bplusmyanmar@hotmail.com>
 * User: Beyond Plus
 * Date: D/M/Y
 * Time: MM:HH PM
 */

namespace App\Http\Controllers\BpAdmin;

use App\Models\Bp_custom;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Validator;

class CustomController extends Controller
{
    public function __construct()
    {
        $this->middleware('admins');
    }

    public function index()
    {

        $custom = Bp_custom::orderBy('custom_name')->paginate(13);

        return view('bp-admin.custom.index', ['custom' => $custom]);
    }

    public function create(Request $request)
    {

        $categories = Bp_custom::get()->pluck('custom_name', 'custom_id');

        return view('bp-admin.custom.add', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        bp_validate_images($request, ['custom_icon', 'pictures']);
        $validator = Validator::make($request->all(), [
            'custom_name' => 'required',
            'custom_link' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $inputs = $request->all();

        if ($__up = bp_store_image($request->file('custom_icon'), 'cust')) {
            $inputs['custom_icon'] = $__up;
        }

        Bp_custom::create($inputs);

        return redirect()->back()->withSuccess(__('message.success'));
    }

    public function edit($id)
    {
        try {
            $custom = Bp_custom::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return 'custom Not Found';
        }
        $categories = Bp_custom::get()->pluck('custom_name', 'custom_id');

        return view('bp-admin.custom.edit', ['custom' => $custom, 'categories' => $categories]);
    }

    public function update($id, Request $request)
    {
        bp_validate_images($request, ['custom_icon', 'pictures']);
        $validator = Validator::make($request->all(), [
            'custom_name' => 'required',
            'custom_link' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $inputs = $request->all();

        if ($__up = bp_store_image($request->file('custom_icon'), 'cust')) {
            $inputs['custom_icon'] = $__up;
        }

        Bp_custom::findOrFail($id)->update($inputs);

        return redirect()->back()->withSuccess(__('message.success'));
    }

    public function destroy($id)
    {
        Bp_custom::find($id)->delete();

        return redirect()->back();
    }
}
