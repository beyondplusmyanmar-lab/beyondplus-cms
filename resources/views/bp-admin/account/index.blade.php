@extends('bp-admin.layouts.admin.index')

@section('title', 'Staff Account')

@section('content')
    <div class="row">
        <div class="col-md-12 tile">
            <div class="box box-danger">
                <div class="box-header">
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="form-group">
                             <form action="{{ url('/bp-admin/account') }}" method="get">
                            
                                <div class="row">
                                  <!--   <div class="col-md-2">
                                            <input type="text" name="start_date" id="start_date" class="form-control" placeholder="Enter From Date" autocomplete="off" value="{{Request::get('start_date')}}">
                                    </div>
                                    <div class="col-md-2">
                                            <input type="text" name="to_date" id="to_date" class="form-control" placeholder="Enter To Date" autocomplete="off" value="{{Request::get('to_date')}}">
                                    </div>
                                    <div class="col-md-2">
                                            <input type="text" name="email" id="email" class="form-control" placeholder="Enter email" autocomplete="off" value="{{Request::get('email')}}">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name" autocomplete="off" value="{{Request::get('name')}}">
                                    </div>
                                    <!-- <div class="col-md-2">
                                        {{ Form::select('role',role_type(),Request::get('filter'), ['class'=>'form-control'])}}
                                    </div> -->
                                     <!--
                                    <div class="col-md-4 text-left">
                                        <button type="submit" class="btn btn-sm btn-info"><span class="fa fa-search"></span>Search</button>
                                        <a href="{{ url('/bp-admin/account') }}" class="btn btn-sm btn-primary"><span class="fa fa-refresh"></span></a>
                                    </div> -->
                                   <!--  <div class="col-md-2" style="text-align: right;">
                                        <a href="{{url('/bp-admin/reports/customer-report-export')}}?start_date={{Request::get('start_date')}}&to_date={{Request::get('to_date')}}&name={{Request::get('name')}}&customer_types_id={{Request::get('customer_types_id')}}" class="btn btn-sm btn-success" id="search_result"><span class="fa fa-download"></span>Customer Export</a>
                                    </div> -->
                                </div>
                            </form>
                            </div>
                        </div>
                        <div class="col-sm-3 pull-right">
                             

                            <a href="{{ url('bp-admin/account/create') }}" class="btn btn-success  pull-right">
                                <i class="fa fa-user-plus"></i>
                                New
                            </a>
                        </div>
                    </div>
                    <form action="{{ url('/bp-admin/account') }}" method="get">
                        <div class="row pb-4 pt-4">
                               
                                    
                                        <div class="col-md-3"></div>
                                        <div class="col-md-3">
                                             <!--    <input type="text" name="name" id="name" class="form-control" placeholder="Search with Name" autocomplete="off" value="{{Request::get('name')}}"> -->
                                        </div>

                                        
                                         <div class="col-md-4">
                                            @php
                                                $department =department();
                                                $department[0] = "All";
                                            @endphp
                                            {{ Form::select('filter',$department,Request::get('filter'), ['class'=>'form-control'])}}
                                        </div>
                                        <div class="col-md-2 text-left">
                                            <button type="submit" class="btn btn-md btn-info"><span class="fa fa-search"></span>Search</button>
                                            <a href="{{ url('/bp-admin/account') }}" class="btn btn-md btn-primary"><span class="fa fa-refresh"></span></a>
                                        </div>
                               
                        </div>
                    </form>
                </div>

                <!-- /.box-header -->
                <div class="box-body">
                    <table  class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>User Type</th>
                            <th>Email</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($adminaccounts as $adminaccount)
                        
                        
                        <tr>
                            <td>{{$adminaccount->name}} {{Auth::guard("admins")->user()->first_name}}</td>
                            <td>{{role_type($adminaccount->role)}}
                                <br />
                                @php
                                    $department =department();
                                    $department[0] = "All";
                                @endphp
                                {{$department[$adminaccount->department_type]}}
                            </td>
                            <td>{{$adminaccount->email}}</td>
                            <td>{{$adminaccount->created_at}}</td>
                            <td>
        
                                <div style="float:right">
                                <a href="{{ url('bp-admin/account/'.$adminaccount->id.'/edit') }}" class="btn btn-xs btn-info">Edit</a>
                                
                                <a href="{{ url('bp-admin/account/delete',[$adminaccount->id]) }}" class="btn btn-delete btn-xs btn-danger">Remove</a>
                                </div>
                             </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-12">
                           {{--!! dataPaginator($users, true) !!--}}
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
@stop

@push('scripts')
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