@extends('bp-admin.layouts.admin.index')

@section('title', 'News and Events')

@section('content')
    <div class="row">
        <div class="col-md-12 tile">
            <div class="box box-danger">
                <div class="box-header" style="padding-bottom:.75rem;">
                    <div class="row align-items-center">
                        <div class="col-sm-8">
                            <h4 class="mb-0">News &amp; events</h4>
                            <small class="text-muted">Time-sensitive announcements and events.</small>
                        </div>
                        <div class="col-sm-4 pull-right">
                            <a href="{{ url('bp-admin/news/create') }}" class="btn btn-success  pull-right">
                                <i class="fa fa-plus"></i>
                                New item
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body pt-3" style="border-top: 1px solid #eef0f3;">
                    <table  class="table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Language</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($post as $c)
                        <tr>
                            <td>
                                <a href="{{ url('bp-admin/news/'.$c->id.'/edit') }}" >{{$c->title}}</a> <br>
                            </td>
                            <td>
                                @if($c->post_type == "event")
                                    Event <br />
                                    {{$c->event_at}}
                                @else 
                                    News
                                @endif
                            </td>
                            <td>
                                @isset($c->translate)
                                    <a href="{{ url('bp-admin/news/'.$c->id.'/edit') }}" >{{langauge($c->lang)}}</a> | <a href="{{ url('bp-admin/news/'.$c->translate->id.'/edit') }}" >{{ langauge($c->translate->lang) }}</a>
                                @else
                                     <a href="{{ url('bp-admin/news/'.$c->id.'/edit') }}" >{{langauge($c->lang)}}</a> 
                                @endisset
                            </td>
                            <td>
                                <a href="{{ url('bp-admin/news/'.$c->id.'/edit') }}" class="btn btn-xs btn-info">Edit</a>
                                    <a href="{{ url('bp-admin/news/delete', [$c->id]) }}" class="btn btn-delete btn-xs btn-danger" onclick="return confirm('Delete this item?')">Delete</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No news or events yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="pagination"> {{ $post->links() }} </div>
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