@extends('bp-admin.layouts.admin.index')

@section('title', 'Blocks')

@section('content')
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header" style="padding-bottom:.75rem;">
                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <h4 class="mb-0">Content blocks</h4>
                        <small class="text-muted">Reusable snippets you embed in a page or post body with the shortcode.</small>
                    </div>
                    <div class="col-sm-4">
                        <a href="{{ url('bp-admin/block/create') }}" class="btn btn-success pull-right">
                            <i class="fa fa-plus"></i> New block
                        </a>
                    </div>
                </div>
                @if(Auth::guard("admins")->user()->role > 2)
                    <form action="{{ url('/bp-admin/block') }}" method="get">
                        <div class="row pt-3">
                            <div class="col-md-6">
                                <input type="text" name="name" id="name" class="form-control" placeholder="Search by name"
                                       autocomplete="off" value="{{ Request::get('name') }}">
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-info"><span class="fa fa-search"></span> Search</button>
                                <a href="{{ url('/bp-admin/block') }}" class="btn btn-primary" title="Reset"><span class="fa fa-refresh"></span></a>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
            <!-- /.box-header -->
            <div class="box-body pt-3" style="border-top: 1px solid #eef0f3;">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:220px">Shortcode</th>
                            <th>Name</th>
                            <th>Language</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($block as $c)
                            <tr>
                                <td>
                                    <input type="text" class="form-control form-control-sm" value="[block]{{ $c->id }}[/block]"
                                           readonly onclick="this.select()" title="Paste into a page or post body">
                                </td>
                                <td><a href="{{ url('bp-admin/block/'.$c->id.'/edit') }}">{{ $c->title }}</a></td>
                                <td>
                                    <a href="{{ url('bp-admin/block/'.$c->id.'/edit') }}">{{ langauge($c->lang) }}</a>
                                    @isset($c->translate)
                                        | <a href="{{ url('bp-admin/block/'.$c->translate->id.'/edit') }}">{{ langauge($c->translate->lang) }}</a>
                                    @endisset
                                </td>
                                <td class="text-right">
                                    <a href="{{ url('bp-admin/block/'.$c->id.'/edit') }}" class="btn btn-sm btn-info"><i class="fa fa-pencil"></i> Edit</a>
                                    <a href="{{ url('bp-admin/block/delete', [$c->id]) }}" class="btn btn-sm btn-danger btn-delete"
                                       onclick="return confirm('Delete this block?')"><i class="fa fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No blocks yet. Create one, then embed it with its shortcode.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if(method_exists($block, 'links'))
                    <div class="row">
                        <div class="col-sm-12">{{ $block->links() }}</div>
                    </div>
                @endif
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
