@extends('bp-admin.layouts.admin.index')

@section('title', 'FAQ')

@section('content')
    <div class="row">
        <div class="col-md-12 tile">
            <div class="box box-danger">
                <div class="box-header">
                    <div class="row">
                        <div class="col-sm-9">
                          <!--   <a href="{{ url('bp-admin/page/create') }}" class="btn btn-success t">
                                <i class="fa fa-user"></i>
                                User Guide
                            </a> -->
                        </div>
                        <div class="col-sm-3 pull-right">
                            <a href="{{ url('bp-admin/faq/create') }}" class="btn btn-success  pull-right">
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
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($page as $c)
                        <tr>
                            <td>
                                <a href="{{ url('bp-admin/faq/'.$c->id.'/edit') }}" >{{$c->title}}</a>
                            </td>
                            <td>
                                {{$c->content}}
                            </td>
                            <td>
                                <a href="{{ url('bp-admin/faq/'.$c->id.'/edit') }}" class="btn btn-xs btn-info">Edit</a>
                                <a href="{{ url('bp-admin/faq/delete', [$c->id]) }}" class="btn btn-delete btn-xs btn-danger">Delete</a>
                            </td>
                            
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="pagination"> {{ $page->links() }} </div>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(document).ready(function () {
        });
    </script>
@stop