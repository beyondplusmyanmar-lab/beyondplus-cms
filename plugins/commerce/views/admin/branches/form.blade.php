@extends('bp-admin.layouts.admin.index')

@section('title', $branch ? 'Edit location' : 'Add location')

@section('content')
@php
    $action = $branch ? url('bp-admin/commerce/branches/'.$branch->id) : url('bp-admin/commerce/branches');
    $active = $branch ? (int) $branch->is_active === 1 : true;
@endphp
<div class="row">
    <div class="col-md-8 tile">
        <div class="box box-danger">
            <div class="box-header" style="padding-bottom:.75rem;">
                <h4 class="mb-0"><i class="fa fa-map-marker"></i> {{ $branch ? 'Edit location' : 'Add location' }}</h4>
            </div>
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">
                @component('bp-admin.inc.alert')@endcomponent

                <form action="{{ $action }}" method="post">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label class="control-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $branch->name ?? '') }}" required placeholder="Downtown Store">
                    </div>

                    <div class="form-group">
                        <label class="control-label">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $branch->address ?? '') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="control-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $branch->phone ?? '') }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="control-label">Hours</label>
                            <input type="text" name="hours" class="form-control" value="{{ old('hours', $branch->hours ?? '') }}" placeholder="Mon–Sat 9am–6pm">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Map embed URL</label>
                        <input type="text" name="map_embed" class="form-control" value="{{ old('map_embed', $branch->map_embed ?? '') }}" placeholder="https://www.google.com/maps?q=…&output=embed">
                        <small class="form-text text-muted">Optional. Google Maps → Share → Embed a map → copy the src URL.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="control-label">Sort order</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $branch->sort_order ?? '0') }}">
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input type="hidden" name="is_active" value="no">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="yes" {{ $active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>

                    <hr>
                    <a href="{{ url('bp-admin/commerce/branches') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-save"></i> {{ $branch ? 'Save changes' : 'Create location' }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
