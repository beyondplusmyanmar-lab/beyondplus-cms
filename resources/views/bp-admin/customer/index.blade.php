@extends('bp-admin.layouts.admin.index')

@section('title', 'Customer')

@section('content')
    <div class="row">
        <div class="col-md-12 tile">
            <div class="box box-danger">
                <div class="box-header">
                    <div class="row">
                        <div class="col-sm-9">
                            
                            <div class="form-group">
                             {{ Form::open([
                                'method' => 'get'
                                ]) }}
                              <input type="text" name="search"   class="form-control" placeholder="Search" value="{{\Request::get('search')}}">
                             {{ Form::close() }}
                            </div>
                        </div>
                        <div class="col-sm-3 pull-right">
                             

                            <a href="{{ url('bp-admin/user/create') }}" class="btn btn-success  pull-right">
                                <i class="fa fa-user-plus"></i>
                                New
                            </a>
                        </div>
                    </div>
                </div>

                <!-- /.box-header -->
                <div class="box-body">
                    <table  class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($user as $c)
                        
                        
                        <tr>
                            <td>{{$c->first_name}}</a></td>
                            <td>{{$c->phone}}</td>
                            <td>{{$c->email}}</td>
                            <td>{{$c->created_at}}</td>
                            <td>
        
                                <div style="float:right">
                                <a href="{{ url('bp-admin/user/'.$c->id.'/edit') }}" class="btn btn-xs btn-info">Edit</a>
                                
                                <a href="{{ url('bp-admin/user/delete',[$c->id]) }}" class="btn btn-delete btn-xs btn-danger">Remove</a>
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
        $(document).ready(function () {
        });
    </script>
@endpush