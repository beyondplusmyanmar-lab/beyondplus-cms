@extends('bp-admin.layouts.admin.index')

@section('title', 'SMS Report')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header">
                    <div class="row">
                        <div class="col-sm-5">
                            <h4>SMS Report</h4>
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
                        <form action="{{ url('/dashboard/reports/sms-report') }}" method="get">
                            
                            <div class="row">
                                <div class="col-md-2">
                                        <input type="text" name="start_date" id="start_date" class="form-control" placeholder="Enter From Date" autocomplete="off" value="{{Request::get('start_date')}}">
                                </div>
                                <div class="col-md-2">
                                        <input type="text" name="to_date" id="to_date" class="form-control" placeholder="Enter To Date" autocomplete="off" value="{{Request::get('to_date')}}">
                                </div>
                                <div class="col-md-2 text-left">
                                    <button type="submit" class="btn btn-sm btn-info"><span class="fa fa-search"></span>Search</button>
                                    <a href="{{ url('/dashboard/reports/sms-report') }}" class="btn btn-sm btn-primary"><span class="fa fa-refresh"></span></a>
                                </div>
                            <!--     <div class="col-md-2" style="text-align: right;">
                                    <a href="{{url('/dashboard/reports/order-report-export')}}?start_date={{Request::get('start_date')}}&to_date={{Request::get('to_date')}}&order_no={{Request::get('order_no')}}&order_status={{Request::get('order_status')}}" class="btn btn-sm btn-success" id="search_result"><span class="fa fa-download"></span>Order Export</a>
                                </div> -->
                            </div>
                        </form>
                    </div>
                </div>
            
                <table id="pagetable" class="table table-bordered">
                    <thead>
                        <tr>
                             <th width="10%">No</th>
                             <th class="text-nowrap">Sender Name</th>
                             <th class="text-nowrap">Customer Phone</th>
                             <th class="text-nowrap">Message</th>
                             <th class="text-nowrap">Date</th>
                        </tr>
                    </thead>
                    <tbody id="tablecontents">
                    @foreach($sms_records as $id => $sms)
                        <tr>
                            <td>
                                <a href="{{ url('/dashboard/orders/' . $sms->id) }}" target="_blank"> 
                                    {{ $sms->id ?? ''}}
                                </a>                                
                            </td>
                            <td>{{$sms->sender_name ?? ''}} </td>
                            <td>{{$sms->phone ?? ''}}</td>
                            <td>{{$sms->message ?? ''}}</td>
                            <td>{{ $sms->created_at->format('d-m-Y') ?? '' }} </td>
                           
                                                                   
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
