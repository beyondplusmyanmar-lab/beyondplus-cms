@extends('bp-admin.layouts.admin.index')

@section('title', 'Order Report')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header">
                    <div class="row">
                        <div class="col-sm-5">
                            <h4>Order Report</h4>
                        </div>
                        <div class="col-sm-3 pull-right">
                            <!-- <a href="{{ url('dashboard/category/add') }}" class="btn btn-success  pull-right">
                                <i class="fa fa-user-plus"></i>
                                New
                            </a> -->
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
            <!-- end panel-heading -->
            <div class="table-responsive panel-body">
                <div class="row my-2">
                    <div class="col-md-12 text-right">
                        <form action="{{ url('/dashboard/reports/order-report') }}" method="get">
                            
                            <div class="row">
                                <div class="col-md-2">
                                        <input type="text" name="start_date" id="start_date" class="form-control" placeholder="Enter From Date" autocomplete="off" value="{{Request::get('start_date')}}">
                                </div>
                                <div class="col-md-2">
                                        <input type="text" name="to_date" id="to_date" class="form-control" placeholder="Enter To Date" autocomplete="off" value="{{Request::get('to_date')}}">
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control" name="order_status" id="order_status">
                                        <option value="">OrderStatus</option>
                                        @foreach (App\Models\Order::ORDER_STATUS as $key=>$value)
                                            <option value="{{$key}}" {{ (Request::get('order_status') == $key) ? 'selected' : '' }}>{{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="order_no" id="order_no" class="form-control" placeholder="Enter Order No" autocomplete="off" value="{{Request::get('order_no')}}">
                                </div>
                                {{--
                                <div class="col-md-2">
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter Customer Name" autocomplete="off" value="{{Request::get('name')}}">
                                </div>
                                --}}
                                <div class="col-md-2 text-left">
                                    <button type="submit" class="btn btn-sm btn-info"><span class="fa fa-search"></span>Search</button>
                                    <a href="{{ url('/dashboard/reports/order-report') }}" class="btn btn-sm btn-primary"><span class="fa fa-refresh"></span></a>
                                </div>
                                <div class="col-md-2" style="text-align: right;">
                                    <a href="{{url('/dashboard/reports/order-report-export')}}?start_date={{Request::get('start_date')}}&to_date={{Request::get('to_date')}}&order_no={{Request::get('order_no')}}&order_status={{Request::get('order_status')}}" class="btn btn-sm btn-success" id="search_result"><span class="fa fa-download"></span>Order Export</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            
                <table id="pagetable" class="table table-bordered">
                    <thead>
                        <tr>
                             <th width="10%">No</th>
                             <th class="text-nowrap">Order No.</th>
                             <th class="text-nowrap">Customer Name</th>
                             <th class="text-nowrap">Customer Phone</th>
                             <th class="text-nowrap">Order Date</th>
                             <th class="text-nowrap">Order Status</th>
                             <th class="text-nowrap">Total</th>
                        </tr>
                    </thead>
                    <tbody id="tablecontents">
                    @foreach($orders as $id => $item)
                        <tr>
                            <td>{{$id+1}}</td>
                            <td>
                                <a href="{{ url('/dashboard/orders/' . $item->id) }}" target="_blank"> 
                                    {{ $item->id ?? ''}}
                                </a>                                
                            </td>
                            <td>{{$item->customer->first_name ?? ''}} {{$item->customer->last_name ?? ''}}</td>
                            <td>{{$item->customer->phone ?? ''}}</td>
                            <td>{{ $item->created_at->format('d-m-Y') ?? '' }} </td>
                           
                            <td>
                                @if ($item->order_status === Null)
                                    -
                                @else
                                    @foreach (App\Models\Order::ORDER_STATUS as $key=>$label)
                                       @if($item->order_status == $key)
                                            {{ $label ?? ''}}
                                       @endif
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @php
                                    $toal_amount = 0;
                                    $sub_total = 0;
                                    $grand_total = 0;
                                    $discount_price = 0;
                                    $dics_percent = 0;
                                    $dics_amount = 0;
                                    $cal_percent = 0;
                                    $coupon_amt = 0;
                                    $customer_percent = 0;
                                    $amt = 0;
                                @endphp
                                @foreach ($item->orderItems as $label)
                                    @php

                                        $dics_percent = $label->discount_percent;                                            
                                        $dics_amount += $label->discount_amount;
                                        $discount_price  = ($label->price) - ($label->discount_percent);
                                        $total_amount = $label->price;
                                        $sub_total += $total_amount;
                                        $cal_percent = ($sub_total / 100)* $label->discount_percent ;
                                        
                                        if($item->customer_type==1){
                                            $customer_percent = 0;
                                        }else{
                                                if(!isset($item->customertype->discount_amount)){
                                                    $amt = 0;
                                                }else{
                                                    $amt = $item->customertype->discount_amount;
                                                }
                                            $customer_percent = ($sub_total / 100) * $amt;
                                        }

                                        if($label->coupon_code == NULL){
                                            $coupon_amt = 0;
                                        }else{
                                            $coupon_amt = $label->coupon->coupon_amount;
                                        }

                                        if($item->coupon_code == NULL){
                                                $grand_total = ($sub_total + $item->shipping_amount) - ($dics_amount + $cal_percent + $customer_percent);
                                            }else{
                                                if($item->coupon->discount_type=="percent"){
                                                    $grand_total = ($sub_total + $item->shipping_amount) - ($dics_amount + $cal_percent + $customer_percent + (($sub_total / 100)* $item->coupon->coupon_amount));
                                                }elseif($item->coupon->discount_type=="fixed_cart"){
                                                    $grand_total = ($sub_total + $item->shipping_amount) - ($dics_amount + $cal_percent + $customer_percent + $item->coupon->coupon_amount);
                                                }
                                                else{
                                                    $grand_total = ($sub_total + $item->shipping_amount) - ($dics_amount + $cal_percent + $customer_percent + $item->coupon->coupon_amount);
                                                }
                                            }
                                    @endphp         
                                @endforeach

                                {{$item->currency->symbol ?? '$'}} {{ number_format($grand_total, 2, '.', ',') ?? '0' }}
                            </td>                                                   
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

         </div>
            <!-- /.box -->
        </div>
    </div>
@stop
@section('scripts')
 <script type="text/javascript">
      $(function () {
        $('#pagetable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": false,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "iDisplayLength":50,
        });


        // $( "#tablecontents" ).DataTable({
        //   items: "tr",
        // });
      });
    </script>

    <script>
    $(function () {       

        $('.datatable-Insurance').on('search.dt', function() {
        var search = $('.dataTables_filter input').val();
         var arr= [];
         table.$('tr', {"filter":"applied"}).each(function() {
            arr.push(this.id);
        });
        arr=JSON.stringify(arr);
        var order_status=$('#order_status').val();
        var start_date=$('#start_date').val();
        var to_date=$('#to_date').val();
        var order_no=$('#order_no').val();
        var url = "{{ url('/dashboard/reports/order-report') }}?start_date="+ start_date +"&to_date="+ to_date+"&order_no="+ order_no+"&order_status="+ order_status +"&search="+arr;
        //var detail_download_url = "{{url('/dashboard/orders')}}?start_date="+ start_date +"&to_date="+ to_date+"&country_id="+ country +"&search="+arr;

         $('#search_result').attr('href',  url );
         $('#detail_download').attr('href',  detail_download_url );
         
       
        return false; 
        });
      
    });

    $(document).ready(function(){

         $("#start_date").datepicker({
            dateFormat : 'yy-mm-dd',
            showOtherMonths: true,
            selectOtherMonths: true,
            autoclose: true,
            changeMonth: true,
            changeYear: true,
            //gotoCurrent: true,
           orientation: "bottom" // add this
        });

         $("#to_date").datepicker({
            dateFormat : 'yy-mm-dd',
            showOtherMonths: true,
            selectOtherMonths: true,
            autoclose: true,
            changeMonth: true,
            changeYear: true,
            //gotoCurrent: true,
           orientation: "bottom" // add this
        });

        // $('#start_date').datepicker({  dateFormat : 'yy-mm-dd'});
        // $('#to_date').datepicker(  {dateFormat : 'yy-mm-dd'});
        $('#country').on('change', function() {
            var start_date=$('#start_date').val();
            var to_date=$('#to_date').val();
            var url = "{{ url('/dashboard/reports/order-report') }}?start_date="+ start_date +"&to_date="+ to_date +$(this).val();
            if (url) {
                window.location = url;
            }
            return false;
        });
   });
</script>
@endsection
