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
use App\Models\Bp_block;
use Auth;

class BlockController extends Controller
{
    public function __construct()
    {
       $this->middleware('admins');
    }

    public function index(Request $request){

        if(Auth::guard("admins")->user()->role < 3) {
            
            $block_type = Auth::guard("admins")->user()->department_type;

            $block = Bp_block::where('block_type',$block_type)->orderBy('id','desc')->where('translate_id',0)->paginate(13);

             return view('bp-admin.block.index', array('block' => $block));
        }

        if($request->name == null && $request->filter==null ){

            $block = Bp_block::orderBy('id','desc')->where('translate_id',0)->paginate(13);

        } else {

            $block = Bp_block::orderBy('id','desc')->where('translate_id',0);

            // $page = Bp_post::whereNotIn('post_type',['post','event','news','page','user-guide'])->orderBy('updated_at','desc')->where('translate_id',0);

            if ($request->name != null  ) {
                if($request->name != "0") {
                    $block = $block->where("title",'like','%'.$request->name.'%');
                }
            }

            // return $request->filter;

            if($request->filter != "0") {
                $block = $block->where("block_type",$request->filter);
            }

            $block = $block->paginate(20);

        }

        
        return view('bp-admin.block.index', array('block' => $block));
    }


    public function create(){
        return view('bp-admin.block.add');
    }

    public function store(Request $request){
        // $this->validate($request, [
        // 'title' => 'required',
        // 'description' => 'required'
        // ]);

        $inputs = $request->all();
        $inputs['block_url'] = formatUrl($request->input('title'));

        Bp_block::create($inputs);
        return redirect()->to('bp-admin/block');
    }

    public function edit($id)
    {
        try {
            $block = Bp_block::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return 'Category Not Found';
        }
        return view('bp-admin.block.edit', array('block' => $block));

    }

    public function update($id, Request $request)
    {
        $inputs = $request->all();
        $inputs['block_url'] = formatUrl($request->input('title'));

        Bp_block::findOrFail($id)->update($inputs);
        return redirect()->to('bp-admin/block');
    }

    public function destroy($id)
    {
        Bp_block::find($id)->delete();
        return redirect()->back();
    }

    
    public function translate($id) {
        try {
            $block = Bp_block::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return 'Post Not Found';
        }
        return view('bp-admin.block.translate', array('block' => $block, 'translate_id' => $id));
    }

}
