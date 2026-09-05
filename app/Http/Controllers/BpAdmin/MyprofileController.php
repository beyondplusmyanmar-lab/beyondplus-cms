<?php

/**
 * Created by Beyond Plus <bplusmyanmar@hotmail.com>
 * User: Beyond Plus
 * Date: D/M/Y
 * Time: MM:HH PM
 */

namespace App\Http\Controllers\BpAdmin;

use App\Admin;
use App\Http\Controllers\Controller;
use App\Models\Bp_usertype;
use Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Validator;

class MyprofileController extends Controller
{
    public function __construct()
    {
        $this->middleware('admins');
    }

    public function editPassword()
    {
        $id = Auth::guard('admins')->user()->id;
        try {
            $adminaccounts = Admin::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return 'Product Not Found';
        }
        // $categories= User::lists('name','email');
        $usertypes = Bp_usertype::get()->pluck('role', 'id');

        return view('bp-admin.myprofile')->with(compact('adminaccounts', 'usertypes'));
    }

    public function editsavePassword(Request $request)
    {
        $id = Auth::guard('admins')->user()->id;

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $inputs = $request->all();

        if ($request->input('password') != '') {
            $inputs['password'] = bcrypt($request->input('password'));
        } else {
            unset($inputs['password']);
        }

        Admin::findOrFail($id)->update($inputs);

        return redirect()->to('/bp-admin');
    }
}
