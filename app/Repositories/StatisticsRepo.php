<?php

namespace App\Repositories;

use DB;
use DataTables;
use Session;
use Hash;

use App\Models\Order;
use App\Models\Customers;
use App\Models\Products;
use Illuminate\Http\Request;
use App\Repositories\GeneralSettingRepo;

class StatisticsRepo
{
    protected $generalSettingRepo;

    public function __construct(GeneralSettingRepo $generalSettingRepo) {
        $this->generalSettingRepo   = $generalSettingRepo;
    }

    public function getThisMonth10LastOrder() {

        $data = Order::select("orders.id", "orders.shipping_first_name", "orders.shipping_last_name", "orders.city","orders.customer_type", "orders.discount_amount","orders.order_status","orders.payment_status", "orders.grand_total", "orders.payment_transaction_id")

        ->whereMonth('created_at', date('m'))

        ->whereYear('created_at', date('Y'))

        ->orderBy("orders.id", 'desc')

        ->limit(10)

        ->get();

        return $data;
    } 


    public function getMostCouponUsed() {

        $data = DB::table('orders')->select('coupon_code', DB::raw("COUNT(coupon_code) as total") )->where('coupon_code','!=' ,'')->where('payment_status', 'Success')->groupBy('coupon_code')->orderBy('total','desc')->get();

        return $data;
    }

    public function getTopBestSellItems() {

        $data = DB::table('order_items')->select('product_id', 'name', 'sku', 'price', DB::raw("SUM(qty_ordered) as total") )->groupBy('product_id')->limit(10)->get();

        return $data;
    }

    public function getTodayOrder() {

        $data = DB::table("orders")

        // ->select(DB::raw("sum(orders.grand_total) as months_sale"), DB::raw("COUNT(orders.id) as months_order"))
        ->whereDay('created_at', date('d'))

        ->whereMonth('created_at', date('m'))

        ->whereYear('created_at', date('Y'))

        ->orderBy("orders.id", 'desc')

        ->count();

        return $data;
    }


    public function getThisMonthSaleAndOrder() {

        $data = DB::table("orders")

        ->select(DB::raw("sum(orders.grand_total) as months_sale"), DB::raw("COUNT(orders.id) as months_order"))

        ->whereMonth('created_at', date('m'))

        ->whereYear('created_at', date('Y'))

        ->orderBy("orders.id", 'desc')

        ->first();

        return $data;
    }

    public function getThisMonthNewCustomer(){

        $data = Customers::orderBy("customers.id", 'desc')

        ->whereMonth('created_at', date('m'))

        ->whereYear('created_at', date('Y'))

        ->count();

        return $data;
    }

    public function getThisLowStockProductCount() {

        $getDefaultSettings = $this->generalSettingRepo->getDefaultSettings();
        $stock_limit = $getDefaultSettings[0]['stock_limit'];

        $data = DB::table("products")

        ->select(DB::raw("products.stock<".$stock_limit." as stocks"))

        ->orderBy("products.id", 'desc')

        ->count();

        return $data;
    }

}
