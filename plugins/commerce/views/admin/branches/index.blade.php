@extends('bp-admin.layouts.admin.index')

@section('title', 'Locations')

@section('content')
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header d-flex justify-content-between align-items-center" style="padding-bottom:.75rem;">
                <div>
                    <h4 class="mb-0"><i class="fa fa-map-marker"></i> Store locations</h4>
                    <small class="text-muted">Branches shown in the Business theme's Locations section.</small>
                </div>
                <a href="{{ url('bp-admin/commerce/branches/create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add location</a>
            </div>
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">
                @component('bp-admin.inc.alert')@endcomponent
                @include('commerce::admin._tabs')

                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th style="width:140px">Phone</th>
                            <th style="width:90px">Status</th>
                            <th style="width:110px" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branches as $b)
                            <tr>
                                <td><strong>{{ $b->name }}</strong></td>
                                <td class="small text-muted">{{ \Illuminate\Support\Str::limit($b->address, 60) }}</td>
                                <td class="small">{{ $b->phone }}</td>
                                <td>@if($b->is_active)<span class="badge badge-success">Active</span>@else<span class="badge badge-secondary">Hidden</span>@endif</td>
                                <td class="text-end">
                                    <a href="{{ url('bp-admin/commerce/branches/'.$b->id.'/edit') }}" class="btn btn-xs btn-outline-secondary"><i class="fa fa-pencil"></i></a>
                                    <a href="{{ url('bp-admin/commerce/branches/'.$b->id.'/delete') }}" class="btn btn-xs btn-outline-danger" onclick="return confirm('Delete this location?')"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No locations yet. <a href="{{ url('bp-admin/commerce/branches/create') }}">Add one</a>.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
