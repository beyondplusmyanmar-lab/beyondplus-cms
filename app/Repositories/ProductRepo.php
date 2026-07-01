<?php

namespace App\Repositories;

use DB;
use Auth;
use Hash;
use Session;

use App\Models\Product;
use App\Models\Category;

use App\Repositories\GeneralSettingRepo;

class ProductRepo
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


    public function save_product($input) {
        //dd($input['o_id']);
        // $input = json_decode($input);
        // $input = json_decode($input);
        $oid = $input['oid'];

        // dd($oid);
        // echo $oid;
        $product = Product::where('oid', $oid)->first();

        //dd($product);

        if( $product ) {
            // echo "Ok";
            return $product->update($input);
        }

        // dd($input);
        $product = new Product;
        $product->fill($input);
        return $product->save();

    }

    public function  save_category($input) {

        return Category::insert($input);;
        // $category = new Category;

        // $category->fill($input);
        // return $category->save();

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