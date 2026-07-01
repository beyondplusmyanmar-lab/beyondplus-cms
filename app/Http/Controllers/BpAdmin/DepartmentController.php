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
use App\Models\Bp_post;
use App\Models\User;
use App\Models\Bp_tax;
use App\Models\Bp_relationship;
use Auth;

class DepartmentController extends Controller
{
    public function __construct()
    {
       $this->middleware('admins');
    }

    public function index(Request $request){

        if(Auth::guard("admins")->user()->role < 3) {
            
            $post_type = Auth::guard("admins")->user()->department_type;

            // $block = Bp_block::where('block_type',$block_type)->orderBy('id','desc')->where('translate_id',0)->paginate(13);

            $page = Bp_post::where('post_type',$post_type)->orderBy('updated_at','desc')->where('translate_id',0)->paginate(20);


             return view('bp-admin.department.index', array('page' => $page));
        }

        if($request->name == null && $request->filter==null ){

            $page = Bp_post::whereNotIn('post_type',['post','event','news','page','user-guide'])->orderBy('updated_at','desc')->where('translate_id',0)->paginate(20);

        } else {

            $page = Bp_post::whereNotIn('post_type',['post','event','news','page','user-guide'])->orderBy('updated_at','desc')->where('translate_id',0);

            if ($request->name != null  ) {
                if($request->name != "0") {
                    $page = $page->where("title",'like','%'.$request->name.'%');
                }
            }

            // return $request->filter;

            if($request->filter != "0") {
                $page = $page->where("post_type",$request->filter);
            }

            $page = $page->paginate(20);

        }

        return view('bp-admin.department.index', array('page' => $page));

    }


    public function create(){
            $categories= Bp_tax::all();
        //$categories= Bp_tax::lists('category_name','category_id');
        return view('bp-admin.department.add', array('categories' => $categories));

    }

    public function store(Request $request){
        // $this->validate($request, [
        // 'title' => 'required',
        // 'description' => 'required'
        // ]);

        $inputs = $request->all();
        $inputs['post_type'] = 'page';
        $inputs['post_type'] = $request->input('post_type');
        $inputs['post_link'] = urlencode(formatUrl($request->input('title')));
        if ($request->file('category_icon') && $request->file('category_icon')->isValid()) {
            $destinationPath = uploadPath();
            $extension = $request->file('category_icon')->getClientOriginalExtension(); // getting image extension
            // $fileName = 'catmk'.md5(microtime().rand()).'.'.$extension; // renameing image
            $fileName = $request->file('category_icon')->getClientOriginalName();
            $request->file('category_icon')->move($destinationPath, $fileName); // uploading file to given path
            if($request->file('pictures') !=null){
                $inputs['category_icon'] = $fileName;
            }
        }


        Bp_post::create($inputs);
        return redirect()->to('bp-admin/department');
    }

    public function edit($id)
    {



        

        try {
            $page = Bp_post::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return 'Category Not Found';
        }
        $categories= Bp_tax::get()->pluck('category_name','category_id');

        if(Auth::guard("admins")->user()->role <= 2) {
            
            if($page->post_type == Auth::guard("admins")->user()->department_type) {
                return view('bp-admin.department.edit', array('page' => $page, 'categories' => $categories));
            } else {
                return "Permission Denied";
            }
            // Auth::guard("admins")->user()->department_type;
        } 

        return view('bp-admin.department.edit', array('page' => $page, 'categories' => $categories));

    }

    public function update($id, Request $request)
    {
        $inputs = $request->all();
     //   $inputs = $request->except('_token', '_method');
        // $inputs['post_type'] = 'page';
        $inputs['post_type'] = $request->input('post_type');
        $post_link = explode('-', $inputs['post_link']);

        $inputs['post_link'] = urlencode(formatUrl($request->input('title')));
        if($post_link> 0){
            
            if(in_array($post_link[0] ,departmentShort())) {
                $inputs['post_link'] = $request->input('post_link');
            }
        }

        if ($request->file('category_icon') && $request->file('category_icon')->isValid()) {
            $destinationPath = uploadPath();
            $extension = $request->file('category_icon')->getClientOriginalExtension(); // getting image extension
            $fileName = 'catmk'.md5(microtime().rand()).'.'.$extension; // renameing image
            $request->file('category_icon')->move($destinationPath, $fileName); // uploading file to given path
            $inputs['category_icon'] = $fileName;
        }

        Bp_post::findOrFail($id)->update($inputs);
        return redirect()->to('bp-admin/department');
    }

    public function destroy($id)
    {
        Bp_post::find($id)->delete();
        return redirect()->back();
    }

    
    public function translate($id) {
        try {
            $page = Bp_post::findOrFail($id);
            $tax_type = Bp_relationship::where('post_id',$id)->where('type','page')->pluck('tax_id')->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return 'Post Not Found';
        }
        return view('bp-admin.department.translate', array('page' => $page,  'tax_type' => $tax_type,'translate_id' => $id));
    }

}
