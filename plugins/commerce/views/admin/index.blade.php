@extends('bp-admin.layouts.admin.index')

@section('title', 'Products')

@section('content')
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header d-flex justify-content-between align-items-center" style="padding-bottom:.75rem;">
                <div>
                    <h4 class="mb-0"><i class="fa fa-shopping-cart"></i> Products</h4>
                    <small class="text-muted">Your catalogue. Featured, active products appear on the Business theme homepage and /shop.</small>
                </div>
                <a href="{{ url('bp-admin/commerce/create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add product</a>
            </div>
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">
                @component('bp-admin.inc.alert')@endcomponent
                @include('commerce::admin._tabs')

                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:64px"></th>
                            <th>Name</th>
                            <th style="width:140px">Price</th>
                            <th style="width:150px">Status</th>
                            <th style="width:120px" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $p)
                            <tr>
                                <td>
                                    @if($p->image)
                                        <img src="{{ bp_upload_url($p->image) }}" alt="{{ $p->name }}" style="width:48px;height:48px;object-fit:cover;border-radius:6px;">
                                    @else
                                        <span class="text-muted"><i class="fa fa-image"></i></span>
                                    @endif
                                </td>
                                <td><strong>{{ $p->name }}</strong></td>
                                <td>{{ number_format((float) $p->price) }} {{ $currency }}</td>
                                <td>
                                    @if($p->is_active)<span class="badge badge-success">Active</span>@else<span class="badge badge-secondary">Hidden</span>@endif
                                    @if($p->is_featured)<span class="badge badge-warning">Featured</span>@endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ url('bp-admin/commerce/'.$p->id.'/edit') }}" class="btn btn-xs btn-outline-secondary"><i class="fa fa-pencil"></i></a>
                                    <a href="{{ url('bp-admin/commerce/'.$p->id.'/delete') }}" class="btn btn-xs btn-outline-danger" onclick="return confirm('Delete this product?')"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No products yet. <a href="{{ url('bp-admin/commerce/create') }}">Add your first product</a>.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
