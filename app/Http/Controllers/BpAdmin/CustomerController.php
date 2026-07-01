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

class CustomerController extends Controller
{
    public function __construct()
    {
       $this->middleware('admins');
    }

     public function index(Request $request){


        if($request->start_date == null && $request->to_date==null && $request->customer_types_id == null && $request->phone==null && $request->name==null){
            $customers = Customers::orderBy('id', 'DESC')->paginate(10);
            // $customers = Customers::orderBy('id','desc')->get();
        }else{

            $customers = Customers::orderBy('id','desc');


            if ($request->name != null  ) {
                if($request->has('name')) {
                    if($request->has('name') && isset($request->name)) {   
                        // $customers = $customers->where('payment_transaction_id',$request->name);  
                        // echo $request->name;  
                        // - care         
                        $customers->orWhereRaw("first_name like '%" . $request->name . "%' ");
                    }
                }
            }

            

            if ($request->phone != null  ) {
                if($request->has('phone')) {
                    if($request->has('phone') && isset($request->phone)) {   
                        // $customers = $customers->where('payment_transaction_id',$request->phone);  
                        // echo $request->phone;  
                        // - care         
                        
                        $customers->where("phone",$request->phone);
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


            if ($request->customer_types_id != null  ) {
                if($request->has('customer_types_id')) {
                    if($request->customer_types_id != "") {
                        $customers = $customers->where('customer_types_id',$request->customer_types_id);
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
        
        return view('bp-admin.customer.index',array('user' => $customers ));
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

    public function show($id)
    {
        try {
            $user = Customers::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return 'Product Not Found';
        }
       // $categories= User::lists('name','email');

        return view('bp-admin.customer.show')->with(compact('user'));
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
