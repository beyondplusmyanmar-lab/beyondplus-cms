<?php

namespace App\Services;

use DB;
use Auth;
use Hash;
use Session;

use App\Models\Product;

class ProductService
{
    // protected $generalSettingRepo;
    // protected $checkFinishedRule;
    // protected $hints;
    // protected $limit_item_count;

    // public function __construct(GeneralSettingRepo $generalSettingRepo) {
    //     $this->generalSettingRepo   = $generalSettingRepo;
    //     $this->checkFinishedRule    = [];
    //     $this->hints                = [];
    //     $this->limit_item_count     = 0;
    // }


    public function productJsonFormat($data, $type) {

        $data = $data;
        $data['Price'] = $data['Price'];
        

        return json_encode($data);
    }


    public function miniproductJsonFormat($data, $type) {

        $minidata['oid']            = $data['Id'];
        $minidata['provider_type']  = $data['ProviderType'];
        $minidata['title']          = $data['Title'];
        $minidata['original_title'] = $data['OriginalTitle'];
        $minidata['category_id']     = $data['CategoryId'];
        $minidata['vendor_id']      = $data['VendorId'];
        $minidata['vendor_name']    = $data['VendorName'];
        $minidata['brand_id']       = (isset($data['BrandId'])) ? $data['BrandId'] : null;
        $minidata['taobao_item_url']  = $data['TaobaoItemUrl'];
        // $minidata['price']          = $data['Price']['OriginalPrice'];
        $minidata['price']          = $data['Price']['ConvertedPriceList']['Internal']['Price'];
        $picture['original']        = $data['Pictures'][0]['Url'];
        $picture['small']           = $data['Pictures'][0]['Small']['Url'];
        $picture['medium']           = $data['Pictures'][0]['Medium']['Url'];
        $picture['large']           = $data['Pictures'][0]['Large']['Url'];
        $minidata['picture']        = $picture;
        

        return $minidata;
    }




     // // item check coupon_code
     //    // product table sku and size etc that not include product name
     //    $product = Product::with(['languageProducts', 'languageProducts.product','productsCurrency', 'productsCategories' ])->findOrFail($id);

     //    if(!$product) {
     //        abort(404);
     //    } 

        

     //    // if not current currency price , english price add 
     //    // if not used and filtering enable  regular_price and sales_price at dashboard in the future , it can remove 
     //    if(isset($product['productsCurrency'][getDefaultCurrenyArrayPos()])) {
     //        $productsCurrency = $product['productsCurrency'][getDefaultCurrenyArrayPos()];
     //    } else {
     //        $productsCurrency = $product['productsCurrency'][0];
     //    }


     //    $product_id             = $product['languageProducts'][getDefaultCurrenyArrayPos()]['product_id'];
     //    $product_name           = $product['languageProducts'][getDefaultCurrenyArrayPos()]['name'];
     //    $product_price          = $productsCurrency['regular_price'];
     //    $sales_price            = isset($productsCurrency['sales_price']) ? $productsCurrency['sales_price'] : 0;
     //    $products_categories    = $product['productsCategories']->toArray();



     //    $sku                    = $product->sku;
     //    // hide for shop_product
     //    $exchange_reward_points = 0;
     //    // $exchange_reward_points = isset($product->exchange_reward_points) ? $product->exchange_reward_points : 0;


     //    $default_setting = getDefaultSetting();

     //    $currency_code = $default_setting['currency']['code'];
     //    $currency_symbol = $default_setting['currency']['symbol']; 
}