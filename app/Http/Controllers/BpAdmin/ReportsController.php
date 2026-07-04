<?php

namespace App\Http\Controllers\BpAdmin;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customers;
use App\Models\CustomerTypes;

// use PDF;
use DB;

// use App\Models\Order;
// use App\Models\OrderItems;
// use App\Models\Product;
// use App\Repositories\GeneralSettingRepo;

use Maatwebsite\Excel\Facades\Excel;

// use App\Exports\OrderExport;
// use App\Exports\OrderReportExport;
use App\Exports\CustomerReportExport;

// use App\Models\Category;
// use App\Models\SmsRecords;

// use Cviebrock\EloquentSluggable\Sluggable;
// use App\Repositories\ProductsRepo;
use App\Imports\CustomerImport;

class ReportsController extends Controller
{


    public function __construct() {
        $this->middleware('admins');
    }

    /** Full, filterable activity log (dashboard shows only the latest few). */
    public function activityLog(Request $request) {
        $logNames = \Spatie\Activitylog\Models\Activity::query()
            ->select('log_name')->distinct()->orderBy('log_name')->pluck('log_name')->filter()->values();

        $query = \Spatie\Activitylog\Models\Activity::with('causer')->latest();
        if ($request->filled('log') && $logNames->contains($request->query('log'))) {
            $query->where('log_name', $request->query('log'));
        }
        $activities = $query->paginate(30)->appends($request->query());

        return view('bp-admin.reports.activity', compact('activities', 'logNames'));
    }

    // protected $generalSettingRepo;

    // public function __construct(GeneralSettingRepo $generalSettingRepo) {
    //     $this->middleware('admins');
    //     $this->generalSettingRepo = $generalSettingRepo;
    //     // $this->middleware('permission:report-list|Full');
    //     // $this->middleware('permission:report-show|Full');
    //     // $this->middleware('permission:report-export|Full');
        
    // }

    // public function shipment(Request $request){


    //     if($request->transaction_id == null && $request->tracking_id==null && $request->package_id==null && $request->order_id==null  && $request->customer_name==null ){
    //         $orders = [];
    //     }else{


    //         $orders = Order::with('orderItems');

    //         if($request->has('tracking_id')) {
    //             // check in order items
    //             if ($request->tracking_id != null) {
    //                 // echo "ok3";
    //                 // $order =  $order->where('tracking_id',$request->tracking_id);

    //                 // $order =  $order->whereHas('order_items', function( $q) use($tracking_id){           
    //                 //         $q->where('order_items.tracking_id', $tracking_id);

    //                 //     }
    //                 // );
    //                 $tracking_id = $request->tracking_id;

    //                 //where has should be before order by
    //                 // $orders->whereHas('orderItems', function( $q) use($tracking_id){           
    //                 //         $q->where('order_items.tracking_id', $tracking_id);

    //                 //     }
    //                 // );

    //                 return redirect()->to('/bp-admin/order-trackingid/'.$tracking_id);

    //             }

    //         }
    //         // return $orders->orderBy('id','desc')->get();

    //         if($request->has('transaction_id')) {
    //             // check in order items
    //             if ($request->transaction_id != null  ) {
    //                 // echo "ok1";
    //                 // $order = $order->where('provider_transaction_id',$request->transaction_id);
    //                 // $order->where('tracking_id',$request->tracking_id);
    //                 // return $request->transaction_id;

    //                 $transaction_id = $request->transaction_id;

    //                 $orders =  $orders->whereHas('orderItems', function( $q) use($transaction_id){           
    //                         $q->where('order_items.provider_transaction_id', $transaction_id);

    //                     }
    //                 );

    //             }
    //         }

    //         // return $request->order_id;

    //         if($request->has('order_id')) {
    //             // check in order items

    //             if ($request->order_id != null  ) {

    //                 // echo "ok1";
    //                 $orders = $orders->where('id',$request->order_id);
    //                 // $order->where('tracking_id',$request->tracking_id);
    //                 // return $request->transaction_id;
    //             }
    //         }

            
            

    //         if($request->has('package_id')) {
    //             if ($request->package_id != null  ) {
    //                 // echo "ok1";
    //                 // $orders = $order->where('package_id',$request->package_id);
    //                 // $order->where('tracking_id',$request->tracking_id);
    //                 // return $request->transaction_id;

    //                 $package_id = $request->package_id;

    //                 $orders =  $orders->whereHas('orderItems', function( $q) use($package_id){           
    //                         $q->where('order_items.package_id', $package_id);

    //                     }
    //                 );

    //             }
    //         }


    //         // http://taobao-shopping.local/bp-admin/item-search?tracking_id=&transaction_id=&order_id=&customer_name=&package_id=57
    //         if ($request->customer_name != null) {
    //             if ($request->has('customer_name') && isset($request->customer_name)) {
    //                 // echo "ok";
    //                 // die();
    //                 // return $request->customer_name;
    //                 $orders =  $orders->orWhereRaw("shipping_first_name like '%" . $request->customer_name . "%' ");
    //                 // return $request->transaction_id;
    //             }
    //         }

    //         // die();
    //         $orders = $orders->orderBy('id','desc')->get();

    //         // return  $orders[0];

    //         if(count($orders) == 1) {

    //             if($request->has('order_id')) {
    //                 if ($request->order_id != null  ) {
    //                     return redirect()->to('/bp-admin/order/'.$orders[0]->id);
    //                 }
    //             }
    //         }
    //     }

    //     return view('bp-admin.shipment.index',compact('orders'));
    // }


    public function customerReport(Request $request){

        if($request->start_date == null && $request->to_date==null && $request->customer_types_id == null && $request->name==null){
            $customers = [];
            // $customers = Customers::orderBy('id','desc')->get();
        }else{
            $customers = Customers::orderBy('id','desc');

            if ($request->name != null  ) {
                if($request->has('name')) {
                    if($request->has('name')&& isset($request->name)) {   
                        // $customers = $customers->where('payment_transaction_id',$request->name);  
                        // echo $request->name;  
                        // - care         
                        $customers ->orWhereRaw("first_name like ?", ['%' . $request->name . '%']);
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



            


            $customers = $customers->get();
        }

        $customer_types = CustomerTypes::all()->pluck('name', 'id');
        return view('bp-admin.reports.customer_report',compact('customers','customer_types'));
    }


    // public function orderReport(Request $request){


    //     if($request->start_date == null && $request->to_date==null && $request->order_no==null && $request->order_status == null){
    //         $orders = [];
    //     }else{
    //         $orders = Order::orderBy('id','desc');

    //         if ($request->start_date != null  ) {
    //             if ($request->has('start_date') && isset($request->start_date)) {
    //                 $orders = $orders->whereDate('created_at', '>=', \Carbon\Carbon::parse($request->start_date)->format('Y-m-d'));

    //             }
    //         }


    //         if ($request->to_date != null  ) {
    //             if ($request->has('to_date') && isset($request->to_date)) {
    //                 $orders = $orders->whereDate('created_at', '<=',\Carbon\Carbon::parse($request->to_date)->format('Y-m-d'));

    //             }
    //         }


    //         if ($request->order_status != null  ) {
    //             if($request->has('order_status')) {
    //                 if($request->order_status != "") {
    //                     $orders = $orders->where('order_status',$request->order_status);
    //                 }
                    
    //                 // echo $request->order_status;
    //             }
    //         }



    //         if ($request->order_no != null  ) {
    //             if($request->has('order_no')) {
    //                 if($request->has('order_no')&& isset($request->order_no)) {   
    //                     // $orders = $orders->where('payment_transaction_id',$request->order_no);  
    //                     // echo $request->order_no;  
    //                     // - care         
    //                     $orders ->orWhereRaw("payment_transaction_id like '%" . $request->order_no . "%' ");
    //                 }
    //             }
    //         }

    //         $orders = $orders->get();
    //     }
        
    //     return view('bp-admin.reports.order_report',compact('orders'));
    // }

    // public function smsReport(Request $request) {
    //     if($request->start_date == null && $request->to_date==null ){
    //         $sms_records = SmsRecords::whereDate('created_at', \Carbon\Carbon::now()->format('Y-m-d'))->orderBy('id','desc')->get();
    //     }else{
    //         $sms_records = SmsRecords::orderBy('id','desc');

    //         if ($request->start_date != null  ) {
    //             if ($request->has('start_date') && isset($request->start_date)) {
    //                 $sms_records = $sms_records->whereDate('created_at', '>=', \Carbon\Carbon::parse($request->start_date)->format('Y-m-d'));

    //             }
    //         }


    //         if ($request->to_date != null  ) {
    //             if ($request->has('to_date') && isset($request->to_date)) {
    //                 $sms_records = $sms_records->whereDate('created_at', '<=',\Carbon\Carbon::parse($request->to_date)->format('Y-m-d'));

    //             }
    //         }


    //         $sms_records = $sms_records->get();
    //     }
        
    //     return view('bp-admin.reports.sms_report',compact('sms_records'));
    // }
    // public function productReport(Request $request){
        
    //     $keyword = $request->get('search');
    //     $perPage = 400;
    //     $default_currency = $this->generalSettingRepo->getDefaultCurrency();
    //     $default_settings = $this->generalSettingRepo->getDefaultSettings();
    //     // dd($default_settings);

    //     // if (!empty($keyword)) {
    //     //     $products = Product::with(['languageProducts', 'productsCurrency'])->where('name', 'LIKE', "%$keyword%")
    //     //         ->orWhere('slug', 'LIKE', "%$keyword%")
    //     //         ->orWhere('description', 'LIKE', "%$keyword%")
    //     //         ->orWhere('sku_number', 'LIKE', "%$keyword%")
    //     //         ->latest()->paginate($perPage);
    //     // }else{
    //     //     $products = Product::with(['languageProducts', 'productsCurrency'])->latest()->paginate($perPage);
    //     // }

    //     if($request->sale_count == null && $request->min_price==null && $request->max_price==null && $request->sku==null){

    //         // $products = Product::with(['languageProducts', 'productsCurrency'])->latest()->paginate($perPage);
    //         $products = [];
    //     }else{
    //         $products = Product::with(['languageProducts', 'productsCurrency']);
                       
    //         // if ($request->has('start_date')&& isset($request->start_date)) {
    //         //     $products->whereDate('created_at','>=',$request->start_date);
    //         // }
    //         // if ($request->has('to_date')&& isset($request->to_date)) {
    //         //     $products->whereDate('created_at','<=',$request->to_date);
    //         // }
    //         if ($request->has('min_price')&& isset($request->min_price)) {
    //             $products->whereHas('productsCurrency', function($q) use($request){
    //                 $q->where('regular_price','>=',(int)$request->min_price);
    //             });                
    //         }

    //         if ($request->has('max_price')&& isset($request->max_price)) {
    //             $products->whereHas('productsCurrency', function($q)use($request){
    //                 $q->where('regular_price','<=',(int)$request->max_price);
    //             });                
    //         }
    //         if ($request->has('sku')&& isset($request->sku)) {
    //             // $products->where('sku',$request->sku);
    //             $products ->orWhereRaw("sku like '%" . $request->sku . "%' ");         
    //         } 
    //         if ($request->has('sale_count')&& isset($request->sale_count)) {                
    //             $qty_count = (int)$request->sale_count;

    //             $products->having(DB::raw("(SELECT sum(order_items.qty_ordered) FROM order_items where products.id=order_items.product_id Group BY products.id)"), '>=', $qty_count );
    //         }             

    //         // $products = $products->latest()->paginate();
    //         $products = $products->get();
    //     }

    //     $productCategory=ProductsCategories::all();
    //     return view('bp-admin.reports.product_report', compact('products','productCategory', 'default_currency', 'default_settings'));
    // }

    // public function orderReportExport(Request $request){
    //     return Excel::download(new OrderReportExport($request), 'orders.csv');
    // }

    public function customerReportExport(Request $request){
        return Excel::download(new CustomerReportExport($request), date('d-m').'-customers.csv');
    }
    // public function productReportExport(Request $request){
    //     $keyword = $request->get('search');
    //     $perPage = 400;
    //     $default_currency = $this->generalSettingRepo->getDefaultCurrency();
    //     $default_settings = $this->generalSettingRepo->getDefaultSettings();

    //     $productCategory=ProductsCategories::all();
    //     return Excel::download(new ProductReportExport($request,$productCategory,$default_currency,$default_settings), 'products.csv');
    // }

    public function customerImportView() {
        return view('bp-admin.reports.customer_import');
    }

    public function customerImport() {

        Excel::import(new CustomerImport,request()->file('file'));
        return back();

    }
}