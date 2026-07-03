<?php

/**
 * Created by Beyond Plus <bplusmyanmar@hotmail.com>
 * User: Beyond Plus
 * Date: D/M/Y
 * Time: MM:HH PM
 */
namespace App\Http\Controllers\BpAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Bp_slider;
use App\Models\User;
use Auth;

class SliderController extends Controller
{
    public function __construct()
    {
       $this->middleware('admins');
    }

    public function index(){

        $slider = Bp_slider::where('slider_type','slider')->orderBy('updated_at','desc')->paginate(13);
        return view('bp-admin.slider.index', array('slider' => $slider));
    }


    public function create(){

        return view('bp-admin.slider.add');

    }

    public function store(Request $request){
        // Only allow real images (blocks e.g. an uploaded .php from being executed).
        $request->validate([
            'slider_name' => 'required',
            'slider_link' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
        ]);
        $inputs = $request->all();

        if ($__up = bp_store_image($request->file('slider_link'), 'slid')) {
            $inputs['slider_link'] = $__up;
        }



        $inputs['slider_type'] = 'slider';
        $inputs['user_id'] = Auth::guard('admins')->user()->id;
        Bp_slider::create($inputs);
        return redirect()->to('bp-admin/slider');
    }

    public function edit($id)
    {
        try {
            $slider = Bp_slider::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return 'Category Not Found';
        }
        return view('bp-admin.slider.edit', array('slider' => $slider));

    }

    public function update($id, Request $request)
    {
        // Image is optional on update, but if supplied it must be a real image.
        $request->validate([
            'slider_link' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
        ]);
        $inputs = $request->all();

        if ($__up = bp_store_image($request->file('slider_link'), 'slid')) {
            $inputs['slider_link'] = $__up;
        }

        $inputs['slider_type'] = 'slider';
        $inputs['user_id'] = Auth::guard('admins')->user()->id;
        Bp_slider::findOrFail($id)->update($inputs);
        return redirect()->to('bp-admin/slider');
    }

    public function destroy($id)
    {
        $slider = Bp_slider::find($id);
        if ($slider) {
            bp_delete_upload($slider->slider_link);
            $slider->delete();
        }
        return redirect()->back();
    }

}
