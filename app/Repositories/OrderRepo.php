<?php

namespace App\Repositories;

use DB;
use Auth;
use Hash;
use Mail;
use PDF;
use Session;

use App\Models\Order;
// use App\Coupon;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\Addresses;
use App\Models\Customers;
use App\Models\OrderItems;
use App\Models\CustomerTypes;
use App\Models\CustomersReward;
use App\Models\RewardPointRules;
use App\Models\Transactions;
use Illuminate\Http\Request;

// use Newsletter;
use App\Repositories\GeneralSettingRepo;

use App\Services\OtapiService;

class OrderRepo
{
    protected $generalSettingRepo;
    protected $checkFinishedRule;
    protected $hints;
    protected $limit_item_count;
    protected $cartRepo;
    protected $otapiService;

    public function __construct(GeneralSettingRepo $generalSettingRepo, CartRepo $cartRepo, OtapiService $otapiService) {
        $this->cartRepo             = $cartRepo;
        $this->generalSettingRepo   = $generalSettingRepo;
        $this->checkFinishedRule    = [];
        $this->hints                = [];
        $this->limit_item_count     = 0;
        $this->otapiService         = $otapiService;
    }

    public function saveOrder(Request $request, $cartdata, $id = NULL)
    { 

        $res['status_code']  = "200";
        $res['message'] = "";

        $cart_items                 = $cartdata;


        $input                      = $request->body;
        $customers_id               = Auth::guard("customer_web")->user()->id;

        // $input['status'] = ($input['status'] == 1) ? "active" : "inactive";

        $order = new Order();

        // $order->first_name          =    $input['billing_info']['first_name'];
        // $order->last_name           =    $input['billing_info']['last_name'];

        // $order->phone               =    $input['billing_info']['phone'];
        // $order->email               =    $input['billing_info']['email'];
        // $order->billing_address1    =    $input['billing_info']['billing_address1'];
        // $order->billing_address2    =    $input['billing_info']['billing_address2'];
        // $order->city                =    $input['billing_info']['city'];
        // $order->province            =    $input['billing_info']['province'];
        // $order->region              =    $input['billing_info']['region'];
        // $order->country             =    $input['billing_info']['country'];
        $order->message             =    $input['billing_info']['message'];

        $order->invoice_no          = uniqid();
        $order->customers_id        = $customers_id;
        $order->customer_type       = Auth::guard("customer_web")->user()->customer_types_id;
        $order->currency_id         = 3;
        // $order->discount_amount     = $cart_items['discount'];
        $order->sub_total           = $cart_items['sub_total'];
        $order->service_fee         = $cart_items['service_fee'];
        $order->grand_total         = $cart_items['total'];
        $order->total_item_count    = count($cart_items['product_ids_qty']);
        $order->order_note          = $cart_items;
        $order->payment_type        = $input['payment']['type'];
        // $order->coupon_code         = $cart_items['coupon_code'];
        // $order->shipping_method     = $input['shipping_method'];
        $order->shipping_method     = 1;

        //domestic delivery fee
        $order->shipping_amount     = $cart_items['delivery_price'];

        $order->usedwallet              = $cart_items['wallet'];



        // $order->shipping_amount     = ($cart_items['shipping_amount'] == 'Free') ? 0 : $cart_items['shipping_amount'];
        // $order->order_reward_points = $this->cartRepo->getCartRedeemPoints();

        // if($input['shipping']['checked']) {

                // shipping_address : {
            $order->shipping_first_name =    $input['shipping_address']['shipping_first_name'];
            $order->shipping_last_name  =    $input['shipping_address']['shipping_last_name'];

            $order->shipping_phone     =    $input['shipping_address']['shipping_phone'];
            $order->shipping_email     =    $input['shipping_address']['shipping_email'];
            $order->shipping_address1  =    $input['shipping_address']['shipping_address1'];
            $order->shipping_address2  =    $input['shipping_address']['shipping_address2'];
            $order->shipping_city      =    $input['shipping_address']['shipping_city'];
            $order->shipping_province  =    $input['shipping_address']['shipping_province'];
            $order->shipping_region    =    $input['shipping_address']['shipping_region'];
            $order->shipping_country   =    $input['shipping_address']['shipping_country'];


        // } else {

        //     $order->shipping_first_name =    $input['billing_info']['first_name'];
        //     $order->shipping_last_name  =    $input['billing_info']['last_name'];

        //     $order->shipping_phone      =    $input['billing_info']['phone'];
        //     $order->shipping_email      =    $input['billing_info']['email'];
        //     $order->shipping_address1  =    $input['billing_info']['billing_address1'];
        //     $order->shipping_address2  =    $input['billing_info']['billing_address2'];
        //     $order->shipping_city      =    $input['billing_info']['city'];
        //     $order->shipping_province  =    $input['billing_info']['province'];
        //     $order->shipping_region    =    $input['billing_info']['region'];
        //     $order->shipping_country   =    $input['billing_info']['country'];

        //         //$data['shipping_address_id'] = $addresses->id;

        // }

        $saved = $order->save();

        // ota cancel api and insert

        // // insert order_items from session
       

        if($saved) {
            // clear basket for no duplicate
            // http://otapi.net/service-json/ClearBasket?instanceKey=&language=&sessionId=
            if(isset($cart_items['mycart'])) {

                $comment = "sesson clear from user";
                $this->otapiService->clearBasket($cart_items['mycart'], $comment);

                foreach($cart_items['mycart'] as $mycart) {

                    // echo  json_encode($mycart);

                    // die();

                    $order_items = new OrderItems;


                    $order_items->orders_id = $order->id;
                    $order_items->product_id = $mycart['product_id'];
                    $order_items->sku       = $mycart['id']; // configure id added
                    $order_items->product_type = $mycart['product_type'];
                    $order_items->provider_type = $mycart['provider_type'];
                    $order_items->name      = $mycart['name'];
                    //todo
                    //$order_items->coupon_code = $mycart->coupon_code;
                    $order_items->weight = $mycart['weight'];
                    $order_items->qty_ordered = $mycart['quantity'];
                    //todo
                    // $order_items->discount_percent = $mycart['discount_percent'];
                    // $order_items->discount_amount = $mycart['discount_amount'];
                    //if saleprice , save normal price
                    $order_items->price = $mycart['price'];
                    $order_items->total = $mycart['item_total'];
                    $order_items->message  = $mycart['PlusMessage'];
                    
                    $order_items->save();

                    if($mycart['wishlist'] == 1)
                    {
                        $this->removeWishlistItems($order_items->product_id, Auth::guard("customer_web")->user()->id);
                    }
                    
                    // otc data import and get order id
                    $comment = "MSK order id ".$order->id;
                    $this->otapiService->batchSimplifiedAddItemsToBasket($mycart, $comment);

                }

                $response = $this->otapiService->CreateSalesOrder(1,"MSK order id ".$order->id);

                //dd($response);


                if($response['ErrorCode'] == "Ok") {
                    $payment_transaction_id          = $response['Result']['Id'];

                     $order->payment_request_id      = $response['RequestId'];
                     $order->payment_transaction_id  = $response['Result']['Id'];
                     $order->payment_status          = 2;
                     $order->save();

                     // update transaction id 
                     OrderItems::where('orders_id' , $order->id )->update(
                                ['payment_transaction_id' => $payment_transaction_id ]
                            );
                } else { 

                    $response = $this->otapiService->CreateSalesOrder(1,"MSK order id ".$order->id);

                    if($response['ErrorCode'] == "Ok") {
                        $payment_transaction_id          = $response['Result']['Id'];

                         $order->payment_request_id      = $response['RequestId'];
                         $order->payment_transaction_id  = $response['Result']['Id'];
                         $order->payment_status          = 2;
                         $order->save();

                         // update transaction id 
                         OrderItems::where('orders_id' , $order->id )->update(
                                    ['payment_transaction_id' => $payment_transaction_id ]
                                );
                    } else { 

                        $order = Order::where('id', $order->id)->delete();

                        $res['status_code']  = "404";
                        //$res['message'] = "Please try again";
                        $res['message'] = $response['ErrorDescription'];
                        return $res;
                    }
                }

            }

            if($order->usedwallet > 0) {
                // reduce wallet point
                $customers = Customers::where('id',$order->customers_id)->first();
                $customers->decrement('wallets',$order->usedwallet);
                $customers->save();

                $transactions = new Transactions;
                $transactions->amount = $order->usedwallet;
                $transactions->meta = "Paid for order ". $order->id;
                $transactions->type = 3;
                $transactions->slip_id = "order-".$order->id;
                $transactions->customer_id = $order->customers_id;
                $saved = $transactions->save();
            }
        }

        // if($response['ErrorCode'] == "Ok") {

        //     if(isset($response['SalesLinesList'])) {
        //         foreach($response['SalesLinesList'] as $salesLinesList) {

        //             $order_optitems = new OrderOptitems;
                    
                    
        //             $order_optitems->item_data              = $salesLinesList;
        //             $order_optitems->payment_transaction_id = $salesLinesList['OrderId'];
        //             $order_optitems->tacking_id             = $salesLinesList['Id'];
        //             $order_optitems->tacking_status         = $salesLinesList['tacking_status'];

        //             $order_optitems->save();
        //         }
        //     }
        // }


        // otc data import and get order id
        // if(isset($cart_items['mycart'])) {
        //     $this->otapiService->batchSimplifiedAddItemsToBasket($cart_items['mycart']);
        // }
        // return $res;
        if($saved) {
            $res['message'] = $order;
        }
        
        return $res;
    }

    public function removeWishlistItems($product_id, $customers_id)
    {
        return Wishlist::where('product_id',$product_id)->where('customers_id',$customers_id)->delete();
    }

    public function paymePaymentStatus($request)
    {

        // order status update filter with payment id
        $order = new Order();

        $input = $request->all();

        if(isset($input['statusCode'])) {
            $statusCode         = $input['statusCode'];
            $payment_request_id = $input['paymentRequestId'];
            $transactions       = $input['transactions'];


            $order = Order::where('payment_request_id', $payment_request_id)->where('payment_type','3')->first();

            // const PAYMENT_STATUS = [
            //     1 => 'Pending',
            //     2 => 'Processing',
            //     3 => 'Failed',
            //     4 => 'Success',
            //     5 => 'Expired',
            // ];

            if($order) {
                switch ($statusCode) {
                  case "PR005":
                  $data['payment_transaction_id'] = $transactions[0]['transactionId'];
                  $data['payment_request_id']     = $payment_request_id;
                  $data['payment_status']         = "4";
                  $data['order_status']           = "2";

                  $saved = $order->fill($data)->save();

                  // ($saved) ? $this->rewardPointToCustomer($order, $order->customers_id) : false;

                  break;
                  case "PR004":
                  $data['payment_transaction_id'] = 1;
                  $data['payment_request_id']     = $payment_request_id;
                  $data['payment_status']         = "3";
                  $data['order_status']           = "1";

                  $saved = $order->fill($data)->save();

                  break;
                  case "PR007":
                  $data['payment_transaction_id'] = 1;
                  $data['payment_request_id']     = $payment_request_id;
                  $data['payment_status']         = "5"; 
                  $data['order_status']           = "3";

                  $saved = $order->fill($data)->save();

                  default:
                  return json_encode(['']);
              }
          }
      } else {

        $saved = $order->fill(['order_status' => base64_encode(serialize($input)),'customers_id' => 1 , 'currency_id' => 2])->save();
        return ($saved) ? $order : false;
    }

}

public function reduceRewardPointToCustomer($order, $customers_id, $previous_order_status, $order_status) {

    //     1 => 'Pending',
    //     2 => 'Processing',
    //     3 => 'Cancel',

    if(in_array($order_status, [1,2,3])) {
        // echo $previous_order_status;
        // echo "<br/>";
        // echo $order_status;
        // die();

        // sum status check
        if(in_array($previous_order_status, [4,5])) 
        {

            $customer = Customers::find($customers_id);

            $customers_reward = CustomersReward::where('action_id',$order->id)->where('customers_id',$customers_id)->get();

            $c_reward = $customers_reward;

            // print_r($order->order_note->can_reward_points);
            // die();
            // print_r($order);
            // die();
            //reduce start
            $customer->decrement("total_subtotal_amount",round($order->sub_total));
            $customer->decrement("total_reward_points",$order->order_note->can_reward_points);
            $customer->save();

            $customers_reward = new CustomersReward();
            $customers_reward->customers_id     = $customers_id;
            $customers_reward->reward_type      = "spend_amount";
            $customers_reward->description      = "Reduced reward point from invoice number #".$order->id.' cancelation';
            $customers_reward->action_id        = $order->id;
            $customers_reward->action_type      = "subtract";
            $customers_reward->point            = $order->order_note->can_reward_points;
            $customers_reward->save();



            foreach ($c_reward as $key => $value) {

                if($value->reward_type == "redeem_amount" && $value->action_type == "subtract") {

                    $customer->increment("total_reward_points",$value->point);

                    $customers_reward = new CustomersReward();
                    $customers_reward->customers_id     = $customers_id;
                    $customers_reward->reward_type      = "redeem_amount";
                    $customers_reward->description      = "Added redeem point from invoice number #".$order->id.' cancelation';
                    $customers_reward->action_id        = $order->id;
                    $customers_reward->action_type      = "sum";
                    $customers_reward->point            = $value->point;
                    $customers_reward->save();

                    break;

                }
                
            }

            //change customer type
            $customerType = CustomerTypes::get();

            $temp_customer_type = 1;

            foreach ($customerType as $key => $value) {
                if($customer->total_subtotal_amount > $value->total_spend_amount){
                    $temp_customer_type = $value->id;
                }
            }

            $customer->customer_types_id = $temp_customer_type;
            $customer->save();

            $this->stockControl($order,  "plus");

        }

        return $order_status = '200';

    } 

    return $order_status = '404';
    
    
}

public function rewardPointToCustomer($order, $customers_id) {


    $shipping_amount = 0;
        //$customers_id = $customers_id;

    $get_reward_point_rules = RewardPointRules::first();

    if($order->shipping_amount) {
        $shipping_amount = $order->shipping_amount;
    }

    // calculate reward point
    $reward_points = $order->order_note->can_reward_points;
    //$reward_points = round(round($order->sub_total)/$get_reward_point_rules->spend_amount_per_point);

    $customer = Customers::find($customers_id);
    $customer->increment("total_subtotal_amount",round($order->sub_total));
    $customer->increment("total_reward_points",$reward_points);
    $customer->save();
        // sum total reward point 
    
    // $saved = Customers::where("id",$customers_id)

    if($order->coupon_code != ""){
        $coupon = Coupon::where('coupon_code',$order->coupon_code)->first();
        $user[] = $customers_id;

        $coupon->used_coupon = $coupon->used_coupon+1;

        if($coupon->used_user) {
            $coupon->used_user = array_merge($coupon->used_user,$user);
        } else {
            $coupon->used_user = $user;
        }
        
        $coupon->save();
        // $coupon->increment("used_coupon",1);
    }
    

    //change customer type
    $customerType = CustomerTypes::get();

    $temp_customer_type = 1;

    // $customer = Customers::find($customers_id);

    foreach ($customerType as $key => $value) {
        if($customer->total_subtotal_amount > $value->total_spend_amount){
            $temp_customer_type = $value->id;
        }
    }

    $customer->customer_types_id = $temp_customer_type;
    $customer->save();

    // end customer type change

    // reward history
    $customers_reward = new CustomersReward();
    $customers_reward->customers_id     = $customers_id;
    $customers_reward->reward_type      = "spend_amount";
    $customers_reward->description      = "Reward point from invoice number #".$order->id;
    $customers_reward->action_id        = $order->id;
    $customers_reward->action_type      = "sum";
    $customers_reward->point            = $reward_points;
    $customers_reward->save();

    if($order->order_reward_points) {

        $customer->decrement("total_reward_points",$order->order_reward_points);
        $customer->save();

        $customers_reward = new CustomersReward();
        $customers_reward->customers_id     = $customers_id;
        $customers_reward->reward_type      = "redeem_amount";
        $customers_reward->description      = "Redeem point from invoice number #".$order->id;
        $customers_reward->action_id        = $order->id;
        $customers_reward->action_type      = "subtract";
        $customers_reward->point            = $order->order_reward_points;
        $customers_reward->save();
    }

    //reduce reward point if use redeem product
    
    $this->stockControl($order,  "minus");

    return ($customers_reward) ? true : false;
}

public function stockControl($order, $status)
{
    //listen only substract
    $outofstock = [];

    if($status == "minus") {

        $getDefaultSettings = $this->generalSettingRepo->getDefaultSettings();

        foreach ($order->orderItems as $key => $item) {

            $product = Product::find($item->product_id);

            //manage stock check
            if($product->stock_type == 2) {
                $product->decrement("stock",$item->qty_ordered);
                $product->save();

                if($product->stock < $getDefaultSettings[0]['stock_limit']) {
                    $outofstock[]   = $item->product_id;
                }
            }
            
            

        }

    } else {

        foreach ($order->orderItems as $key => $item) {

            $product = Product::find($item->product_id);

            //manage stock check
            if($product->stock_type == 2) {
                $product->increment("stock",$item->qty_ordered);
                $product->save();
            }

        }

    }

    if(count($outofstock) > 0) {
        $product = Product::whereIn('id',$outofstock)->with(['languageProducts'])->get();
        Mail::to("admin@example.com")->send(new \App\Mail\Outofstock_adminMail($product));
    }

    return $outofstock;

}

public function order_processing_mail($id){

    $order = Order::with(['orderItems','orderItems.product'])->where('id',$id)->first();

    $email = $order->email;

    $pdf = PDF::loadView('admin.orders.invoice', compact('order'))->setPaper('a4', 'portrait');


    $response = $pdf->save('storage/files/shares/invoice/invoice-'.$order->invoice_no.'.pdf')->stream('invoice.pdf');

    if($response) {

        // for processing mail
        $order_mail_status = 2;

        Mail::to($email)->send(new \App\Mail\OrderSendMail($order,$order_mail_status,1));
        Mail::to("admin@example.com")->send(new \App\Mail\OrderSendMail($order,$order_mail_status,2));


        // remove later
        // Mail::to($email)->send(new \App\Mail\OrderProcessingMail($order,"Your Order Number #".invoice_no_format($order->id)." is Processing."));
        // //Mail::to("admin@example.com")->send(new \App\Mail\OrderProcessingMail($order,"New Order:#".$order->id));
        // Mail::to("admin@example.com")->send(new \App\Mail\OrderProcessingMail($order,"New Order:#".invoice_no_format($order->id)));
        
    } else {
        dd("false");
    }

    // $pdf = PDF::loadView('admin.orders.invoice', compact('order'))->setPaper('a4', 'portrait');
    // $response = $pdf->save('storage/files/shares/invoice/invoice-'.$order->invoice_no.'.pdf')->stream('invoice.pdf');

    // if($response) {
    //     Mail::to($email)->send(new \App\Mail\OrderSendMail($order,$order_mail_status,1));
    //     Mail::to($this->adminMail)->send(new \App\Mail\OrderSendMail($order,$order_mail_status,2));
    // } else {
    //     dd("false");
    // }

    
    // return redirect()->back();
}


}
