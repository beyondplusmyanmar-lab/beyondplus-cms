@extends('bp-admin.layouts.admin.index')

@section('title', 'Department Pages')

@section('content')
    <div class="row">
        <div class="col-md-12 tile">
            <div class="box box-danger">
                <div class="box-header">
                    <div class="row">
                        <div class="col-sm-10">
                            <h4>Department List</h4>
                        </div>
                        <div class="col-sm-1 pull-right">
                        </div>
                        <div class="col-sm-1 pull-right">
                            <a href="{{ url('bp-admin/department/create') }}" class="btn btn-success  pull-right">
                                <i class="fa fa-user-plus"></i>
                                New
                            </a>
                        </div>
                    </div>
                    @if(Auth::guard("admins")->user()->role > 2) 
                    <form action="{{ url('/bp-admin/department') }}" method="get">
                    <div class="row pb-4 pt-4">
                           
                                
                                    <div class="col-md-3"></div>
                                    <div class="col-md-3">
                                            <input type="text" name="name" id="name" class="form-control" placeholder="Search with Name" autocomplete="off" value="{{Request::get('name')}}">
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
                                        <a href="{{ url('/bp-admin/department') }}" class="btn btn-md btn-primary"><span class="fa fa-refresh"></span></a>
                                    </div>
                           
                    </div>
                    </form>
                    @else
                        <br />
                    @endif
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table  class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Short Code</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Languague</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($page as $c)
                        <tr>
                            <td>showPageBlock({{$c->id}})</td>
                            <td>
                                <a href="{{ url('bp-admin/department/'.$c->id.'/edit') }}" >{{$c->title}}</a>
                            </td>
                            <td>
                               {{department($c->post_type)}}
                            </td>
                            <td>@isset($c->translate)
                                    <a href="{{ url('bp-admin/department/'.$c->id.'/edit') }}" >{{langauge($c->lang)}}</a> | <a href="{{ url('bp-admin/department/'.$c->translate->id.'/edit') }}" >{{ langauge($c->translate->lang) }}</a>
                                @else
                                     <a href="{{ url('bp-admin/department/'.$c->id.'/edit') }}" >{{langauge($c->lang)}}</a> 
                                @endisset
                            </td>
                            <td>
                                <a href="{{ url('bp-admin/department/'.$c->id.'/edit') }}" class="btn btn-xs btn-info">Edit</a>
                                <a href="{{ url('bp-admin/department/delete', [$c->id]) }}" class="btn btn-delete btn-xs btn-danger">Delete</a>
                            </td>
                            
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="pagination"> {{ $page->appends(Request::all())->links() }} </div>
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