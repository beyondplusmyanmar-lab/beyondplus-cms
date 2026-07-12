@extends('bp-admin.layouts.admin.index')

@section('title', $product ? 'Edit product' : 'Add product')

@section('content')
@php
    $action = $product ? url('bp-admin/commerce/'.$product->id) : url('bp-admin/commerce');
    $featured = $product ? (int) $product->is_featured === 1 : false;
    $active   = $product ? (int) $product->is_active === 1 : true;
@endphp
<div class="row">
    <div class="col-md-8 tile">
        <div class="box box-danger">
            <div class="box-header" style="padding-bottom:.75rem;">
                <h4 class="mb-0"><i class="fa fa-shopping-cart"></i> {{ $product ? 'Edit product' : 'Add product' }}</h4>
            </div>
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">
                @component('bp-admin.inc.alert')@endcomponent

                <form action="{{ $action }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label class="control-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="control-label">Price</label>
                            <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ old('price', $product->price ?? '0') }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="control-label">Sort order</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $product->sort_order ?? '0') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Short description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Image</label>
                        @if($product && $product->image)
                            <div class="mb-2"><img src="{{ bp_upload_url($product->image) }}" style="height:70px;border-radius:6px;" alt="current"></div>
                        @endif
                        <input type="file" name="image" class="form-control-file" accept="image/*">
                        <small class="form-text text-muted">JPG/PNG/WebP. Leave empty to keep the current image.</small>
                    </div>

                    <div class="form-check mb-2">
                        <input type="hidden" name="is_active" value="no">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="yes" {{ $active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active (visible on the site)</label>
                    </div>
                    <div class="form-check mb-3">
                        <input type="hidden" name="is_featured" value="no">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="yes" {{ $featured ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_featured">Featured (show on the homepage)</label>
                    </div>

                    <hr>
                    <a href="{{ url('bp-admin/commerce') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-save"></i> {{ $product ? 'Save changes' : 'Create product' }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
