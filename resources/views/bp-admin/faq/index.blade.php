@extends('bp-admin.layouts.admin.index')

@section('title', 'FAQs')

@section('content')
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header" style="padding-bottom:.75rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">Frequently asked questions</h4>
                        <small class="text-muted">Shown on the public <code>/faq</code> page when enabled in Configuration.</small>
                    </div>
                    <a href="{{ url('bp-admin/faq/create') }}" class="btn btn-success"><i class="fa fa-plus"></i> New FAQ</a>
                </div>
            </div>
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">
                @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th style="width:60px">#</th><th>Question</th><th style="width:90px">Status</th><th style="width:120px">Actions</th></tr>
                    </thead>
                    <tbody>
                    @forelse($faqs as $faq)
                        <tr>
                            <td>{{ $faq->sort_order }}</td>
                            <td>{{ $faq->question }}</td>
                            <td>@if($faq->is_active)<span class="badge badge-success">Active</span>@else<span class="badge badge-secondary">Hidden</span>@endif</td>
                            <td>
                                <a href="{{ url('bp-admin/faq/'.$faq->id.'/edit') }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa fa-pencil"></i></a>
                                <a href="{{ url('bp-admin/faq/delete/'.$faq->id) }}" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this FAQ?')"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No FAQs yet. Add your first one.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="mt-3">{{ $faqs->links() }}</div>
            </div>
        </div>
    </div>
</div>
@stop
