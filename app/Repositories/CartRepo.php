<?php

namespace App\Repositories;

use DB;
use Auth;
use Hash;
use Mail;
// use PDF;
use Session;

use App\Models\Order;
// use App\Coupon;
use App\Models\Product;
// use App\Wishlist;
// use App\Addresses;
// use App\Customers;
// use App\OrderItems;
use App\Models\CustomerTypes;
// use App\CustomersReward;
// use App\RewardPointRules;
// use Illuminate\Http\Request;

// use Newsletter;

use App\Repositories\GeneralSettingRepo;

class CartRepo
{
    protected $generalSettingRepo;
    protected $checkFinishedRule;
    protected $hints;
    protected $limit_item_count;

    public function __construct(GeneralSettingRepo $generalSettingRepo) {
        $this->generalSettingRepo   = $generalSettingRepo;
        $this->checkFinishedRule    = [];
        $this->hints                = [];
        $this->limit_item_count     = 0;
    }

    public function get_api_mycart($changed_shipping_method = 0)
    {

        // return auth()->guard('customer_web')->user()->wallets;
        //$cart = session()->forget('cart');

        $output = [];

        $mycart = [];

        $group_cat_ids = [];

        $check_sales = 0;

        $member_discount = 0;

        $can_reward_points = 0;

        $allow_free_shipping = 0;

        //domestic delivery fee
        $delivery_price = 0;

        $products = [];

        // session()->forget('shipping_type');
        // session()->forget('cart');

        if (session()->get('cart')) {

            // innernal call function / request para error 
            if(isset($_GET['fast'])) {
                return session()->get('cache_cart');
            }

            $mycart = array_values(session()->get('cart'));

            $total = 0;

            foreach ($mycart as $id => $details) {
                $total += $details['price'] * $details['quantity'];
                $currency_code      = $details['currency_code'];
                $currency_symbol    = $details['currency_symbol'];

                $mycart[$id]['item_total'] = $details['price'] * $details['quantity'];

                if(!in_array($details['product_id'], $products)) {
                    $products[] = $details['product_id'];
                    $delivery_price += $details['delivery_price'];
                }
                
                // $mycart[$id]['promo'] = getPromoRules($details['product_id'],$details['product_cats']);

                

                if($details['sales_price'] > 0)
                {
                    $check_sales =  $details['sales_price'];
                }

                //group catids for coupon

                // foreach ($details['product_cats'] as $key => $product_cat) {

                //     if(!in_array($product_cat['categories_id'], $group_cat_ids)) {
                //         $group_cat_ids[] =  $product_cat['categories_id'];
                //     }

                // }

                // check with product_id

                
                
                
            }

            $item_total = $total;


            //check shop product for redeem product
            $check_shop_product = (in_array('shop_product', array_column($mycart, "product_type"))) ? 1 : 0;

            $check_redeem_product = (in_array('redeem_product', array_column($mycart, "product_type"))) ? 1 : 0;


            //Total products id and qty
            $product_ids_qty = array_column($mycart, "quantity", "product_id" );

            // Coupon check and discount
            if(session()->get('session_coupon')) {
                if( session()->get('session_coupon')['status_code']  == "200") {
                    $coupon_rule = session()->get('session_coupon');
                    $coupon_code = session()->get('session_coupon')['code'];
                } else {
                    $coupon_rule = "";
                    $coupon_code = session()->get('session_coupon')['code'];
                }
            } else {
                $coupon_rule = "";
                $coupon_code = "";
            }

            

            if(session()->get('shipping_type')) {
                $shipping_type = session()->get('shipping_type');

            } else {
                $shipping_type = 0;
            }

            //extract promo rule and get grand total
            // $extract_promo = $this->extractPromo($total, $mycart, $product_ids_qty, $coupon_rule, $group_cat_ids, $check_sales);

            // if($extract_promo['coupon_status'] == "404") {
            //     $coupon_code = "";
            // }

            
                if(isset($extract_promo['allow_free_shipping'])) {


                    // type 1 is free shipping
                    // type 0 is for default
                    // dd( $extract_promo['allow_free_shipping']);
                    if( $extract_promo['allow_free_shipping'] != 0 ) {
                        $allow_free_shipping = $extract_promo['allow_free_shipping'];

                        if($changed_shipping_method == 0 ) {
                            $shipping_type = $extract_promo['allow_free_shipping'];
                        }

                    }
                    
                } 
            

            

            

            if(Auth::guard("customer_web")->check()){
                    
                $customer_types_id = Auth::guard("customer_web")->user()->customer_types_id;
                $customerType = CustomerTypes::where('id',$customer_types_id)->first();

                if($customerType->discount_amount){
                        $member_discount = (($customerType->discount_amount/100) * $total );
                        $extract_promo['discount']   += $member_discount;
                        $extract_promo['total']      -= $member_discount;
                        $extract_promo['hints']['member'][]    =  $customerType->name ." get " . $customerType->discount_amount."% discount";
                        // $extract_promo['hints']['array'][]    =  [];
                   
                } 
                
            }

            //check reward points
            // $get_reward_point_rules     = RewardPointRules::first();
            // $can_reward_points          = round(round($extract_promo['total'])/$get_reward_point_rules->spend_amount_per_point);


            //for paypal payment
            $output['items_total']      = $total;
            $sub_total                  = $total;

            // $coupon_total               = $extract_promo['coupon_total'];
            // $promo_total                = $extract_promo['promo_total'];
            // $sub_total                  = $extract_promo['total'];
            // $grand_total                = $extract_promo['total'];
            // $can_reward_points          = $can_reward_points;

            // calculate shipping amount
            $shipping_method = $this->generalSettingRepo->getShippingTypeById($shipping_type,$sub_total,$allow_free_shipping);

            // dd($shipping_method['all']);
            //if(isset($shipping_method)){

            $shipping_type      = $shipping_method['data']['id'];
            $shipping_amount    = $shipping_method['data']['cost'];

            

            $shipping_zone      = $shipping_method['all'];

            // 

            $service_fee_percentage = $this->generalSettingRepo->get_individual_value("service_fee_percentage");

            $service_fee        = number_format((float)(($service_fee_percentage/100) * $total ), 2, '.', '');
            // } else {
            //     $shipping_amount = "Free";

            //     $shipping_zone = $this->generalSettingRepo->checkShippingZone($sub_total, $allow_free_shipping);
            // }

            


            $output['mycart']           = $mycart;
            $output['allow_free_shipping'] = $allow_free_shipping;
            $output['shipping_type']    = $shipping_type;
            $output['shipping_amount']  = $shipping_amount;

            $output['service_fee_percentage'] = $service_fee_percentage;
            $output['service_fee']       =  $service_fee;

            $grand_total        = $sub_total + $shipping_amount + $service_fee + $delivery_price;
            // $output['discount']         = isset($extract_promo['discount']) ? number_format((float)$extract_promo['discount'], 2, '.', '') : 0;
            // $output['sub_total']        = number_format((float)$total, 2, '.', '');
            // $output['total']            = number_format((float)$extract_promo['total'], 2, '.', '');

            // $output['coupon_total']     = number_format((float)$coupon_total, 2, '.', '');
            // $output['promo_total']      = number_format((float)$promo_total, 2, '.', '');
            // $output['member_total']     = number_format((float)$member_discount, 2, '.', '');
            // $output['item_total']       = number_format((float)$item_total, 2, '.', '');

            $output['sub_total']        = number_format((float)$sub_total, 2, '.', '');

            if(auth()->guard('customer_web')->check()) {
                $wallet = auth()->guard('customer_web')->user()->wallets;

                if($wallet > $grand_total ) {
                     $output['wallet'] =   number_format((float)$grand_total, 2, '.', '');
                     $grand_total  -=  $output['wallet'];
                } else {
                     $output['wallet'] = number_format((float)$wallet, 2, '.', ''); 
                     $grand_total  -= $output['wallet'];
                }
               
            }

            $output['total']            = number_format(round($grand_total), 2, '.', '');


            // $output['hints']            = $extract_promo['hints']  ;
            $output['product_ids_qty']  = $product_ids_qty;

            // $output['coupon_status']      =  $extract_promo['coupon_status'];
            // $output['coupon_code']        = $coupon_code;

            // $output['currency_code']            = $currency_code;
            $output['currency_symbol']          = $currency_symbol;

            // $output['check_shop_product']       = $check_shop_product;
            // $output['check_redeem_product']     = $check_redeem_product;

            // $output['can_reward_points']        = $can_reward_points;

            //shipping zone for shipping method
            $output['shipping_zone']            = $shipping_zone;

            $output['delivery_price']          = number_format((float)$delivery_price, 2, '.', '');

            

            session()->put('shipping_type',$shipping_type);


        } 

        // $cache_cart = session()->get('cache_cart');

        // session()->put('cache_cart', $output);
        // $cart = session()->forget('cart');

        return $output;
    }

    // public function get_api_mycart($changed_shipping_method = 0)
    // {
    //     $output = [];

    //     $mycart = [];

    //     $group_cat_ids = [];

    //     $check_sales = 0;

    //     $member_discount = 0;

    //     $can_reward_points = 0;

    //     $allow_free_shipping = 0;

    //     // session()->forget('shipping_type');
    //     // session()->forget('cart');

    //     if (session()->get('cart')) {

    //         // innernal call function / request para error 
    //         if(isset($_GET['fast'])) {
    //             return session()->get('cache_cart');
    //         }

    //         $mycart = array_values(session()->get('cart'));

    //         $total = 0;

    //         foreach ($mycart as $id => $details) {
    //             $total += $details['price'] * $details['quantity'];
    //             $currency_code      = $details['currency_code'];
    //             $currency_symbol    = $details['currency_symbol'];

    //             $mycart[$id]['item_total'] = $details['price'] * $details['quantity'];
    //             $mycart[$id]['promo'] = getPromoRules($details['product_id'],$details['product_cats']);

                

    //             if($details['sales_price'] > 0)
    //             {
    //                 $check_sales =  $details['sales_price'];
    //             }

    //             //group catids for coupon

    //             foreach ($details['product_cats'] as $key => $product_cat) {

    //                 if(!in_array($product_cat['categories_id'], $group_cat_ids)) {
    //                     $group_cat_ids[] =  $product_cat['categories_id'];
    //                 }

    //             }
                
                
                
    //         }

    //         $item_total = $total;


    //         //check shop product for redeem product
    //         $check_shop_product = (in_array('shop_product', array_column($mycart, "product_type"))) ? 1 : 0;

    //         $check_redeem_product = (in_array('redeem_product', array_column($mycart, "product_type"))) ? 1 : 0;


    //         //Total products id and qty
    //         $product_ids_qty = array_column($mycart, "quantity", "product_id" );

    //         // Coupon check and discount
    //         if(session()->get('session_coupon')) {
    //             if( session()->get('session_coupon')['status_code']  == "200") {
    //                 $coupon_rule = session()->get('session_coupon');
    //                 $coupon_code = session()->get('session_coupon')['code'];
    //             } else {
    //                 $coupon_rule = "";
    //                 $coupon_code = session()->get('session_coupon')['code'];
    //             }
    //         } else {
    //             $coupon_rule = "";
    //             $coupon_code = "";
    //         }

            

    //         if(session()->get('shipping_type')) {
    //             $shipping_type = session()->get('shipping_type');

    //         } else {
    //             $shipping_type = 0;
    //         }

    //         //extract promo rule and get grand total
    //         $extract_promo = $this->extractPromo($total, $mycart, $product_ids_qty, $coupon_rule, $group_cat_ids, $check_sales);

    //         if($extract_promo['coupon_status'] == "404") {
    //             $coupon_code = "";
    //         }

            
    //             if(isset($extract_promo['allow_free_shipping'])) {


    //                 // type 1 is free shipping
    //                 // type 0 is for default
    //                 // dd( $extract_promo['allow_free_shipping']);
    //                 if( $extract_promo['allow_free_shipping'] != 0 ) {
    //                     $allow_free_shipping = $extract_promo['allow_free_shipping'];

    //                     if($changed_shipping_method == 0 ) {
    //                         $shipping_type = $extract_promo['allow_free_shipping'];
    //                     }

    //                 }
                    
    //             } 
            

            

            

    //         if(Auth::guard("customer_web")->check()){
                    
    //             $customer_types_id = Auth::guard("customer_web")->user()->customer_types_id;
    //             $customerType = CustomerTypes::where('id',$customer_types_id)->first();

    //             if($customerType->discount_amount){
    //                     $member_discount = (($customerType->discount_amount/100) * $total );
    //                     $extract_promo['discount']   += $member_discount;
    //                     $extract_promo['total']      -= $member_discount;
    //                     $extract_promo['hints']['member'][]    =  $customerType->name ." get " . $customerType->discount_amount."% discount";
    //                     // $extract_promo['hints']['array'][]    =  [];
                   
    //             } 
                
    //         }

    //         //check reward points
    //         $get_reward_point_rules     = RewardPointRules::first();
    //         $can_reward_points          = round(round($extract_promo['total'])/$get_reward_point_rules->spend_amount_per_point);


    //         //for paypal payment
    //         $output['items_total']      = $total;

    //         $coupon_total               = $extract_promo['coupon_total'];
    //         $promo_total                = $extract_promo['promo_total'];
    //         $sub_total                  = $extract_promo['total'];
    //         $grand_total                = $extract_promo['total'];
    //         $can_reward_points          = $can_reward_points;

    //         // calculate shipping amount
    //         $shipping_method = $this->generalSettingRepo->getShippingTypeById($shipping_type,$sub_total,$allow_free_shipping);

    //         // dd($shipping_method['all']);
    //         //if(isset($shipping_method)){

    //         $shipping_type      = $shipping_method['data']['id'];
    //         $shipping_amount    = $shipping_method['data']['cost'];

    //         $grand_total        = $sub_total + $shipping_amount;

    //         $shipping_zone      = $shipping_method['all'];
    //         // } else {
    //         //     $shipping_amount = "Free";

    //         //     $shipping_zone = $this->generalSettingRepo->checkShippingZone($sub_total, $allow_free_shipping);
    //         // }


    //         $output['mycart']           = $extract_promo['mycart'];
    //         $output['allow_free_shipping'] = $allow_free_shipping;
    //         $output['shipping_type']    = $shipping_type;
    //         $output['shipping_amount']  = $shipping_amount;
    //         $output['discount']         = isset($extract_promo['discount']) ? number_format((float)$extract_promo['discount'], 2, '.', '') : 0;
    //         // $output['sub_total']        = number_format((float)$total, 2, '.', '');
    //         // $output['total']            = number_format((float)$extract_promo['total'], 2, '.', '');

    //         $output['coupon_total']     = number_format((float)$coupon_total, 2, '.', '');
    //         $output['promo_total']      = number_format((float)$promo_total, 2, '.', '');
    //         $output['member_total']     = number_format((float)$member_discount, 2, '.', '');
    //         $output['item_total']       = number_format((float)$item_total, 2, '.', '');

    //         $output['sub_total']        = number_format((float)$sub_total, 2, '.', '');
    //         $output['total']            = number_format((float)$grand_total, 2, '.', '');


    //         $output['hints']            = $extract_promo['hints']  ;
    //         $output['product_ids_qty']  = $product_ids_qty;

    //         $output['coupon_status']      =  $extract_promo['coupon_status'];
    //         $output['coupon_code']        = $coupon_code;

    //         $output['currency_code']            = $currency_code;
    //         $output['currency_symbol']          = $currency_symbol;

    //         $output['check_shop_product']       = $check_shop_product;
    //         $output['check_redeem_product']     = $check_redeem_product;

    //         $output['can_reward_points']        = $can_reward_points;

    //         //shipping zone for shipping method
    //         $output['shipping_zone']            = $shipping_zone;

    //         session()->put('shipping_type',$shipping_type);


    //     } 

    //     // $cache_cart = session()->get('cache_cart');

    //     // session()->put('cache_cart', $output);

    //     return $output;
    // }

    public function get_api_mycart_add_item_qty( $id, $quantity, $data = [])
    {
        

        // return $id .' - '. $quantity;
        // item check coupon_code
        // product table sku and size etc that not include product name
        
        $cart = session()->get('cart');

        
        // if not current currency price , english price add 
        // if not used and filtering enable  regular_price and sales_price at dashboard in the future , it can remove 
   
        // if(isset($product['productsCurrency'][getDefaultCurrenyArrayPos()])) {
        //     $productsCurrency = $product['productsCurrency'][getDefaultCurrenyArrayPos()];
        // } else {
        //     $productsCurrency = $product['productsCurrency'][0];
        // }


        // database project
        $product = Product::findOrFail($id);

        if(!$product) {
            abort(404);
        } 

        // $product_id             = $product['oid'];
        // $sku                    = $product['oid'];
        // $product_name           = $product['title'];
        // $product_name_ch        = $product['original_title'];
        // $product_price          = $product['price'];
        // $sales_price            = isset($product['price']) ? $product['price'] : 0;
        // $products_categories    = $product['productsCategories']->toArray();


          // form        : {
        //     id          : "",
        //     vendor_id   : "",
        //     vendor_name : "",
        //     product_id : "",
        //     quantity    : 1,
        //     ConfiguredItem : {
        //         Configurators : [
        //             {Pid : 0, Vid : 0},
        //             {Pid : 0, Vid : 0}
        //         ]
        //     },
        // },



        $id                     = $data['ConfiguredItem']['Id'];
        $product_id             = $product['oid'];
        $sku                    = $product['oid'];
        $product_name           = $product['title'];
        $product_name_ch        = $product['original_title'];
        $delivery_price         = $data['DeliveryPrice'];
        $product_price          = $data['Price'];
        $taobao_item_url        = $product['taobao_item_url'];
        $provider_type          = $product['provider_type'];
        $sales_price            = isset($data['price']) ? $data['price'] : 0;

        $vendor_id              = $data['vendor_id'];
        $vendor_name            = $data['vendor_name'];
        $ConfiguredItem         = $data['ConfiguredItem'];
        $PlusMessage            = $data['PlusMessage'];
        $weight                 = $data['Weight'];



        // $sku                    = $product->sku;

        // hide for shop_product
        $exchange_reward_points = 0; 
        // $exchange_reward_points = isset($product->exchange_reward_points) ? $product->exchange_reward_points : 0;

        // id  1
        // shipping_weight "KG"
        // shipping_dimensions "CM"
        // default_currency    "3"
        // default_language    "1"
        // coupon_module   1
        // promotion_module    1
        // reward_module   null
        // decimal_place   null
        // currency    
        // id  3
        // name    "Myanmar"
        // code    "MMK"
        // conversation_rate   "978"
        // symbol  "MMK"
        // status  "active"
        // created_at  "2020-09-03 21:35:00"
        // updated_at  "2020-09-03 21:35:39"

        // $default_setting = getDefaultSetting();
        // return $default_setting['currency']['code'];

        // die();

        $default_setting = $this->generalSettingRepo->getDefaultSettings()[0];
        // return $default_setting = getDefaultSetting();

        // did();

        $currency_code = $default_setting['currency']['code'];
        $currency_symbol = $default_setting['currency']['symbol'];
 
        // if cart is empty then this the first product
        if(!$cart) {
            $cart = [
                $id => [
                    "id"                        => $id,
                    "product_id"                => $product_id,
                    "name"                      => $product_name,
                    "quantity"                  => $quantity,
                    "delivery_price"            => $delivery_price,
                    "price"                     => ($sales_price != "0") ? $sales_price : $product_price,
                    "sales_price"               => $sales_price,
                    "photo"                     => $product->picture->medium,
                    "currency_code"             => $currency_code,
                    "currency_symbol"           => $currency_symbol,
                    // "product_cats"              => $products_categories,
                    "sku"                       => $sku,
                    "taobao_item_url"           => $taobao_item_url,
                    "exchange_reward_points"    => $exchange_reward_points,
                    "product_type"              => "shop_product",
                    "provider_type"             =>  $provider_type,
                    "wishlist"                  => 0,

                    "vendor_id"                 => $vendor_id,
                    "vendor_name"               => $vendor_name,
                    "ConfiguredItem"            => $ConfiguredItem,
                    "PlusMessage"               => $PlusMessage,
                    "weight"                    => $weight,
                    
                ]
            ];
 
            session()->put('cart', $cart);
            return $this->get_api_mycart();
        }
 
        // if cart not empty then check if this product exist then increment quantity
        if(isset($cart[$id])) {

            // if($request->has('qty'))
            // {
            $cart[$id]['quantity'] = $cart[$id]['quantity'] + $quantity;
            // } else {
            //     $cart[$id]['quantity']++;
            // }

            session()->put('cart', $cart);
            return $this->get_api_mycart();
        }
 
        // if item not exist in cart then add to cart with quantity = 1
        $cart[$id] = [
            "id"                        => $id,
            "product_id"                => $product_id,
            "name"                      => $product_name,
            "quantity"                  => $quantity,
            "delivery_price"            => $delivery_price,
            "price"                     => ($sales_price != "0") ? $sales_price : $product_price,
            "sales_price"               => $sales_price,
            "photo"                     => $product->picture->medium,
            "currency_code"             => $currency_code,
            "currency_symbol"           => $currency_symbol,
            // "product_cats"              => $products_categories,
            "sku"                       => $sku,
            "taobao_item_url"           => $taobao_item_url,
            "exchange_reward_points"    => $exchange_reward_points,
            "product_type"              => "shop_product",
            "provider_type"             => $provider_type,
            "wishlist"                  => 0,

            "vendor_id"                 => $vendor_id,
            "vendor_name"               => $vendor_name,
            "ConfiguredItem"            => $ConfiguredItem,
            "PlusMessage"               => $PlusMessage,
            "weight"                    => $weight,
        ];
 
        session()->put('cart', $cart);
        return $this->get_api_mycart();
    }

    public function addToRedeemCart($id, $quantity = 1)
    {
        $id = "000".$id;

        $can_use_points = $this->canUsePoints();        

        $product = Product::with(['languageProducts', 'languageProducts.product','productsCurrency', 'productsCategories' ])->findOrFail($id);
        // dd($product);
        if(!$product) {
            $data['status']  = "404";
            $data['message'] = "Invalid Product";
            $data['data']    = [];
            return $data;
        } 

        $cart = session()->get('cart');

        $exchange_reward_points = $product['languageProducts'][getLanArrayPos()]['product']['exchange_reward_points'];

        //return $can_use_points .' - '. $exchange_reward_points;
        if($can_use_points < $exchange_reward_points ) {
            $data['status']  = "404";
            $data['message'] = "Insufficient Point !!!";
            $data['data']    = [];
            return $data;
        }

        $product_id             = $product['languageProducts'][getLanArrayPos()]['product_id'];
        $product_name           = $product['languageProducts'][getLanArrayPos()]['name'];
        // $product_price          = $product['productsCurrency'][getLanArrayPos()]['regular_price'];
        // $sales_price            = $product['productsCurrency'][getLanArrayPos()]['sales_price'];
        $products_categories    = $product['productsCategories']->toArray();
        $sku                    = $product['languageProducts'][getLanArrayPos()]['product']['sku'];

        // $coupon_code
        $default_setting = getDefaultSetting();

        $currency_code = $default_setting['currency']['code'];
        $currency_symbol = $default_setting['currency']['symbol'];
        // $currency_code = "HKD";
 
        // if cart is empty then this the first product
        if(!$cart) {
            $cart = [
                $id => [
                    "id"                        => $id,
                    "product_id"                => $product_id,
                    "name"                      => $product_name,
                    "quantity"                  => 1,
                    "price"                     => 0,
                    "sales_price"               => 0,
                    "photo"                     => $product->featured_image,
                    "currency_code"             => $currency_code,
                    "currency_symbol"           => $currency_symbol,
                    // "product_cats"              => $products_categories,
                    "sku"                       => $sku,
                    "exchange_reward_points"    => $exchange_reward_points,
                    "product_type"              => "redeem_product",
                    "wishlist"                  => 0
                ]
            ];
 
            session()->put('cart', $cart);

            $data['status']  = "200";
            $data['message'] = "Successfully add to cart !!";
            $data['data']    = $this->get_api_mycart();
            return $data;

        }
 
        // if cart not empty then check if this product exist then increment quantity
        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
            session()->put('cart', $cart);

            $data['status']  = "200";
            $data['message'] = "Successfully add to cart !!";
            $data['data']    = $this->get_api_mycart();
            return $data;

        }
 
        // if item not exist in cart then add to cart with quantity = 1
        $cart[$id] = [
            "id"                        => $id,
            "product_id"                => $product_id,
            "name"                      => $product_name,
            "quantity"                  => 1,
            "price"                     => 0,
            "sales_price"               => 0,
            "photo"                     => $product->featured_image,
            "currency_code"             => $currency_code,
            "currency_symbol"           => $currency_symbol,
            // "product_cats"              => $products_categories,
            "sku"                       => $sku,
            "exchange_reward_points"    => $exchange_reward_points,
            "product_type"              => "redeem_product",
            "wishlist"                  => 0
        ];
 
        

        session()->put('cart', $cart);

        $data['status']  = "200";
        $data['message'] = "Successfully add to cart !!";
        $data['data']    = $this->get_api_mycart();
        return $data;

    }


    //extract individaul items promo
    public function extractPromo($total, $mycart, $product_ids_qty, $coupon_rule, $group_cat_ids, $check_sales) {

        $temp['total']              = $total;
        $temp['coupon_total']       = 0;
        $temp['promo_total']        = 0;
        $temp['discount']           = 0;
        $temp['mycart']             = $mycart;
        $temp['coupon_status']      = '404';

        $this->checkFinishedRule    = [];
        $this->hints                = [];

        if($coupon_rule != "") {

            if($coupon_rule['status_code'] == "200") {
                $discount_type = $coupon_rule['logic']['discount_type'];

                    // percent
                    // fixed_cart
                    // fixed_product

                    // percent and fixed_cart coupon rule check
                if(in_array($discount_type, ["percent", "fixed_cart"]) )
                {
                    $promo_group_result = $this->checkCouponRule($coupon_rule, $total, $product_ids_qty, $group_cat_ids, $check_sales);
                    $temp['coupon_total'] += $promo_group_result['discount'];
                    $temp['discount']   += $promo_group_result['discount'];
                    $temp['total']      -= $promo_group_result['discount'];
                    $temp['coupon_status']      = $promo_group_result['coupon_status'];
                    $temp['allow_free_shipping'] = $promo_group_result['allow_free_shipping'];
                    if(isset($promo_group_result['hints'])){
                        $this->hints['coupon'][] = $promo_group_result['hints'];
                    }
                }

                

                    // fixed product discount

            }
                //logic here
        }

            //return $coupon_rule;

        foreach ($mycart as $key => $value) {
            if(isset($value['promo'])) {

                // dd($value);
                
                // check current promo
                //$output[] = $value['promo'];

                // //if calculate with item total
                // $result = $this->calculatePromo($value['item_total'], $value['promo'], $value['quantity'], $product_ids_qty);

                $result = $this->calculatePromo($total, $value['quantity'], $value['item_total'], $value['promo'], $product_ids_qty);


                //$data[] = $result['discount'];
                $temp['promo_total'] += $result['discount'];
                $temp['discount']   += $result['discount'];
                $temp['total']      -= $result['discount'];

                $mycart[$key]['discount_percent']   = $result['discount_percent'];
                $mycart[$key]['discount_amount']    = $result['discount_amount'];


                if(isset($result['hints'])){
                    $this->hints['promo'][$key] = $result['hints'];
                }

                // checkout discount amount

                if($coupon_rule != "") {

                    if($coupon_rule['status_code'] == "200") {
                        if($discount_type == 'fixed_product') 
                        {
                            $promo_group_result = $this->checkFixedProductCouponRule($coupon_rule, $total, $product_ids_qty, $value);
                            //print_r($promo_group_result);
                            $temp['coupon_total'] += $promo_group_result['discount'];
                            $temp['discount']   += $promo_group_result['discount'];
                            $temp['total']      -= $promo_group_result['discount'];

                            if($promo_group_result['coupon_status'] == "200")
                            {
                                $temp['coupon_status']       = $promo_group_result['coupon_status'];
                            } 
                            
                            $temp['allow_free_shipping'] = $promo_group_result['allow_free_shipping'];

                            if(isset($promo_group_result['hints'])){
                                $this->hints['coupon'][0] = $promo_group_result['hints'];
                            }
                        }
                    }
                }

            } 
        }


        // final check
        if($coupon_rule != "") {

            if($coupon_rule['status_code'] == "200") {
                if($discount_type == 'fixed_product') 
                {
                    if($temp['coupon_status'] == "404")
                    {
                        if(!isset($this->hints['coupon'][0])){
                            $this->hints['coupon'][]       = "This Coupon can't use";
                        }
                        
                    }
                }
            }
        }

            //update data
        $temp['mycart']             = $mycart;
        $temp['hints']              = $this->hints; 

        return $temp;

    }

        //  items discount  promotion rule and messages
        //  not include bulk type
    public function calculatePromo($total, $quantity, $item_total, $promo,  $product_ids_qty) {

        $temp['discount']           = 0;
        $temp['discount_percent']   = 0;
        $temp['discount_amount']    = 0;
        $temp['hints']              = [];


        // $quantity = $sessionvalue['quantity'];

        $check_buy_get  = false;
        $check_bulk     = false;
        
        //print_r($sessionvalue['quantity']);
            // dd($promo);
        //dd($sessionvalue);

        foreach ($promo as $key => $dbpromo_rule) {
            // echo $key;
            // print_r($quantity);

            // checkout promo method calculate or not
            

            $promo_rule = checkPromoMethod($dbpromo_rule->promotion_method, $dbpromo_rule, 'info');

            if(!in_array($dbpromo_rule->id, $this->checkFinishedRule)) {


                $this->checkFinishedRule[]    = $dbpromo_rule->id;

                    //  stdClass convert
                // $promo_rule = json_decode(json_encode($promo_rule, true),true);

                switch ($promo_rule->type) {
                    case "fix_amt":
                            // name , qty_amount = 1000, discount = 100 , type = fix_amt
                    if($total >= $promo_rule->qty_amount ) {
                        $temp['discount']           += $promo_rule->discount;
                        $temp['discount_amount']    += $promo_rule->discount;
                        $temp['hints'][]             = $promo_rule->name ;

                    }
                    break;

                    case "percentage_amt":
                            // name , qty_amount = 1000, discount = 8% , type = percentage_amt
                    if($total >= $promo_rule->qty_amount ) {
                                // $temp['discount'] = $promo_rule->discount'];
                        $temp['discount']           += (($promo_rule->raw_discount/100) * $total );
                        $temp['discount_percent']   += $promo_rule->raw_discount;

                        $temp['hints'][]            = $promo_rule->name ;
                    }
                    break;

                    case "get_items":
                            // name , qty_amount = 1000, discount = product names , type = get_items

                    if($total >= $promo_rule->qty_amount ) {
                                //$temp['hints'] = "FOC : ". $promo_rule->discount'];

                        $temp['hints'][]        = $promo_rule->name .  " | ". $promo_rule->discount;
                    }

                    break;

                    default:

                }

            }


            //outer world


            if($promo_rule->type == "bulk_price") {

                    // name , qty_amount = product names, discount = 2-2=8% 3-3=16% 4-0=20%, type = bulk_price        
                    // cart items find in buy products group 

                    // calculate on item total 
                // one item one message
                if($check_bulk == false) {

                    if($quantity > 0) {
                        // 'quantity_ranges_from' => $value->quantity_ranges_from, 'quantity_ranges_to' => $value->quantity_ranges_to, 'percentage_amt'
                        // rule from promotions_quantity table

                        foreach ($promo_rule->promotions_quantity as $key => $value) {
                                        // raw_qty_amount = buy product ids
                                        // check infinity 0 value


                            if($value['quantity_ranges_from'] > 0) {

                                if($value['quantity_ranges_to'] != "0"){
                                    if($quantity >= $value['quantity_ranges_from'] && $quantity <= $value['quantity_ranges_to'] ) {

                                        $temp['discount']           += (($value['percentage_amt']/100) * $item_total );
                                        $temp['discount_percent']   += $value['percentage_amt'];

                                                            //$value['percentage_amt'] show percentage
                                        $temp['hints'][]   = $promo_rule->name . "  ". $value['percentage_amt'] ." % | ". (($value['quantity_ranges_from'] != $value['quantity_ranges_to']) ? $value['quantity_ranges_from'] ." - ".$value['quantity_ranges_to'] :  $value['quantity_ranges_from']) .' qty. ';

                                        $check_bulk = true;

                                    } 


                                } else {

                                    if($quantity >= $value['quantity_ranges_from']){
                                        $temp['discount']           += (($value['percentage_amt']/100) * $item_total );
                                        $temp['discount_percent']   += $value['percentage_amt'];

                                        $temp['hints'][] = $promo_rule->name .' '. $value['percentage_amt'] ." %   | ".  $value['quantity_ranges_from'] ." and above ";

                                        $check_bulk = true;

                                    }

                                }

                            }



                        }

                         // $temp['hints'][] = $hints;


                    }

                }


             } elseif ($promo_rule->type = "buy_get" ) {

                // dd($dbpromo_rule->get_qty);
                // name , qty_amount = (11-1), discount = product names , type = buy_get

                //need to fill up one
                // one item one message
                if( isset($promo_rule->raw_buy_qty )) {
                    if($check_buy_get == false) {
                        if($quantity>=$promo_rule->raw_buy_qty) {
                            // get qty
                            // buy qty
                            $temp['hints'][]        = $promo_rule->name  . " | ". $promo_rule->discount .' x ' . intval(($quantity/$promo_rule->raw_buy_qty)* $promo_rule->raw_get_qty);

                            $check_buy_get = true;
                            // exit from get_buy_ids 
                            //break;
                            // 
                            // return $temp;
                            //$temp['hints'] = "Gift ".$promo_rule['discount'] ." ". $promo_rule['raw_get_qty'] . " item.";
                        }
                    }
                }
                

            }

          


        }

        

        if(count($temp['hints']) == 0) {
            unset($temp['hints']);
        } 


        return $temp;
    }

        // check exactly later
    public function checkCouponRule($coupon_rule, $total, $product_ids_qty, $group_cat_ids, $check_sales)
    {
        $temp['discount']           = 0;
        $temp['discount_percent']   = 0;
        $temp['discount_amount']    = 0;
        $temp['allow_free_shipping']= 0;
        $temp['coupon_status']          = '200';

        $product_ids = array_keys($product_ids_qty);
        $product_qty = array_values($product_ids_qty);
        $product_total_qty = array_sum($product_qty);

        // dd($product_total_qty);
        // print_r($product_qty);
        // dd($coupon_rule);

        // percent, fixed_cart, fixed_product, minimum_spend, maximum_spend
        //limit_coupon, limit_item, allow_free_shipping
        //limit_user


        $discount_type = $coupon_rule['logic']['discount_type'];
        $coupon_rule = $coupon_rule['logic'];

        $check_product_ids = false;
        $exclude_product_ids = false;

        // dd($check_sales);
        if($coupon_rule['exclude_sales_item'] == 1) {
            if($check_sales > 0){
                $temp['coupon_status']          = '404';
                $temp['hints']                  = "This Coupon can't use for sale items.";
                return $temp;
            }
        }
        //product_ids 
        if($coupon_rule['product_ids']) {
            // return dd(count($coupon_rule['product_ids']));
            if(count($coupon_rule['product_ids']) == 0){
                $check_product_ids = true;

            } else {
                foreach ($coupon_rule['product_ids'] as $key => $value) {
                    if(in_array($value, $product_ids)){

                        $check_product_ids = true;
                    }
                }
            }
            
        } else {
            $check_product_ids = true;
        }

        if($check_product_ids == false) {
            $temp['coupon_status']          = '404';
            $temp['hints']                  = "This Coupon can't use for these items.";
            return $temp;
        }


        //---------------------------------------------------------------------------

        //exclude_product_ids 
        if($coupon_rule['exclude_product_ids']) {
            if(count($coupon_rule['exclude_product_ids']) == 0){
                $exclude_product_ids = true;

            } else {
                foreach ($coupon_rule['exclude_product_ids'] as $key => $value) {
                    if(in_array($value, $group_cat_ids)){

                        $exclude_product_ids = true;
                    }
                }
            }
            
        } else {
            $exclude_product_ids = false;
        }

        if($exclude_product_ids == true) {
            $temp['coupon_status']          = '404';
            $temp['hints']                  = "This Coupon can't use for these items.";
            return $temp;
        }

        //---------------------------------------------------------------------------

        $check_product_categories = false;
        $exclude_product_categories = false;

        //product_categories  
        if($coupon_rule['product_categories']) {
            // return dd(count($coupon_rule['product_ids']));
            if(count($coupon_rule['product_categories']) == 0){
                $check_product_categories = true;

            } else {
                foreach ($coupon_rule['product_categories'] as $key => $value) {
                    if(in_array($value, $group_cat_ids)){

                        $check_product_categories = true;
                    }
                }
            }
            
        } else {
            $check_product_categories = true;
        }

        if($check_product_categories == false) {
            $temp['coupon_status']          = '404';
            $temp['hints']                  = "This Coupon can't use for these items.";
            return $temp;
        }


        //---------------------------------------------------------------------------

        //exclude_product_categories 
        if($coupon_rule['exclude_product_categories']) {
            if(count($coupon_rule['exclude_product_categories']) == 0){
                $exclude_product_categories = true;

            } else {
                foreach ($coupon_rule['exclude_product_categories'] as $key => $value) {
                    if(in_array($value, $group_cat_ids)){

                        $exclude_product_categories = true;
                    }
                }
            }
            
        } else {
            $exclude_product_categories = false;
        }

        if($exclude_product_categories == true) {
            $temp['coupon_status']          = '404';
            $temp['hints']                  = "This Coupon can't use for these items.";
            return $temp;
        }

        //---------------------------------------------------------------------------


        // minimum usage
        if($coupon_rule['minimum_spend'])
        {
            // dd($coupon_rule['used_coupon']);
            if($coupon_rule['minimum_spend'] >= $total)
            {
                $temp['coupon_status']          = '404';
                $temp['hints']                  = "This Coupon must have minimum usage ".$coupon_rule['minimum_spend']." amount";
                return $temp;
            }
        }     

        // limit coupon 
        if($coupon_rule['limit_coupon'])
        {
            // dd($coupon_rule['used_coupon']);
            if($coupon_rule['limit_coupon'] <= $coupon_rule['used_coupon'])
            {
                $temp['coupon_status']          = '404';
                $temp['hints']                  = "This Coupon reached maximum usage.";
                return $temp;
            }
        }

        // quantity number check
        if($coupon_rule['limit_item'])
        {
            // dd($coupon_rule['used_coupon']);
            if($coupon_rule['limit_item'] > $product_total_qty)
            {
                $temp['coupon_status']          = '404';
                $temp['hints']                  =  $coupon_rule['limit_item']." item limited.";
                return $temp;
            } 
            // base on cart total

        }

       

        switch ($discount_type) {
            case "percent":
                        // name , qty_amount = 1000, discount = 100 , type = fix_amt
            $temp['discount']               = (($coupon_rule['coupon_amount']/100) * $total );
            $temp['discount_percent']       += $coupon_rule['coupon_amount'];

            $temp['hints']                  =  $coupon_rule['coupon_description'] . " | ". $coupon_rule['coupon_amount'] . " % ";

            break;

            case "fixed_cart":

            $temp['discount']           =   $coupon_rule['coupon_amount'];
            $temp['discount_amount']    +=  $coupon_rule['coupon_amount'];

            $temp['hints']              =    $coupon_rule['coupon_description'] . " | ". $coupon_rule['coupon_amount'] ." amount " ;

            break;


        }

        $temp['allow_free_shipping']= $coupon_rule['allow_free_shipping'];

        return $temp;
    }

    // per item check so error message only show this coupon can't use
    public function checkFixedProductCouponRule($coupon_rule, $total, $product_ids_qty, $item)
    {

        //product_ids 
        //exclude_product_ids

        //minimum_speed
        //maximum_speed

        //limit_coupon
        //limit_item
        
        //allow_free_shipping

        //limit_user
        //exclude_sales_item
        //individual_use_only

        

        $product_ids = array_keys($product_ids_qty);

        $discount_type = $coupon_rule['logic']['discount_type'];
        $coupon_rule = $coupon_rule['logic'];

        
        $item_categories_ids = array_column($item['product_cats'], 'categories_id');

        // $check_product_ids = false;
        // //product_ids 
        // if(isset($coupon_rule['product_ids'])) {
        //     if(count($coupon_rule['product_ids']) == 0){
        //         $check_product_ids = true;
        //     }

        //     foreach ($coupon_rule['product_ids'] as $key => $value) {
        //         if(in_array($value, $product_ids)){
        //             $check_product_ids = true;
        //         }
        //     }
            
        // } 

        //exclude_product_ids

        // dd($item['sales_price']);

        $temp['discount']           = 0;
        $temp['discount_percent']   = 0;
        $temp['discount_amount']    = 0;
        $temp['allow_free_shipping']= 0;
        $temp['coupon_status']      = '200';
        

        if($coupon_rule['exclude_sales_item'] == 1) {
            if($item['sales_price'] > 0){
                $temp['coupon_status']          = '404';
                // dd($item['sales_price']);
                return $temp;
            }
        }


        $check_product_ids = false;
        $exclude_product_ids = false;
        //product_ids 
        
        if($coupon_rule['product_ids']) {
            if(count($coupon_rule['product_ids']) == 0){
                $check_product_ids = true;

            } else {
                    if(in_array($item['product_id'], $coupon_rule['product_ids'])){

                        $check_product_ids = true;
                    }
            }
            
        } else {

            $check_product_ids = true;
        }

        if($check_product_ids == false) {
            $temp['coupon_status']          = '404';
            // $temp['hints']                  = "This Coupon can't use for these items.";
            return $temp;
        }
        //---------------------------------------------------------------------------

        //exclude_product_ids 
        if($coupon_rule['exclude_product_ids']) {
            if(count($coupon_rule['exclude_product_ids']) == 0){
                $exclude_product_ids = true;

            } else {
                if(in_array($item['product_id'], $coupon_rule['product_ids'])){

                        $exclude_product_ids = true;
                   
                }
            }
            
        } else {
            $exclude_product_ids = false;
        }

        if($exclude_product_ids == true) {
            $temp['coupon_status']          = '404';
            // $temp['hints']                  = "This Coupon can't use for these items.";
            return $temp;
        }



        //---------------------------------------------------------------------------

        $check_product_categories = false;
        $exclude_product_categories = false;

        //product_categories  
        if($coupon_rule['product_categories']) {
            // return dd(count($coupon_rule['product_ids']));
            if(count($coupon_rule['product_categories']) == 0){
                $check_product_categories = true;

            } else {
                foreach ($coupon_rule['product_categories'] as $key => $value) {
                    if(in_array($value, $item_categories_ids)){

                        $check_product_categories = true;
                    }
                }
            }
            
        } else {
            $check_product_categories = true;
        }

        if($check_product_categories == false) {
            $temp['coupon_status']          = '404';
            return $temp;
        }


        //---------------------------------------------------------------------------

        //exclude_product_categories 
        if($coupon_rule['exclude_product_categories']) {
            if(count($coupon_rule['exclude_product_categories']) == 0){
                $exclude_product_categories = true;

            } else {
                foreach ($coupon_rule['exclude_product_categories'] as $key => $value) {
                    if(in_array($value, $item_categories_ids)){

                        $exclude_product_categories = true;
                    }
                }
            }
            
        } else {
            $exclude_product_categories = false;
        }

        if($exclude_product_categories == true) {
            $temp['coupon_status']          = '404';
            return $temp;
        }

        //---------------------------------------------------------------------------

        // minimum usage
        if($coupon_rule['minimum_spend'])
        {
            // dd($coupon_rule['used_coupon']);
            if($coupon_rule['minimum_spend'] >= $item['item_total'])
            {
                $temp['coupon_status']          = '404';
                // $temp['hints']                  = "This Coupon must have minimum usage ".$coupon_rule['minimum_spend']." amount";
                return $temp;
            }
        }     


        //limit_coupon
         // maximum usage
        if($coupon_rule['limit_coupon'])
        {
            // dd($coupon_rule['used_coupon']);
            if($coupon_rule['limit_coupon'] <= $coupon_rule['used_coupon'])
            {
                $temp['coupon_status']      = '404';
                // $temp['hints']              = "This Coupon reached maximum usage.";
                return $temp;
            }
        }

         //limit_item
        // quantity number check
        // maximum number of individual items
        // limit item related with product items
        if($coupon_rule['limit_item'])
        {

                // 0 < 3
                if( $this->limit_item_count < $coupon_rule['limit_item']) {
                    // same dar ko shar tar
                    if( $item['quantity'] <= $coupon_rule['limit_item'] )
                    {

                        //check remain
                        if(($this->limit_item_count+$item['quantity']) > $coupon_rule['limit_item']){
                            $quantity                   = $coupon_rule['limit_item'] - $this->limit_item_count;
                            $this->limit_item_count     =   $item['quantity'];
                        } else {
                            $quantity                    =   $item['quantity'];
                            $this->limit_item_count      =   $item['quantity'];
                        }
                        
                        $temp['discount']           =   $coupon_rule['coupon_amount'] *  $quantity ;
                        $temp['discount_amount']    +=  $coupon_rule['coupon_amount'] *  $quantity ;

                        $temp['hints']              =    $coupon_rule['coupon_description'] . " | ". $coupon_rule['coupon_amount'] ." discount per item and maximum ".  $coupon_rule['limit_item']  ." quantity limited." ;
                        $temp['allow_free_shipping']= $coupon_rule['allow_free_shipping'];

                        return $temp;
                    } 


                    if( $item['quantity'] > $coupon_rule['limit_item'] )
                    {

                        if(($this->limit_item_count+$item['quantity']) > $coupon_rule['limit_item']){
                            $quantity                   = $coupon_rule['limit_item'] - $this->limit_item_count;
                            $this->limit_item_count     =   $item['quantity'];
                        } else {
                            $quantity                    =   $item['quantity'];
                            $this->limit_item_count      =   $item['quantity'];
                        }

                        $temp['discount']           =   $coupon_rule['coupon_amount'] * $quantity ;
                        $temp['discount_amount']    +=  $coupon_rule['coupon_amount'] * $quantity ;

                        $temp['hints']              =    $coupon_rule['coupon_description'] . " | ". $coupon_rule['coupon_amount'] ." discount per item and maximum ".  $coupon_rule['limit_item'] ." quantity limited." ;
                        $temp['allow_free_shipping']= $coupon_rule['allow_free_shipping'];

                        return $temp;
                    } 
                } else {
                    return $temp;
                }

        }

        // fixed_product

        // dd();
        // all product data and search
        // should not be calculate on group cart
        

        // if($check) {
            $temp['discount']           =   $coupon_rule['coupon_amount'] * $item['quantity'];
            $temp['discount_amount']    +=  $coupon_rule['coupon_amount'] * $item['quantity'];

            $temp['hints']              =    $coupon_rule['coupon_description'] . " | ". $coupon_rule['coupon_amount'] ." amount " ;
        // }

        $temp['allow_free_shipping']= $coupon_rule['allow_free_shipping'];


        return $temp;
    }


    public function checkCoupon($coupon_code)
    {
            //session()->forget('session_coupon');

        $message['message']     = array();
        $message['status_code'] = "404";

        $coupon = Coupon::where('coupon_code',$coupon_code)->first();
        if($coupon) {

            if($coupon->limit_user) {

                
                if (Auth::guard("customer_web")->user()){

                    if($coupon->used_user) {

                        $customer_id = Auth::guard("customer_web")->user()->id;

                        $user_count = array_count_values($coupon->used_user);

                        if (array_key_exists($customer_id,$user_count))
                        {
                            if($coupon->limit_user <= $user_count[$customer_id])
                            {
                                $message['message'] = "This coupon reached maximum usage.";

                                $session_coupon = [
                                    "code"  => "",
                                    "logic" => "",
                                    "status_code" => "404"
                                ];

                                session()->put('session_coupon', $session_coupon);

                                return $message;
                            }
                        }


                        
                    }

                }

            }

            if(isset($coupon->start_date)) {
                $date1=date_create($coupon->start_date);
                $date2=date_create(date('Y-m-d'));
                
                $diff=date_diff($date1,$date2);
                $sign    = $diff->format("%R");
                $diffday = $diff->format("%a");

                
                if($sign == "-" && $diffday != "0") {

                    $message['message'] = "This coupon can use ". date('d M Y',strtotime($coupon->start_date));

                    $session_coupon = [
                        "code"  => "",
                        "logic" => "",
                        "status_code" => "404"
                    ];

                    session()->put('session_coupon', $session_coupon);

                    return $message;
                }
            }

            if(isset($coupon->date)) {
                
                $date1=date_create($coupon->date);
                $date2=date_create(date('Y-m-d'));
                $diff=date_diff($date1,$date2);
                $sign       = $diff->format("%R%");
                $diffday    = $diff->format("%a");

                // echo $sign."-".$diffday."=".($diffday > 1);
                // die();
                
                if($sign == "+" && $diffday != "0") {

                    $message['message'] = "This coupon is expire now";

                    $session_coupon = [
                        "code"  => "",
                        "logic" => "",
                        "status_code" => "404"
                    ];

                    session()->put('session_coupon', $session_coupon);

                    return $message;
                }

            }
            

            $message['status_code'] = "200";
            $message['message'] = "";

            $session_coupon = [
                "code"  => $coupon_code,
                "logic" => $coupon,
                "status_code" => "200"
            ];


            session()->put('session_coupon', $session_coupon);
            
        } else {
            $message['message'] = "Invalid Coupon Code";

            $session_coupon = [
                "code"  => "",
                "logic" => "",
                "status_code" => "404"
            ];

            session()->put('session_coupon', $session_coupon);
        }

        return $message;
    }

     //existing points check
    public function getCartRedeemPoints()
    {
        $total_cart_points         = 0;


        $mycart = session()->get('cart');

        if($mycart) {

            foreach ($mycart as $key => $item) {

                if($item['product_type'] == 'redeem_product') {
                    $total_cart_points += $item['quantity'] * $item['exchange_reward_points'];
                }
            }
        } 


        if($total_cart_points > 0) {
            return $total_cart_points;
        }

        return 0;
    }

    //existing points check
    public function canUsePoints()
    {
        $total_used_points         = 0;

        $total_reward_points        = Auth::guard("customer_web")->user()->total_reward_points;

        $mycart = session()->get('cart');

        if($mycart) {

            foreach ($mycart as $key => $item) {

                if($item['product_type'] == 'redeem_product') {
                    $total_used_points += $item['quantity'] * $item['exchange_reward_points'];
                }
            }
        } 

        $remain_points = $total_reward_points - $total_used_points;

        if($remain_points > 0) {
            return $remain_points;
        }

        return 0;
    }


    public function changeShipMethod($method)
    {
        session()->put('shipping_type', $method);
    }

    // do later
    public function cartAttribute()
    {
        // item check coupon_code
        // product table sku and size etc that not include product name
        $product = Product::with(['languageProducts', 'languageProducts.product','productsCurrency', 'productsCategories' ])->findOrFail($id);

        if(!$product) {
            abort(404);
        } 

        

        // if not current currency price , english price add 
        // if not used and filtering enable  regular_price and sales_price at dashboard in the future , it can remove 
        if(isset($product['productsCurrency'][getDefaultCurrenyArrayPos()])) {
            $productsCurrency = $product['productsCurrency'][getDefaultCurrenyArrayPos()];
        } else {
            $productsCurrency = $product['productsCurrency'][0];
        }


        $product_id             = $product['languageProducts'][getDefaultCurrenyArrayPos()]['product_id'];
        $product_name           = $product['languageProducts'][getDefaultCurrenyArrayPos()]['name'];
        $product_price          = $productsCurrency['regular_price'];
        $sales_price            = isset($productsCurrency['sales_price']) ? $productsCurrency['sales_price'] : 0;
        $products_categories    = $product['productsCategories']->toArray();



        $sku                    = $product->sku;
        // hide for shop_product
        $exchange_reward_points = 0;
        // $exchange_reward_points = isset($product->exchange_reward_points) ? $product->exchange_reward_points : 0;


        $default_setting = getDefaultSetting();

        $currency_code = $default_setting['currency']['code'];
        $currency_symbol = $default_setting['currency']['symbol'];
    }


    public function addToCartFromWishlist($id, $quantity = 1)
    {
        // item check coupon_code
        // product table sku and size etc that not include product name
        $product = Product::where('id',$id)->first();

        if(!$product) {
            return abort(404);
        } 
        
        $cart = session()->get('cart');

        
        // if not current currency price , english price add 
        // if not used and filtering enable  regular_price and sales_price at dashboard in the future , it can remove 
   
        // if(isset($product['productsCurrency'][getDefaultCurrenyArrayPos()])) {
        //     $productsCurrency = $product['productsCurrency'][getDefaultCurrenyArrayPos()];
        // } else {
        //     $productsCurrency = $product['productsCurrency'][0];
        // }



        // $product_id             = $product['oid'];
        // $sku                    = $product['oid'];
        // $product_name           = $product['title'];
        // $product_name_ch        = $product['original_title'];
        // $product_price          = $product['price'];
        // $sales_price            = isset($product['price']) ? $product['price'] : 0;
        // $products_categories    = $product['productsCategories']->toArray();


          // form        : {
        //     id          : "",
        //     vendor_id   : "",
        //     vendor_name : "",
        //     product_id : "",
        //     quantity    : 1,
        //     ConfiguredItem : {
        //         Configurators : [
        //             {Pid : 0, Vid : 0},
        //             {Pid : 0, Vid : 0}
        //         ]
        //     },
        // },



        $id                     = $data['ConfiguredItem']['Id'];
        $product_id             = $product['oid'];
        $sku                    = $product['oid'];
        $product_name           = $product['title'];
        $product_name_ch        = $product['original_title'];
        $product_price          = $product['price'];
        $taobao_item_url        = $product['taobao_item_url'];
        $sales_price            = isset($product['price']) ? $product['price'] : 0;

        $vendor_id              = $data['vendor_id'];
        $vendor_name            = $data['vendor_name'];
        $ConfiguredItem         = $data['ConfiguredItem'];



        // $sku                    = $product->sku;

        // hide for shop_product
        $exchange_reward_points = 0; 
        // $exchange_reward_points = isset($product->exchange_reward_points) ? $product->exchange_reward_points : 0;

        // id  1
        // shipping_weight "KG"
        // shipping_dimensions "CM"
        // default_currency    "3"
        // default_language    "1"
        // coupon_module   1
        // promotion_module    1
        // reward_module   null
        // decimal_place   null
        // currency    
        // id  3
        // name    "Myanmar"
        // code    "MMK"
        // conversation_rate   "978"
        // symbol  "MMK"
        // status  "active"
        // created_at  "2020-09-03 21:35:00"
        // updated_at  "2020-09-03 21:35:39"

        // $default_setting = getDefaultSetting();
        // return $default_setting['currency']['code'];

        // die();

        $default_setting = $this->generalSettingRepo->getDefaultSettings()[0];
        // return $default_setting = getDefaultSetting();

        // did();

        $currency_code = $default_setting['currency']['code'];
        $currency_symbol = $default_setting['currency']['symbol'];
 
        // if cart is empty then this the first product
        if(!$cart) {
            $cart = [
                $id => [
                    "id"                        => $id,
                    "product_id"                => $product_id,
                    "name"                      => $product_name,
                    "quantity"                  => $quantity,
                    "price"                     => ($sales_price != "0") ? $sales_price : $product_price,
                    "sales_price"               => $sales_price,
                    "photo"                     => $product->picture->medium,
                    "currency_code"             => $currency_code,
                    "currency_symbol"           => $currency_symbol,
                    // "product_cats"              => $products_categories,
                    "sku"                       => $sku,
                    "taobao_item_url"           => $taobao_item_url,
                    "exchange_reward_points"    => $exchange_reward_points,
                    "product_type"              => "shop_product",
                    "wishlist"                  => 0,

                    "vendor_id"                 => $vendor_id,
                    "vendor_name"               => $vendor_name,
                    "ConfiguredItem"            => $ConfiguredItem,
                ]
            ];
 
            session()->put('cart', $cart);
            return $this->get_api_mycart();
        }
 
        // if cart not empty then check if this product exist then increment quantity
        if(isset($cart[$id])) {

            // if($request->has('qty'))
            // {
            $cart[$id]['quantity'] = $cart[$id]['quantity'] + $quantity;
            // } else {
            //     $cart[$id]['quantity']++;
            // }

            session()->put('cart', $cart);
            return $this->get_api_mycart();
        }
 
        // if item not exist in cart then add to cart with quantity = 1
        $cart[$id] = [
            "id"                        => $id,
            "product_id"                => $product_id,
            "name"                      => $product_name,
            "quantity"                  => $quantity,
            "price"                     => ($sales_price != "0") ? $sales_price : $product_price,
            "sales_price"               => $sales_price,
            "photo"                     => $product->picture->medium,
            "currency_code"             => $currency_code,
            "currency_symbol"           => $currency_symbol,
            // "product_cats"              => $products_categories,
            "sku"                       => $sku,
            "taobao_item_url"           => $taobao_item_url,
            "exchange_reward_points"    => $exchange_reward_points,
            "product_type"              => "shop_product",
            "wishlist"                  => 0,

            "vendor_id"                 => $vendor_id,
            "vendor_name"               => $vendor_name,
            "ConfiguredItem"            => $ConfiguredItem,
        ];
 
        session()->put('cart', $cart);

        return $this->get_api_mycart();
    }
}