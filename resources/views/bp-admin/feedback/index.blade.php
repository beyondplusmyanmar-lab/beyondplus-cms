@extends('bp-admin.layouts.admin.index')

@section('title', 'Feedback')

@section('content')
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header" style="padding-bottom:.75rem;">
                <h4 class="mb-0">Feedback inbox @if($unread)<span class="badge badge-danger">{{ $unread }} new</span>@endif</h4>
                <small class="text-muted">Messages submitted from the public feedback form.</small>
            </div>
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">
                @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>From</th><th>Subject</th><th style="width:150px">Received</th><th style="width:120px"></th></tr>
                    </thead>
                    <tbody>
                    @forelse($feedback as $item)
                        <tr @if(!$item->is_read) style="font-weight:600;" @endif>
                            <td>{{ $item->name }}<div class="small text-muted" style="font-weight:400;">{{ $item->email }}</div></td>
                            <td>{{ $item->subject ?: '—' }}</td>
                            <td class="text-muted small" style="font-weight:400;">{{ $item->created_at->diffForHumans() }}</td>
                            <td>
                                <a href="{{ url('bp-admin/feedback/'.$item->id) }}" class="btn btn-sm btn-outline-primary">Read</a>
                                <a href="{{ url('bp-admin/feedback/delete/'.$item->id) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this message?')"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No messages yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="mt-3">{{ $feedback->links() }}</div>
            </div>
        </div>
    </div>
</div>
@stop
