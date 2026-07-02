<?php

namespace App\Repositories;

use DB;
use Auth;
use Hash;
use Mail;
use Session;

//use Newsletter;
use App\Models\Addresses;
use App\Models\Customers;
use App\Models\CustomersReward;
use Illuminate\Http\Request;

// use App\Mail\NewRegisterMail;
// use App\Mail\ActivedAccountMail;
use App\Repositories\GeneralSettingRepo;
use App\Services\OtpNotifier;

class CustomersRepo
{
    protected $generalSettingRepo;
    protected $otpNotifier;

    public function __construct(GeneralSettingRepo $generalSettingRepo, OtpNotifier $otpNotifier) {
        $this->generalSettingRepo = $generalSettingRepo;
        $this->otpNotifier = $otpNotifier;
    }

    public function createCustomer($request)
    {
        return DB::transaction(function () use ($request) {

            $customers = new Customers();

            $input = $request->all();   
            // dd($input['subscribed_to_news_letter']);
            /*if(isset($input['email'])){
                Newsletter::subscribe($input['email']);
            } */
            // $setting_reward_rules = $this->generalSettingRepo->getRewardSettings();
            // $registration_reward_points = isset($setting_reward_rules[0]['new_member_registration']) ? $setting_reward_rules[0]['new_member_registration'] : 50;

            // $point_expire_years = isset($setting_reward_rules[0]['point_expire_years']) ? $setting_reward_rules[0]['point_expire_years'] : 1;
            // $default_point_expire_yr = "+".$point_expire_years." year";
            // $reward_expiry_date = date("Y-12-31", strtotime($default_point_expire_yr));

            $customers->first_name = $input['firstname'];
            $customers->last_name = $input['lastname'] ?? '';
            $customers->phone       = $input['phone'] ?? null;
            $customers->email       = $input['email'] ?? null;
            /*if(isset($input['subscribed_to_news_letter'])){
                $input['subscribed_to_news_letter'] = 1;
            }else{
                $input['subscribed_to_news_letter'] = 0;
            }*/
            // $input['date_of_birth'] = null;

            // $input['date_of_birth'] = ($input['dob']) ? date('Y-m-d', strtotime($input['dob'])) : null;
            
            $customers->customer_types_id = 1;
            $customers->is_verified = 0;
            $customers->status = 1;
            $customers->total_reward_points = 0; //for new customer reward points
            // $customers->reward_expiry_date = $reward_expiry_date; //update reward point end-date for customer
            $customers->otpcode = mt_rand(100000,999999);
            $customers->activation_code = sha1(mt_rand(10000,99999).time().($input['phone'] ?? $input['email'] ?? ''));
            $customers->password = Hash::make($input['password']);
            
            //$input['subscribed_to_news_letter'] = $input['subscribed_to_news_letter'] ?? false;

            // $customers = new Customers();
            // $saved = $customers->fill($input)->save();

            $saved = $customers->save();


            if($saved) {
                // Deliver the OTP over the configured channel (SMS/email), or log it
                // when no gateway is enabled (see admin Configuration page).
                $this->otpNotifier->send($customers, $customers->otpcode);
            }

            // if ($customers->subscribed_to_news_letter) {
            //     Newsletter::subscribe($customers->email);
            // }
            // //reward point
            // $reward = array('customers_id'=> $customers->id, 'reward_type'=> 'registration', 'description'=> 'new registration.', 'action_id'=> 0, 'action_type'=> 'sum', 'point'=> $registration_reward_points);
            // $this->addrewardpoint($reward);

            // try{
            //     $data = [
            //         "from_name" => env('MAIL_NAME', 'Toast for Wine'),
            //         "from_email" => env('MAIL_USERNAME', 'admin@example.com'),
            //         'name' => $input['firstname'].' '.$input['lastname'],
            //         'email' => $input['email'], //$input['email'], 
            //         'activate_link' => url('customer/activate/'.$input['activation_code'])
            //     ];
            //     $activate_link = $data['activate_link'];
            //     Mail::to($data['email'])->send(new ActivedAccountMail($activate_link, $data));
            //     Mail::to($data['from_email'])->send(new NewRegisterMail($data));

            // }catch(\Exception $ex){
            //    var_dump("Error", $ex->getMessage());
            // }

            return ($saved) ? $customers : false;
        });
    }

    public function getCustomerAddress($customer_id, $address_type)
    {
        $customer_address = Addresses::where('customers_id', '=', $customer_id)->where('type', '=', $address_type)->first();
        return $customer_address;
    }

    public function createCustomerAddress($request){
        $input = $request->all();
        $customer_id = Auth::guard('customer_web')->user()->id;

        $addresses = new Addresses();
        $addresses->customers_id = $customer_id;
        $addresses->type = $input['address_type'];
        $addresses->first_name = $input['first_name'];
        $addresses->last_name = $input['last_name'];
        $addresses->company_name = $input['company_name'];
        $addresses->address1 = $input['address1'];
        $addresses->address2 = $input['address2'];
        /*$addresses->postcode = $input['postcode'];*/
        $addresses->country_id = $input['country'];
        $addresses->region_id = $input['region_id'];
        $addresses->city = $input['city'];
        $addresses->state = $input['state'];
        $addresses->phone = $input['phone'];
        $addresses->email = $input['email'];
        return ($addresses->save()) ? $addresses : false;
    }

    public function updateCustomerAddress($request, $id){
        $input = $request->all();
        
        $addresses = Addresses::find($id);
        $addresses->first_name = $input['first_name'];
        $addresses->last_name = $input['last_name'];
        $addresses->company_name = $input['company_name'];
        $addresses->address1 = $input['address1'];
        $addresses->address2 = $input['address2'];
        /*$addresses->postcode = $input['postcode'];*/
        $addresses->country_id = $input['country'];
        $addresses->region_id = $input['region_id'];
        $addresses->city = $input['city'];
        $addresses->state = $input['state'];
        $addresses->phone = $input['phone'];
        $addresses->email = $input['email'];
        return ($addresses->save()) ? $addresses : false;
    }

    public function addrewardpoint($data){
        $customersReward = new CustomersReward();
        $saved = $customersReward->fill($data)->save();

        $customer = Customers::where('id', $data['customers_id'])->first();
        if($customer) {
            if($data['action_type'] == 'sum'){
                $customer->total_reward_points += $data['point'];
                $customer->save();
            }else if($data['action_type'] == 'deduct'){
                $customer->total_reward_points -= $data['point'];
                $customer->save();
            }
        }

        return ($saved) ? $customersReward : false;
    }

}
