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
        // Only these module columns may be toggled/updated from the UI.
        $allowed = ['section', 'module_weight', 'module_icon', 'module_name', 'module_name_mm', 'parent_id'];

        $column = $request->type ? explode(' ', $request->type)[0] : null;

        if (! in_array($column, $allowed, true)) {
            abort(422, 'Invalid field');
        }

        Bp_module::where('module_id', $request->module_id)->update([$column => $request->option]);

        return $column.'-'.$request->module_id;
    }

}
