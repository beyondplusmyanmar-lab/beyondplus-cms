@extends('bp-admin.layouts.admin.index')

@section('title', $mode === 'edit' ? 'Edit FAQ' : 'New FAQ')

@section('content')
<div class="row">
    <div class="col-md-8 tile">
        <div class="box box-danger">
            <div class="box-header">
                <h4 class="mb-0">{{ $mode === 'edit' ? 'Edit FAQ' : 'New FAQ' }}</h4>
            </div>
            <div class="box-body">
                @if($errors->any())
                    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                @endif
                <form method="POST" action="{{ $mode === 'edit' ? url('bp-admin/faq/'.$faq->id) : url('bp-admin/faq/store') }}">
                    {{ csrf_field() }}
                    @if($mode === 'edit') {{ method_field('PUT') }} @endif
                    <div class="form-group">
                        <label class="control-label">Question</label>
                        <input type="text" name="question" class="form-control" value="{{ old('question', $faq->question) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Answer</label>
                        <textarea name="answer" class="form-control" rows="5" required>{{ old('answer', $faq->answer) }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="control-label">Sort order</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $faq->sort_order ?? 0) }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="control-label d-block">Active</label>
                            <label class="mt-2"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $faq->is_active ?? true) ? 'checked' : '' }}> Show on the public FAQ page</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ $mode === 'edit' ? 'Update' : 'Create' }}</button>
                    <a href="{{ url('bp-admin/faq') }}" class="btn btn-outline-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
