@extends('bp-admin.layouts.admin.index')

@section('title', 'Customer Report')

@section('content')
<div class="row">
    <div class="col-md-12 tile">
            <div class="box box-danger">
                <div class="box-header">
                    <div class="row">
                        <div class="col-sm-5">
                            <h4>Customer Report</h4>
                        </div>
                        <div class="col-sm-3 pull-right">
                            <!-- <a href="{{ url('bp-admin/category/add') }}" class="btn btn-success  pull-right">
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
                        <form action="{{ url('/bp-admin/reports/customer-report') }}" method="get">
                            
                            <div class="row">
                                <div class="col-md-2">
                                        <input type="text" name="start_date" id="start_date" class="form-control" placeholder="Enter From Date" autocomplete="off" value="{{Request::get('start_date')}}">
                                </div>
                                <div class="col-md-2">
                                        <input type="text" name="to_date" id="to_date" class="form-control" placeholder="Enter To Date" autocomplete="off" value="{{Request::get('to_date')}}">
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control" name="customer_types_id" id="customer_types_id">
                                        <option value="">Customer Type</option>
                                        @foreach($customer_types as $key => $customer_type)
                                            <option value="{{$key}}" {{ (Request::get('customer_types_id') == $key) ? 'selected' : '' }}>{{$customer_type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name" autocomplete="off" value="{{Request::get('name')}}">
                                </div>
                                <div class="col-md-2 text-left">
                                    <button type="submit" class="btn btn-sm btn-info"><span class="fa fa-search"></span>Search</button>
                                    <a href="{{ url('/bp-admin/reports/customer-report') }}" class="btn btn-sm btn-primary"><span class="fa fa-refresh"></span></a>
                                </div>
                                <div class="col-md-2" style="text-align: right;">
                                    <a href="{{url('/bp-admin/reports/customer-report-export')}}?start_date={{Request::get('start_date')}}&to_date={{Request::get('to_date')}}&name={{Request::get('name')}}&customer_types_id={{Request::get('customer_types_id')}}" class="btn btn-sm btn-success" id="search_result"><span class="fa fa-download"></span>Customer Export</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            
                <table id="pagetable" class="table table-bordered datatable-report">
                    <thead>
                        <tr>
                             <th width="5%">No.</th>
                             <th class="text-nowrap">Name</th>
                             <th class="text-nowrap">Email</th>
                             <th class="text-nowrap">Phone</th>                             
                             <th class="text-nowrap">Join Date</th>
                             <th class="text-nowrap">Customer Type</th>
                             <th class="text-nowrap">Date</th>
                        </tr>
                    </thead>
                    <tbody id="tablecontents">
                    @foreach($customers as $id => $item)
                        <tr>
                            <td>{{ ++$id }}</td>
                            <td>{{ $item->first_name .' '. $item->last_name ?? '-'}}</td>
                            <td>{{ $item->email ?? '-'}}</td>
                            <td>{{ $item->phone ?? '-'}}</td>
                            <td>{{date('d-M-Y', strtotime($item->created_at)) ?? '-'}}</td>
                            <td>
                                {{ !empty($item->customer_types_id) ? $item->customerType->name : '' ?? ''}}
                            </td>
                            <td>
                                {{ $item->created_at->format('Y-m-d') ?? '' }}
                            </td>                          
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>


        
            </div>

         </div>
            <!-- /.box -->
        </div>
    </div>
@stop
@push('scripts')
 <script type="text/javascript">
    // $(function () {
    //     $('#pagetable').DataTable({
    //         "paging": true,
    //         "lengthChange": true,
    //         "searching": false,
    //         "ordering": true,
    //         "info": false,
    //         "autoWidth": false,
    //         "iDisplayLength":50,
    //     });
    // });    
</script>

<script>
    // $(function () {
    //     $('.datatable-Insurance').on('search.dt', function() {
    //     var search = $('.dataTables_filter input').val();
    //      var arr= [];
    //      table.$('tr', {"filter":"applied"}).each(function() {
    //         arr.push(this.id);
    //     });
    //     arr=JSON.stringify(arr);
    //     var customer_types_id=$('#customer_types_id').val();
    //     var start_date=$('#start_date').val();
    //     var to_date=$('#to_date').val();
    //     var name=$('#name').val();
    //     var url = "{{ url('/bp-admin/reports/customer-report') }}?start_date="+ start_date +"&to_date="+ to_date+"&customer_types_id="+ customer_types_id+"&name="+ name +"&search="+arr;
    //      $('#search_result').attr('href',  url );
    //      $('#detail_download').attr('href',  detail_download_url );        
       
    //     return false; 
    //     });      
    // });

    $(document).ready(function(){

        // $('#demoDate').datepicker({
        //     format: "dd/mm/yyyy",
        //     autoclose: true,
        //     todayHighlight: true
        //   });

        $('#start_date').datepicker({  format : 'yyyy-mm-dd',autoclose: true,
            todayHighlight: true});
        $('#to_date').datepicker({  format : 'yyyy-mm-dd',autoclose: true,
            todayHighlight: true});
        $('#country').on('change', function() {
            var start_date=$('#start_date').val();
            var to_date=$('#to_date').val();
            var url = "{{ url('/bp-admin/reports/customer-report') }}?start_date="+ start_date +"&to_date="+ to_date +$(this).val();
            if (url) {
                window.location = url;
            }
            return false;
        });
   });
</script>

   
@endpush