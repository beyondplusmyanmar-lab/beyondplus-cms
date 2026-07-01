@extends('bp-admin.layouts.admin.index')

@section('title', 'Media')

@section('content')
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header">
                <div class="row">
                    <div class="col-sm-9">
                        <h4>Show</h4>
                    </div>
                    <div class="col-sm-3 pull-right">
                        <a href="{{ url('bp-admin/media/create') }}" class="btn btn-success  pull-right">
                            <i class="fa fa-user-plus"></i>
                            New
                        </a>
                    </div>
                </div>

                @if(Auth::guard("admins")->user()->role > 2) 
                    <form action="{{ url('/bp-admin/media') }}" method="get">
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
                                        <a href="{{ url('/bp-admin/media') }}" class="btn btn-md btn-primary"><span class="fa fa-refresh"></span></a>
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
                            <th>Image</th>
                            <th>Tittle</th>
                            <th>Link </th>
                            <th>Option </th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($media as $c)
                        <tr>
                        <td><img src="{{ url("uploads/".$c->media_link)}}" alt="{{$c->media_name}}" height="200px" width="300px"/>
                            </td>
                            <td>
                                <a href="#" name="" />{{$c->media_name}}</a> 
                            </td>
                            <td>
                               <input type="text" value="{{ "/uploads/".$c->media_link}}" class="form-control">
                           </td>
                           <td>
                            <a href="{{ url('bp-admin/media/'.$c->media_id.'/edit') }}" class="btn btn-xs btn-info">Edit</a>
                            <a href="{{ url('bp-admin/media/delete', [$c->media_id]) }}" class="btn btn-delete btn-xs btn-danger">Delete</a>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="row">
                <div class="col-sm-12">
                    <div class="pagination"> {{ $media->links() }} </div>
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