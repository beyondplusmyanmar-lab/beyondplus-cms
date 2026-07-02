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


        // if($request->start_date == null && $request->to_date==null && $request->role == null && $request->email==null && $request->name==null){
        if($request->filter == null ){
            $customers = Admin::orderBy('id', 'DESC')->paginate(10);
            // $customers = Customers::orderBy('id','desc')->get();
        }else{

            $customers = Admin::orderBy('id','desc');



            if ($request->name != null  ) {
                if($request->has('name')) {
                    if($request->has('name') && isset($request->name)) {   
                        // $customers = $customers->where('payment_transaction_id',$request->name);  
                        // echo $request->name;  
                        // - care         
                        $customers->orWhereRaw("first_name like ?", ['%' . $request->name . '%']);
                    }
                }
            }

            

            // if ($request->phone != null  ) {
            //     if($request->has('phone')) {
            //         if($request->has('phone') && isset($request->phone)) {   
            //             // $customers = $customers->where('payment_transaction_id',$request->phone);  
            //             // echo $request->phone;  
            //             // - care         
                        
            //             $customers->where("phone",$request->phone);
            //         }
            //     }
            // }


            if ($request->email != null  ) {
                if($request->has('email')) {
                    if($request->has('email') && isset($request->email)) {   
                        // $customers = $customers->where('payment_transaction_id',$request->email);  
                        // echo $request->email;  
                        // - care         
                        
                        $customers->where("email",urldecode($request->email));
                    }
                }
            }
            
            
            if ($request->start_date != null  ) {
                if ($request->has('start_date') && isset($request->start_date)) {
                    $customers = $customers->whereDate('created_at', '>=', \Carbon\Carbon::parse($request->start_date)->format('Y-m-d'));

                }
            }


            if ($request->to_date != null  ) {
                if ($request->has('to_date') && isset($request->to_date)) {
                    $customers = $customers->whereDate('created_at', '<=',\Carbon\Carbon::parse($request->to_date)->format('Y-m-d'));

                }
            }


            if ($request->role != null  ) {
                if($request->has('role')) {
                    if($request->role != "") {
                        $customers = $customers->where('role',$request->role);
                    }
                    
                    // echo $request->order_status;
                }
            }

            if ($request->filter != null  ) {
                if($request->has('filter')) {
                    if($request->filter != "") {
                        $customers = $customers->where('department_type',$request->filter);
                    }
                    
                    // echo $request->order_status;
                }
            }



            


            $customers = $customers->paginate(10);

        }

        // if($request->search) {
        //     $user = Customers::where('first_name','like','%'.$request->search.'%')->orderBy('id', 'DESC')->paginate(10);
        // } else {
        //     $user = Customers::orderBy('id', 'DESC')->paginate(10);
        // }
        
        return view('bp-admin.account.index',array('adminaccounts' => $customers ));
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
            'email'=> 'required',
            'password'=> 'required'
        ]);

        if ($validator->fails()) {  
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $inputs = $request->all();
        $inputs['api_token'] = bcrypt(time());

        $inputs['password'] = bcrypt($request->input('password'));
        Admin::create($inputs);
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
            'email'=> 'required'
        ]);

        if ($validator->fails()) {  
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $inputs = $request->all();
        // dd($inputs);
     //   $inputs = $request->except('_token', '_method');
        if($request->input('password') != "") {
            $inputs['password'] = bcrypt($request->input('password'));
        } else {
            unset($inputs['password']);
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
