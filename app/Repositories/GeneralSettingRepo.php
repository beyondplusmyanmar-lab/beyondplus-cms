<?php

namespace App\Repositories;

use DB;
use Auth;
use DataTables;
use Session;
use Hash;

use App\Models\Country;
use App\Models\Addresses;
use App\Models\Languages;
use App\Models\Currency;
use App\Models\SiteSettings;
use App\Models\RewardPointRules;
use App\Models\PaymentTypes;
use App\Models\ShippingZoneMethods;
use App\Models\Bp_options;
use Illuminate\Http\Request;

class GeneralSettingRepo
{
    public function getLanguages() {
        $languages = Languages::where('status', '=', 'active')->get()
                    ->toArray();
        return $languages;
    } 

    public function getCurrency() {
        $currencies = Currency::where('status', '=', 'active')->get()
                    ->toArray();
        return $currencies;
    } 

    public function getDefaultCurrency() {
        $currency = DB::table('site_settings as ss')->select('ss.id', 'c.name', 'c.code', 'c.symbol', 'ss.default_currency', 'c.conversation_rate')
                        ->leftJoin('currency as c', 'c.id', '=', 'ss.default_currency')
                        ->where('c.status', '=', 'active')
                        ->get();
        return (isset($currency[0])) ? $currency[0] : '$' ;
    }

    //not inculde default currency
    public function getAllCurrency(){
        $currencies = DB::table('currency as c')->select('c.id', 'c.name', 'c.code', 'c.symbol')
                        ->join('site_settings as ss', 'c.id', '!=', 'ss.default_currency')
                        ->where('c.status', '=', 'active')
                        ->get();
        return $currencies;
    }

    public function getDefaultSettings(){
        $site_setting = SiteSettings::where('id', '=', 1)->with(['currency'])->get()
                    ->toArray();
        return $site_setting;
    }

    public function getRewardSettings(){
        $reward_point_rules = RewardPointRules::where('id', '=', 1)->get()
                    ->toArray();
        return $reward_point_rules;
    }

    public function getPaymentTypeBy($payment_name) {
        $payment_type = PaymentTypes::where('name', '=', $payment_name)->get()->first();
        return $payment_type;
    }

    public function getShippingTypeById($shipping_type,$sub_total,$allow_free_shipping = 0) {

        if($shipping_type != 0) {
            if($allow_free_shipping == 1) {
                $shipping_zone['data'] = ShippingZoneMethods::where('id', $shipping_type)->where('minimun_amount','<',$sub_total)->where('method_name','!=','flat_rate')->where('is_enabled', '1')->orderBy('method_order')->get(['id','method_name','cost', 'minimun_amount'])->first();
            } else {

                $shipping_zone['data'] = ShippingZoneMethods::where('id', $shipping_type)->where('minimun_amount','<',$sub_total)->where('is_enabled', '1')->orderBy('method_order')->get(['id','method_name','cost'])->first();

                // $query_shipping_zone = ShippingZoneMethods::where('method_name','free_shipping')->where('minimun_amount','!=',null)->where('minimun_amount','<',$sub_total)->where('is_enabled', '1')->get(['id','method_name','cost', 'minimun_amount']);

              

                // if(count($query_shipping_zone) > 0) {
                //     $shipping_zone['data'] = ShippingZoneMethods::where('id', $shipping_type)->where('is_enabled', '1')->get(['id','method_name','cost'])->first();


                // } else {
                //     $shipping_zone['data'] = ShippingZoneMethods::where('id', 2)->where('is_enabled', '1')->get(['id','method_name','cost'])->first();
                // }

            }
        } else {
            //check shipping method not 0 at checkout ui
            $shipping_zone['data']['id']      = 0;
            $shipping_zone['data']['cost']    = 0;
        }
        
        
        $shipping_zone['all'] = $this->checkShippingZone($shipping_type,$sub_total, $allow_free_shipping);
        
        return $shipping_zone;
    }

    public function checkShippingZone($shipping_type, $sub_total, $allow_free_shipping){
//        dd($allow_free_shipping);
        // default shipping type
        if($shipping_type != 0) {
            if($allow_free_shipping == 1) {
            // show free and local pickup
                $response = ShippingZoneMethods::where('method_name','!=','flat_rate')->where('minimun_amount','<',$sub_total)->where('is_enabled', '1')->orderBy('method_order')->get(['id','ship_type_title','cost']);
            } else {
                // find  mini amount and free shipping if not include type 1

                $query_shipping_zone = ShippingZoneMethods::where('id','>','1')->where('method_name','free_shipping')->where('minimun_amount','<',$sub_total)->where('is_enabled', '1')->get(['id','method_name','cost', 'minimun_amount']);
                if(count($query_shipping_zone) > 0) {
                    // die(($query_shipping_zone));
                    // free shipping amount, pick up if minimun_amount < total 
                    $response = ShippingZoneMethods::where('id','>','1')->where('method_name','!=','flat_rate')->where('minimun_amount','<',$sub_total)->where('is_enabled', '1')->orderBy('method_order')->get(['id','ship_type_title','cost']);
                    // dd('flat_rate');
                } else {
                    // show price and local pickup

                    $response = ShippingZoneMethods::whereIn('method_name', ['flat_rate','local_pickup'])->where('is_enabled', '1')->orderBy('method_order')->get(['id','ship_type_title','cost']);
                }
                
            }
        } else {

            $query_shipping_zone = ShippingZoneMethods::where('id','>','1')->where('method_name','free_shipping')->where('minimun_amount','<',$sub_total)->where('is_enabled', '1')->get(['id','method_name','cost', 'minimun_amount']);
            if(count($query_shipping_zone) > 0) {
                // die(($query_shipping_zone));
                // free shipping amount, pick up if minimun_amount < total 
                $response = ShippingZoneMethods::where('id','>','1')->where('method_name','!=','flat_rate')->where('minimun_amount','<',$sub_total)->where('is_enabled', '1')->orderBy('method_order')->get(['id','ship_type_title','cost']);
                // dd('flat_rate');
            } else {
                // show price and local pickup

                $response = ShippingZoneMethods::whereIn('method_name', ['flat_rate','local_pickup'])->where('is_enabled', '1')->orderBy('method_order')->get(['id','ship_type_title','cost']);
            }

            //$response = ShippingZoneMethods::where('id','>','1')->where('minimun_amount','<',$sub_total)->where('is_enabled', '1')->get(['id','ship_type_title','cost']);
        }
        
        
        return $response;
    }

    public function getCheckoutPermit(){
        $permit = [];
        // $permit['shipping_zone'] = ShippingZoneMethods::where('minimun_amount','<',$sub_total)->where('is_enabled', '1')->get(['id','ship_type_title','cost']);
        $permit['payment_type']  = PaymentTypes::where('status', 'active')->orderBy('weight')->get(['id','name']);
        $permit['country']       = Country::where('level','region')->orderBy('id')->get();

        if(Auth::guard("customer_web")->check()){

            $customers_id = Auth::guard("customer_web")->user()->id;
            $billing = Addresses::where('customers_id',$customers_id)->where('type','billing')->orderBy('id','desc')->first();
            $shipping_info = Addresses::where('customers_id',$customers_id)->where('type','shipping')->orderBy('id','desc')->first();

            if($billing) {

                $permit['billing_info']                     =  [];
                $permit['billing_info']['first_name']       =  $billing->first_name;
                $permit['billing_info']['last_name']        =  $billing->last_name;
                $permit['billing_info']['phone']            =  $billing->phone;
                $permit['billing_info']['email']            =  $billing->email;
                $permit['billing_info']['billing_address1']  =  $billing->address1;
                $permit['billing_info']['billing_address2']  =  $billing->address2;
                $permit['billing_info']['city']             =  $billing->city;
                $permit['billing_info']['province']         =  $billing->postcode;
                $permit['billing_info']['region']           =  $billing->region_id;
                $permit['billing_info']['country']          =  $billing->country_id;
                $permit['billing_info']['message']          = "";
               
            }

            if($shipping_info) {
                $permit['shipping_info'] = [];

                $permit['shipping_info']['shipping_first_name']        =  $shipping_info->first_name;
                $permit['shipping_info']['shipping_last_name']          =  $shipping_info->last_name;
                $permit['shipping_info']['shipping_phone']              =  $shipping_info->phone;
                $permit['shipping_info']['shipping_email']              =  $shipping_info->email;
                $permit['shipping_info']['shipping_address1']           =  $shipping_info->address1;
                $permit['shipping_info']['shipping_address2']           =  $shipping_info->address2;
                $permit['shipping_info']['shipping_city']               =  $shipping_info->city;
                $permit['shipping_info']['shipping_province']           =  $shipping_info->postcode;
                $permit['shipping_info']['shipping_region']             =  $shipping_info->region_id;
                $permit['shipping_info']['shipping_country']            =  $shipping_info->country_id;

            }

            // $customerAddresses = Auth::guard("customer_web")->user()->customerAddresses();

            // dd($addresses);
        }
        

        return $permit;
        // cost
    }

    public function get_individual_value($option_name) {
        $option_value = "";
        $result = Bp_options::where('option_name',$option_name)->first();

        if($result) {
            $option_value = $result->option_value;
        }

        return  $option_value;
    }

    

}
