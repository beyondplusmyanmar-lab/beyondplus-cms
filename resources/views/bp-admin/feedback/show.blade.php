@extends('bp-admin.layouts.admin.index')

@section('title', 'Feedback')

@section('content')
<div class="row">
    <div class="col-md-8 tile">
        <div class="box box-danger">
            <div class="box-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ $item->subject ?: 'Feedback' }}</h4>
                <a href="{{ url('bp-admin/feedback') }}" class="btn btn-sm btn-outline-secondary"><i class="fa fa-arrow-left"></i> Inbox</a>
            </div>
            <div class="box-body">
                <p class="text-muted mb-1"><strong>{{ $item->name }}</strong> &lt;{{ $item->email ?: 'no email' }}&gt; &middot; {{ $item->created_at->format('D, d M Y g:i A') }}</p>
                <hr>
                <div style="white-space:pre-wrap;">{{ $item->message }}</div>
                <hr>
                @if($item->email)
                    <a href="mailto:{{ $item->email }}?subject=Re: {{ $item->subject }}" class="btn btn-primary btn-sm"><i class="fa fa-reply"></i> Reply by email</a>
                @endif
                <a href="{{ url('bp-admin/feedback/delete/'.$item->id) }}" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this message?')"><i class="fa fa-trash"></i> Delete</a>
            </div>
        </div>
    </div>
</div>
@stop
