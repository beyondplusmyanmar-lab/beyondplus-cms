<?php

namespace App\Http\Controllers\BpAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Bp_module;
use App\Models\Bp_access;
use App\Models\Bp_usertype;

class PermissionController extends Controller
{
    public function __construct()
    {
       // $this->middleware('admins');
    }

    public function index(){

        $pro_cat = 1;

        //$module = Bp_access::with('module')->orderBy('access_id','asc')->get();

        $module = Bp_access::with('module')->whereHas('module', function ($q) use($pro_cat) {
                $q->where('section', $pro_cat)->where('parent_id', 0);
            })->orderBy('usertype','asc')->get();
        return view('bp-admin.permission.index', array('module' => $module));
    }


    public function ajaxUpdate(Request $request){
        // return "ajaxUpdate";
        
        if($request->type) {
            $type = explode(" ", $request->type);
        }
        #validation require
        $access = Bp_access::where('access_id',$request->access_id)->update([$type[0] => $request->option]);

        return $type[0].'-'.$request->access_id;
    }


    public function permissionReset(Request $request){
        // return "ajaxUpdate";
        Bp_access::truncate();


        $bp_module = Bp_module::where('section','1')->get();

        $bp_usertype = Bp_usertype::get();


        foreach ($bp_usertype as $key => $value) {

            $option = 1;
            if($value->id == 1) {
                $option = 0;
            }

            foreach ($bp_module as $key2 => $value2) {
                Bp_access::create(['module_id' => $value2->module_id, 'usertype' => $value->id, 'canshow' => $option, 'cancreate' => $option,  'canedit' => $option,  'candelete' => $option  ]);
            }
        }

        return redirect()->back();
    }

}
