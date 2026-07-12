@extends('bp-admin.layouts.admin.index')

@section('title', $promo ? 'Edit promotion' : 'Add promotion')

@section('content')
@php
    $action = $promo ? url('bp-admin/commerce/promotions/'.$promo->id) : url('bp-admin/commerce/promotions');
    $active = $promo ? (int) $promo->is_active === 1 : true;
    $dt = fn ($v) => $v ? \Illuminate\Support\Carbon::parse($v)->format('Y-m-d\TH:i') : '';
@endphp
<div class="row">
    <div class="col-md-8 tile">
        <div class="box box-danger">
            <div class="box-header" style="padding-bottom:.75rem;">
                <h4 class="mb-0"><i class="fa fa-tags"></i> {{ $promo ? 'Edit promotion' : 'Add promotion' }}</h4>
            </div>
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">
                @component('bp-admin.inc.alert')@endcomponent

                <form action="{{ $action }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label class="control-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $promo->title ?? '') }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-8 form-group">
                            <label class="control-label">Link (optional)</label>
                            <input type="text" name="link" class="form-control" value="{{ old('link', $promo->link ?? '') }}" placeholder="https://… or /shop">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="control-label">Badge</label>
                            <input type="text" name="badge" class="form-control" value="{{ old('badge', $promo->badge ?? '') }}" placeholder="-20%">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $promo->description ?? '') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="control-label">Starts</label>
                            <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at', $dt($promo->starts_at ?? null)) }}">
                            <small class="form-text text-muted">Leave empty to start immediately.</small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="control-label">Ends</label>
                            <input type="datetime-local" name="ends_at" class="form-control" value="{{ old('ends_at', $dt($promo->ends_at ?? null)) }}">
                            <small class="form-text text-muted">Leave empty for no end date.</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Image</label>
                        @if($promo && $promo->image)
                            <div class="mb-2"><img src="{{ bp_upload_url($promo->image) }}" style="height:70px;border-radius:6px;" alt="current"></div>
                        @endif
                        <input type="file" name="image" class="form-control-file" accept="image/*">
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="control-label">Sort order</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $promo->sort_order ?? '0') }}">
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input type="hidden" name="is_active" value="no">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="yes" {{ $active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>

                    <hr>
                    <a href="{{ url('bp-admin/commerce/promotions') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-save"></i> {{ $promo ? 'Save changes' : 'Create promotion' }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
