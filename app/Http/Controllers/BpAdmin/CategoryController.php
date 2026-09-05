<?php

/**
 * Created by Beyond Plus <bplusmyanmar@hotmail.com>
 * User: Beyond Plus
 * Date: D/M/Y
 * Time: MM:HH PM
 */

namespace App\Http\Controllers\BpAdmin;

use App\Models\Bp_tax;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->tax_name = 'cat';
        $this->middleware('admins');
    }

    public function index()
    {

        $category = Bp_tax::with('translate')->orderBy('tax_id', 'desc')->where('lang', 1)->paginate(13);

        return view('bp-admin.category.index', ['category' => $category]);
    }

    public function create()
    {
        $categories = Bp_tax::get()->pluck('tax_name', 'tax_id');

        return view('bp-admin.category.add', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        bp_validate_images($request, ['pictures', 'tax_icon']);
        // $this->validate($request, [
        // 'title' => 'required',
        // 'description' => 'required'
        // ]);

        $inputs = $request->all();
        $inputs['tax_link'] = formatUrl($request->input('tax_name'));

        if ($__up = bp_store_image($request->file('tax_icon'), 'tax_')) {
            $inputs['tax_icon'] = $__up;
        } else {
            $inputs['tax_icon'] = 'fa fa-list';
        }

        Bp_tax::create($inputs);

        return redirect()->to('bp-admin/category');
    }

    public function edit($id)
    {
        try {
            $category = Bp_tax::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return 'Category Not Found';
        }
        $categories = Bp_tax::get()->pluck('tax_name', 'tax_id');

        return view('bp-admin.category.edit', ['category' => $category, 'categories' => $categories]);
    }

    public function update($id, Request $request)
    {
        bp_validate_images($request, ['pictures', 'tax_icon']);
        $inputs = $request->all();
        //   $inputs = $request->except('_token', '_method');
        $inputs['tax_link'] = formatUrl($request->input('tax_name'));

        if ($__up = bp_store_image($request->file('tax_icon'), 'tax_')) {
            $inputs['tax_icon'] = $__up;
        }

        Bp_tax::findOrFail($id)->update($inputs);

        return redirect()->to('bp-admin/category');
    }

    public function destroy($id)
    {
        Bp_tax::find($id)->delete();

        return redirect()->back();
    }

    public function translate($id)
    {
        try {
            $tax = Bp_tax::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return 'Tax Not Found';
        }

        return view('bp-admin.category.translate', ['tax' => $tax, 'translate_id' => $id]);
    }
}
