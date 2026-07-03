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
use App\Admin;
use Validator;

class AccountController extends Controller
{
    public function __construct()
    {
       $this->middleware('admins');
    }

     public function index(Request $request){

        $query = Admin::orderBy('id', 'DESC');

        if ($request->name != null && $request->name != '0') {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->name.'%')
                  ->orWhere('email', 'like', '%'.$request->name.'%');
            });
        }

        $adminaccounts = $query->paginate(10);

        return view('bp-admin.account.index', array('adminaccounts' => $adminaccounts));
    }

    public function create(){



        //$brand= Brand::lists('brand_name','id');
        //return view('dashboard.create')->with('category',$categories);
        $adminaccounts = Admin::get();
        return view('bp-admin.account.add')->with(compact('adminaccounts'));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'role' => 'required',
            'email'=> 'required|email',
            'password'=> 'required|min:8'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Explicit allow-list — never mass-assign from $request->all().
        Admin::create([
            'name'      => $request->input('name'),
            'email'     => $request->input('email'),
            'role'      => $request->input('role'),
            'password'  => bcrypt($request->input('password')),
            'api_token' => bcrypt(time()),
        ]);
        return redirect()->to('bp-admin/account');
    }

    public function edit($id)
    {
        try {
            $adminaccounts = Admin::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return 'Product Not Found';
        }
       // $categories= User::lists('name','email');

        return view('bp-admin.account.edit')->with(compact('adminaccounts'));
    }


    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'role' => 'required',
            'email'=> 'required|email'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Explicit allow-list — never mass-assign from $request->all().
        $inputs = [
            'name'  => $request->input('name'),
            'email' => $request->input('email'),
            'role'  => $request->input('role'),
        ];
        if ($request->input('password') != "") {
            $inputs['password'] = bcrypt($request->input('password'));
        }

        Admin::findOrFail($id)->update($inputs);
        return redirect()->to('bp-admin/account');//return view
    }

    public function destroy($id)
    {
        Admin::find($id)->delete();

        return redirect()->back();
    }



}
