<?php

namespace App\Http\Controllers\BpAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Bp_module;
use App\Models\Bp_access;

class ModulesController extends Controller
{
    public function __construct()
    {
       $this->middleware('admins');
    }

    public function index(){

        $module = Bp_module::get();
        return view('bp-admin.modules.index', array('module' => $module));
    }


    public function ajaxUpdate(Request $request){
        // return "ajaxUpdate";
        
        if($request->type) {
            $type = explode(" ", $request->type);
        }
        #validation require
        $access = Bp_module::where('module_id',$request->module_id)->update([$type[0] => $request->option]);

        return $type[0].'-'.$request->module_id;
    }

}
