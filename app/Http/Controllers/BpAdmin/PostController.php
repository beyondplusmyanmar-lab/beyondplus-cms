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
use App\Models\Bp_tax;
use App\Models\Bp_term;
use App\Models\Bp_post;
use App\Models\Bp_relationship;
use App\Models\User;
use Auth;
use Validator;

class PostController extends Controller
{
    var $categories;
    public function __construct()
    {
       $this->middleware('admins');
       $this->taxes =  Bp_tax::where('tax_type','cat')->get();
    }

    public function index(){
        $post = Bp_post::where('post_type','post')->orderBy('id','desc')->where('translate_id',0)->paginate(13);
        return view('bp-admin.post.index', array('post' => $post));
    }

    public function create(){
       return view('bp-admin.post.add', array('taxes' => $this->taxes));

    }

    public function store(Request $request){
        bp_validate_images($request, ['featured_img']);

        $validator = Validator::make($request->all(), [
            'title' => 'required', 
            'body' => 'required',
        ]);

        if ($validator->fails()) {  
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $inputs = $request->all();
        $inputs['post_link'] = formatUrl($request->input('title'));
        $inputs['post_type'] = 'post';
        $inputs['post_created'] = Auth::guard('admins')->user()->id;
        Bp_post::create($inputs);

        $update_id = Bp_post::orderBy('id', 'desc')->first();
        
        if ($__up = bp_store_image($request->file('featured_img'), 'feat')) {
            $inputs['featured_img'] = $__up;
        }

        $categories  = $request->get('taxes');

        $this->termInsert($categories,$update_id->id);

        return redirect()->to('bp-admin/post');
    }

    public function edit($id)
    {
        try {
            $post = Bp_post::findOrFail($id);
            $tax_type = Bp_relationship::where('post_id',$id)->where('type','cat')->pluck('tax_id')->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return 'Post Not Found';
        }
        return view('bp-admin.post.edit', array('post' => $post, 'taxes' => $this->taxes, 'tax_type' => $tax_type));

    }

    public function update($id, Request $request)
    {
        bp_validate_images($request, ['featured_img']);
        $inputs = $request->all();
        $inputs['post_link'] = formatUrl($request->input('title'));
        if ($__up = bp_store_image($request->file('featured_img'), 'feat')) {
            $inputs['featured_img'] = $__up;
        }

        Bp_post::findOrFail($id)->update($inputs);

        $categories  = $request->get('taxes');

        //Deleteing Term
        $this->termInsert($categories,$id);

        return redirect()->to('bp-admin/post');
    }

    public function destroy($id)
    {
        $post = Bp_post::find($id);
        if ($post) {
            bp_delete_upload($post->featured_img);
            $post->delete();
        }
        return redirect()->back();
    }

    public function termInsert($categories,$id) {
        if($categories){
            if(sizeof($categories)>0){
                Bp_relationship::where('post_id',$id)->where('type','cat')->delete();
            }
            //Recreating New Term
            for( $i=0; $i<sizeof($categories); $i++){
                $cat['tax_id'] = $categories[$i];
                $cat['post_id'] = $id;
                $cat['type']    = 'cat';
                Bp_relationship::create($cat);
            }
        } else {
            $cat['tax_id'] = 1;
            $cat['post_id'] = $id;
            $cat['type']    = 'cat';
            Bp_relationship::create($cat);
        }
    }


    public function translate($id) {
        try {
            $post = Bp_post::findOrFail($id);
            $tax_type = Bp_relationship::where('post_id',$id)->where('type','post')->pluck('tax_id')->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return 'Post Not Found';
        }
        return view('bp-admin.post.translate', array('post' => $post, 'taxes' => $this->taxes, 'tax_type' => $tax_type,'translate_id' => $id));
    }
}
