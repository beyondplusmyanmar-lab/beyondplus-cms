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
use App\Models\Bp_slideshow;
use App\Models\User;
use Auth;

class SlideshowController extends Controller
{
    public function __construct()
    {
       $this->middleware('admins');
    }

    public function index(){

        $slideshow = Bp_slideshow::where('slideshow_type','=', 'slideshow')->orderBy('updated_at','desc')->paginate(13);
        return view('bp-admin.slideshow.index', array('slideshow' => $slideshow));
    }


    public function create(){

        return view('bp-admin.slideshow.add');

    }

    public function store(Request $request){
        bp_validate_images($request, ['slideshow_link']);
        // $this->validate($request, [
        // 'title' => 'required',
        // 'description' => 'required'
        // ]);
        $inputs = $request->all();

        if ($__up = bp_store_image($request->file('slideshow_link'), 'slid')) {
            $inputs['slideshow_link'] = $__up;
        }



        $inputs['slideshow_type'] = 'slideshow';
        $inputs['user_id'] = Auth::guard('admins')->user()->id;
        Bp_slideshow::create($inputs);
        return redirect()->to('bp-admin/slideshow');
    }

    public function edit($id)
    {
        try {
            $slideshow = Bp_slideshow::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return 'Category Not Found';
        }
        return view('bp-admin.slideshow.edit', array('slideshow' => $slideshow));

    }

    public function update($id, Request $request)
    {
        bp_validate_images($request, ['slideshow_link']);
        $inputs = $request->all();
     //   $inputs = $request->except('_token', '_method');

        if ($__up = bp_store_image($request->file('slideshow_link'), 'slid')) {
            $inputs['slideshow_link'] = $__up;
        }

        Bp_slideshow::findOrFail($id)->update($inputs);
        return redirect()->to('bp-admin/slideshow');
    }

    public function destroy($id)
    {
        $slide = Bp_slideshow::find($id);
        if ($slide) {
            bp_delete_upload($slide->slideshow_link);
            $slide->delete();
        }
        return redirect()->back();
    }

}
