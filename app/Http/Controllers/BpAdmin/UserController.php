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
// use App\User;
use App\Models\Customers;
use Validator;
// use Illuminate\Foundation\Auth\ThrottlesLogins;
// use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class UserController extends Controller
{
    public function __construct()
    {
       $this->middleware('admins');
    }

     public function index(Request $request){
        if($request->search) {
            $user = Customers::where('first_name','like','%'.$request->search.'%')->orderBy('id', 'DESC')->paginate(10);
        } else {
            $user = Customers::orderBy('id', 'DESC')->paginate(10);
        }
        
        return view('bp-admin.customer.index',array('user' => $user ));
    }

    public function create(){
        return view('bp-admin.customer.add');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'first_name' => 'required', 
            'email'=> 'required',
            'password'=> 'required'
        ]);

        if ($validator->fails()) {  
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $inputs = $request->all();
        // $inputs['role'] = 1;
        // $inputs['api_token'] = str_random(60);
        $inputs['customer_types_id'] = 1;
        $inputs['password'] = bcrypt($request->input('password'));
        Customers::create($inputs);
        return redirect()->to('bp-admin/user');
    }

    public function edit($id)
    {
        try {
            $user = Customers::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return 'Product Not Found';
        }
       // $categories= User::lists('name','email');

        return view('bp-admin.customer.edit')->with(compact('user'));
    }


    public function update($id, Request $request)
    {
        $inputs = $request->all();
        // $inputs['role'] = 1;
        $inputs['password'] = bcrypt($request->input('password'));
        Customers::findOrFail($id)->update($inputs);
        return redirect()->to('bp-admin/user');
    }

    public function destroy($id)
    {
        Customers::find($id)->delete();
        return redirect()->back();
    }



}
