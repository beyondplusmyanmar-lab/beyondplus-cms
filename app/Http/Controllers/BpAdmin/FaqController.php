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
use App\Models\User;
use App\Models\Bp_tax;
use App\Models\Bp_relationship;
use App\Models\Faq;

class FaqController extends Controller
{
    public function __construct()
    {
       $this->middleware('admins');
       $this->post_type = "faq";
    }

    public function index(){

        $page = Faq::orderBy('updated_at','desc')->where('translate_id',1)->paginate(20);
        return view('bp-admin.faq.index', array('page' => $page));
    }


    public function create(){
            $categories= Bp_tax::all();
        //$categories= Bp_tax::lists('category_name','category_id');
        return view('bp-admin.faq.add', array('categories' => $categories));

    }

    public function store(Request $request){
        bp_validate_images($request, ['category_icon', 'pictures']);
        // $this->validate($request, [
        // 'title' => 'required',
        // 'description' => 'required'
        // ]);

        $inputs = $request->all();
        // $inputs['post_type'] = $this->post_type;
        // $inputs['post_link'] = formatUrl($request->input('title'));
        // (image upload is disabled for FAQ)


        Faq::create($inputs);
        return redirect()->to('bp-admin/faq');
    }

    public function edit($id)
    {
        try {
            $page = Faq::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return 'Category Not Found';
        }
        // $categories= Bp_tax::get()->pluck('category_name','category_id');
        // return view('bp-admin.faq.edit', array('page' => $page, 'categories' => $categories));
        return view('bp-admin.faq.edit', array('page' => $page));

    }

    public function update($id, Request $request)
    {
        bp_validate_images($request, ['category_icon', 'pictures']);
        $inputs = $request->all();
     //   $inputs = $request->except('_token', '_method');
        // $inputs['post_type'] = $this->post_type;
        // $inputs['post_link'] = formatUrl($request->input('title'));
        // (image upload is disabled for FAQ)

        Faq::findOrFail($id)->update($inputs);
        return redirect()->to('bp-admin/faq');
    }

    public function destroy($id)
    {
        Faq::find($id)->delete();
        return redirect()->back();
    }

    
    public function translate($id) {
        try {
            $page = Faq::findOrFail($id);
            $tax_type = Bp_relationship::where('post_id',$id)->where('type','page')->pluck('tax_id')->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return 'Post Not Found';
        }
        return view('bp-admin.faq.translate', array('page' => $page,  'tax_type' => $tax_type,'translate_id' => $id));
    }

}
