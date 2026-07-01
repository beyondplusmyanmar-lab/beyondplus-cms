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

        if(Auth::guard("admins")->user()->role < 3) {
            
            $department_type = Auth::guard("admins")->user()->department_type;

            // $block = Bp_block::where('block_type',$block_type)->orderBy('id','desc')->where('translate_id',0)->paginate(13);
            $media = Bp_media::where('department_type',$department_type)->orderBy('updated_at','desc')->paginate(13);

             return view('bp-admin.media.index', array('media' => $media));
        }

        if($request->name == null && $request->filter==null ){

            $media = Bp_media::where('media_type','media')->orderBy('updated_at','desc')->paginate(13);

        } else {

            $media = Bp_media::where('media_type','media')->orderBy('updated_at','desc');

            if ($request->name != null  ) {
                if($request->name != "0") {
                    $media = $media->where("title",'like','%'.$media->name.'%');
                }
            }

            // return $request->filter;

            if($request->filter != "0") {
                $media = $media->where("department_type",$request->filter);
            }

            $media = $media->paginate(20);

        }

        
        return view('bp-admin.media.index', array('media' => $media));
    }


    public function create(){

        return view('bp-admin.media.add');

    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'media_name' => 'required',
            'media_link' => 'required'
        ]);

        if ($validator->fails()) {  
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $inputs = $request->all();

        if ($request->file('media_link') && $request->file('media_link')->isValid()) {
            $destinationPath = uploadPath();
            $extension = $request->file('media_link')->getClientOriginalExtension(); // getting image extension
            $fileName = 'mediamk'.md5(microtime().rand()).'.'.$extension; // renameing image
            $request->file('media_link')->move($destinationPath, $fileName); // uploading file to given path
            $inputs['media_link'] = $fileName;
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
        $validator = Validator::make($request->all(), [
            'media_name' => 'required',
            'media_link' => 'required'
        ]);

        if ($validator->fails()) {  
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $inputs = $request->all();
     //   $inputs = $request->except('_token', '_method');

        if ($request->file('media_link') && $request->file('media_link')->isValid()) {
            $destinationPath = uploadPath();
            $extension = $request->file('media_link')->getClientOriginalExtension(); // getting image extension
            $fileName = 'mediamk'.md5(microtime().rand()).'.'.$extension; // renameing image
            $request->file('media_link')->move($destinationPath, $fileName); // uploading file to given path
            $inputs['media_link'] = $fileName;
        }

        $inputs['media_type'] = 'media';
        $inputs['user_id'] = Auth::guard('admins')->user()->id;
        Bp_media::findOrFail($id)->update($inputs);
        return redirect()->to('bp-admin/media');
    }

    public function destroy($id)
    {
        Bp_media::find($id)->delete();
        return redirect()->back();
    }

}
