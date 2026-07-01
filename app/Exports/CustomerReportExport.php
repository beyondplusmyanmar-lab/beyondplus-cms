<?php

namespace App\Exports;

use App\Models\Customers;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CustomerReportExport implements FromView
{

    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($request){
        $this->request = $request;
    }

    public function view(): View{

            if($this->request->start_date == null && $this->request->to_date==null && $this->request->customer_types_id == null && $this->request->name==null){
                $customer_reports = [];
            }else{      
                $customer_reports = Customers::orderBy('id','desc');
                if ($this->request->has('start_date')&& isset($this->request->start_date)) {
                    $customer_reports->whereDate('created_at','>=',$this->request->start_date);
                }
                if ($this->request->has('to_date')&& isset($this->request->start_date)) {
                    $customer_reports->whereDate('created_at','<=',$this->request->to_date);
                }
                 if ($this->request->has('customer_types_id')&& isset($this->request->customer_types_id)) {
                    $customer_reports->where('customer_types_id',$this->request->customer_types_id);
                }
                if ($this->request->has('name')&& isset($this->request->name)){
                    $customer_reports->orWhereRaw("concat(first_name,' ', last_name) like '%" . $this->request->name . "%' ");
                }
                if($this->request->has('search') && isset($this->request->search)){
                    $search = $this->request->search;
                    $search_id = json_decode($search);
                    $customer_reports = $customer_reports->whereIn('id',$search_id );
                }
                $customer_reports = $customer_reports->get();
            }
        return view('bp-admin.reports.customer_reports_export', [
            'customers' => $customer_reports,
        ]);
  }
}
