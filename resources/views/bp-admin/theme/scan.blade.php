@extends('bp-admin.layouts.admin.index')

@section('title', 'Theme security scan')

@section('content')
<div class="row">
    <div class="col-md-10 tile">
        <div class="box box-danger">
            <div class="box-header" style="padding-bottom:.75rem;">
                <h4 class="mb-0"><i class="fa fa-shield"></i> Security scan — {{ $meta['name'] ?? $slug }}</h4>
                <small class="text-muted">Static scan of the theme's PHP/Blade files (inline &lt;script&gt; is ignored). A safety net, not a sandbox — only install themes you trust.</small>
            </div>
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">

                @if(empty($scan['critical']) && empty($scan['warning']))
                    <div class="alert alert-success mb-0"><i class="fa fa-check"></i> No risky patterns found. This theme looks clean.</div>
                @endif

                @if(!empty($scan['critical']))
                    <h6 class="text-danger"><i class="fa fa-ban"></i> Critical — activation is blocked</h6>
                    <table class="table table-sm mb-4">
                        <thead><tr><th style="width:45%">File</th><th>Reason</th></tr></thead>
                        <tbody>
                            @foreach($scan['critical'] as $f)
                                <tr class="table-danger"><td><code>{{ $f['file'] }}</code></td><td>{{ $f['reason'] }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                @if(!empty($scan['warning']))
                    <h6 class="text-warning"><i class="fa fa-exclamation-triangle"></i> Warnings — allowed, review recommended</h6>
                    <table class="table table-sm mb-4">
                        <thead><tr><th style="width:45%">File</th><th>Reason</th></tr></thead>
                        <tbody>
                            @foreach($scan['warning'] as $f)
                                <tr><td><code>{{ $f['file'] }}</code></td><td>{{ $f['reason'] }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <a href="{{ url('bp-admin/themes') }}" class="btn btn-sm btn-outline-secondary">Back to Themes</a>
                @if(empty($scan['critical']))
                    <form action="{{ url('bp-admin/themes/activate') }}" method="post" class="d-inline">
                        {{ csrf_field() }}
                        <input type="hidden" name="theme" value="{{ $slug }}">
                        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-check"></i> Activate</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
