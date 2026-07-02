<?php

namespace App\Http\Controllers\Auth;

use DB;
use Hash;

use Session;
use Validator;

use App\Models\Customers;
use Illuminate\Http\Request;
use App\Repositories\CustomersRepo;
use Illuminate\Hashing\BcryptHasher;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Mail;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException;
use IlluminateAgnostic\Collection\Support\Str;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';
    protected $customersRepo;
    protected $otpNotifier;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct( CustomersRepo $customersRepo, \App\Services\OtpNotifier $otpNotifier)
    {

      $this->customersRepo      = $customersRepo;
      $this->otpNotifier        = $otpNotifier;

    }


    protected function guard()
    {
        return Auth::guard('customer_web');
    }

    public function signin()
    {
        // Auth::guard("customer_web")->logout();
        // dd(Auth::guard('customer_web')->user());
        return view('front.customer.sign-in');
    }

    //customer profile page
    public function profile()
    {
        if (! Auth::guard('customer_web')->check()) {
            return redirect('customer/sign-in');
        }

        return view('front.customer.profile', [
            'customer' => Auth::guard('customer_web')->user(),
        ]);
    }

    //customer register page
    public function signup()
    {
        return view('front.customer.sign-up');
    }

    //create new customer
    public function customer_register(Request $request)
    {
        $input = $request->all();

        // Required fields depend on the configured registration method.
        $regType = bp_option('registration_type', 'phone');
        $rules = [
            'firstname' => 'required',
            'password'  => 'required|confirmed|min:8',
        ];
        if ($regType === 'phone' || $regType === 'both') {
            $rules['phone'] = 'required|unique:customers|min:10';
        }
        if ($regType === 'email' || $regType === 'both') {
            $rules['email'] = 'required|email|unique:customers';
        }

        $validator = Validator::make($input, $rules);


        if($validator->fails()) {

          return redirect()->back()->withErrors($validator)->withInput();

        }

        $saved = $this->customersRepo->createCustomer($request);

        if($saved) {
          // Identifier used for the OTP / activation step (phone or email).
          $identifier = ($regType === 'email') ? $input['email'] : $input['phone'];
          $request->session()->put('verify_phone', $identifier);
          $request->session()->put('opt_status', "register" );
          
          return redirect('customer/activate')->with('flash_message', 'Your account is created.
            Please check your otp code.');

        }else{
            return redirect('customer/sign-in')->with('flash_message', 'Registeration has error!');
        }
    }


    //for register account activation


    public function activation(Request $request)
    {
        if ($request->session()->get('verify_phone')) {

            $phone = $request->session()->get('verify_phone');
            // $request->session()->forget('verify_phone');

            return view('front.customer.activation');

        } else {
            return abort('404');
        }

    }

    // public function activation(Request $request)
    // {
    //     $email = $email;
    //     return view('front.customer.activation')->with('email');
    // }

    //for register account activation
    public function customer_activation(Request $request)
    {

        // otp from register or forget pass
        if ($request->session()->get('verify_phone')) {

            $phone = $request->session()->get('verify_phone');
            $opt_status = $request->session()->get('opt_status');
            // $request->session()->forget('verify_phone');

            $activation_code = $request->activation_code;

              // $phone holds whichever identifier was used at registration (phone or email).
              $customer = Customers::where('otpcode', $activation_code)
                  ->where(function ($q) use ($phone) {
                      $q->where('phone', $phone)->orWhere('email', $phone);
                  })->first();
              
              if($customer) {

                  //----------------------------
                  if($opt_status == "register") {

                    if($customer->is_verified == 0) {
                        $customer->is_verified = 1;
                        // $customer->reward_expiry_date = date("Y-12-31", strtotime("+1 year"));
                        $customer->save();

                        $msg = ['status' => 'Success','message' => 'Your otp code has been successfully verified.'];  
                    } else {
                        $msg = ['status' => 'Error','message' => "Your otp code has been verified. Please use your credentials to sign in."];
                    }

                    $request->session()->forget('verify_phone');

                    return redirect('customer/sign-in')->with('msg', $msg);

                  } 

                  //----------------------------
                  if($opt_status == "forgotpass") {


                      $msg = ['status' => 'Success','message' => 'Your otp code has been successfully verified.'];  

                      // $request->session()->forget('verify_phone');

                      return redirect('customer/new-password')->with('msg', $msg);

                  }
                  
              }
              else {

                // return $msg = "Your otp code has been wrong."; 
                // return view('front.customer.activation')->with('flash_message', "Your otp code has been wrong.");
                return redirect('customer/activate')->with('flash_message', "Your otp code has been wrong.");

              }

        } else {

            return abort(404);

        }

        
    }


    // //for register account email activation
    // public function activation(Request $request, $activation_code)
    // {
    //     $activation_code = $request->activation_code;

    //     $customer = Customers::where('activation_code', $activation_code)->first();
        
    //     if($customer) {
    //         if($customer->is_verified == 0) {
    //             $customer->is_verified = 1;
    //             // $customer->reward_expiry_date = date("Y-12-31", strtotime("+1 year"));
    //             $customer->save();

    //             $msg = ['status' => 'Success','message' => 'Your email has been successfully verified.'];  
    //         }
    //         else {
    //             $msg = ['status' => 'Error','message' => "Your email has been verified. Please use your credentials to sign in."];  
    //         }
    //         return redirect('customer/sign-in')->with('msg', $msg);
    //     }
    //     else {
    //         return abort(404);
    //     }
    // }

    public function login(Request $request)
    {
        $input = $request->all();
        
        // Check validation
        $this->validate($request, [
            'phone' => 'required',
            'password' => 'required',           
        ]);

        //dd($request->remember);

        if($request->remember) {
          $request->remember = 1;
        } else {
          $request->remember = 0;
        }

        // dd($request->remember);

        // $remember = $req->get('remember');
        // if(Auth::attempt(['email' => $req->email, 'password' => $req->password], $remember))

        // The sign-in field ("phone") may hold a phone number or an email,
        // depending on the configured registration method.
        $identifier = $request->phone;
        $loginField = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $customer=Customers::where('phone',$identifier)->orWhere('email',$identifier)->first();

        if(!is_null($customer)){
          if(Hash::check($request->password, $customer->password)){
            if($customer->status==1){
              if($customer->is_verified==1){
                if(Auth::guard('customer_web')->attempt([$loginField=>$identifier,'password'=>$request->password],$request->remember)){
                  return redirect('/');
                }
              }else{
                if(Auth::guard('customer_web')->attempt([$loginField=>$identifier,'password'=>$request->password],$request->remember)){
                  if(Auth::guard("customer_web")->check()){
                    Auth::guard("customer_web")->logout();
                  }
                  return redirect()->back()->with('flash_message','A new confirmation mail has sent to you...Please check and confirm your phone');
                }else{
                    return redirect()->back()->with('flash_danger', 'Password is incorrect!!');
                }
              }
            }else{
              return redirect()->back()->with('flash_message', 'Your account is inactive. Please contact to site admin!!');
            }
          }else{
            return redirect()->back()->with('flash_danger', 'Password is incorrect!!');
          }
          
        }else{
          return redirect()->back()->with('flash_danger', 'Your phone or password is incorrect..!!');
        }

        
        // if(Auth::guard('customer_web')->attempt(['email' => $request->email, 'password' => $request->password, 'is_verified' => 1])){
        // // Authentication passed...
        //   return back()->with('flash_message', 'Your login success..!!');
        // }
        
    }

    public function logout(Request $request) 
    {
      Auth::guard("customer_web")->logout();
      return redirect()->to('/');
    }

    public function forgotpass()
    {
      return view('front.customer.forgot-password');
    }

    public function post_forgotpass(Request $request)
    {
      


      if($request->has('phone')) {

        $customer = Customers::where('phone', $request->phone)->first();

        if($customer) {

          $request->session()->put('verify_phone',$request->phone);
          $request->session()->put('opt_status', "forgotpass" );

          $customer->otpcode = mt_rand(100000,999999);
          $saved = $customer->save();

          if($saved) {

            // Deliver the OTP over the configured channel (SMS/email), or log it.
            $this->otpNotifier->send($customer, $customer->otpcode);

            return redirect('customer/activate')->with('flash_message', 'Please check your otp code.');

          } else {

            return redirect()->back()->with('flash_danger', 'Have you registered ?'); 

          }

        } else {
          return redirect()->back()->with('flash_danger', 'Have you registered ?'); 
        }
        
          
      } else {
          return redirect()->back()->with('flash_danger', 'Please Fill Up the phone !!');
      }

    }


    // after opt verify
    public function newPassword(Request $request)
    {
      if($request->session()->get('opt_status') == "forgotpass") {
        $phone = $request->session()->get('verify_phone');
        $opt_status = $request->session()->get('opt_status');
        return view('front.customer.new-password');
      }
        
    }

    //change new password
    public function saveNewPassword(Request $request)
    {
        if($request->session()->get('opt_status') == "forgotpass") {


          $phone = $request->session()->get('verify_phone');

          // $input = $request->all();

          $input = $request->all();
        
          $validator = Validator::make($input, [
            'new_password' => 'required',
            'new_confirm_password' => 'same:new_password',
            ]
          );        
            
          if($validator->fails()) { 

            return redirect()->back()->withErrors($validator)->withInput();

          }   
          
          $customer = Customers::where('phone', $phone )->update(['password'=> Hash::make($request->new_password)]);

          // $request->session()->forget('verify_phone');
          // $request->session()->forget('opt_status');

          if($customer) {
            return redirect('customer/sign-in')->with('flash_message', 'Password change successfully.');
          }
          

        } else {

          return abort('404');

        }

    }
}