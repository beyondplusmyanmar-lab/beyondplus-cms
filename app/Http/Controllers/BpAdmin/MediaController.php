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
use App\Models\Bp_media;
use App\Models\User;
use Auth;
use Validator;

class MediaController extends Controller
{
    public function __construct()
    {
       $this->middleware('admins');
    }

    public function index(Request $request){

        $media = Bp_media::where('media_type', 'media')->orderBy('updated_at', 'desc');

        if ($request->name != null && $request->name != "0") {
            $media = $media->where('media_name', 'like', '%'.$request->name.'%');
        }

        $media = $media->paginate(13);

        return view('bp-admin.media.index', ['media' => $media]);
    }


    public function create(){

        return view('bp-admin.media.add');

    }

    public function store(Request $request){
        bp_validate_images($request, ['media_link']);

        $validator = Validator::make($request->all(), [
            'media_name' => 'required',
            'media_link' => 'required'
        ]);

        if ($validator->fails()) {  
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $inputs = $request->all();

        if ($__up = bp_store_image($request->file('media_link'), 'medi')) {
            $inputs['media_link'] = $__up;
        }

        $inputs['media_type'] = 'media';
        $inputs['user_id'] = Auth::guard('admins')->user()->id;
        Bp_media::create($inputs);
        return redirect()->to('bp-admin/media');
    }

    public function edit($id)
    {
        try {
            $media = Bp_media::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return 'Category Not Found';
        }
        return view('bp-admin.media.edit', array('media' => $media));

    }

    public function update($id, Request $request)
    {
        bp_validate_images($request, ['media_link']);
        $validator = Validator::make($request->all(), [
            'media_name' => 'required',
            'media_link' => 'required'
        ]);

        if ($validator->fails()) {  
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $inputs = $request->all();
     //   $inputs = $request->except('_token', '_method');

        if ($__up = bp_store_image($request->file('media_link'), 'medi')) {
            $inputs['media_link'] = $__up;
        }

        $inputs['media_type'] = 'media';
        $inputs['user_id'] = Auth::guard('admins')->user()->id;
        Bp_media::findOrFail($id)->update($inputs);
        return redirect()->to('bp-admin/media');
    }

    public function destroy($id)
    {
        $media = Bp_media::find($id);
        if ($media) {
            bp_delete_upload($media->media_link);
            $media->delete();
        }
        return redirect()->back();
    }

}
