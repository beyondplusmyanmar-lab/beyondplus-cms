@extends('bp-admin.layouts.admin.index')

@section('title', 'System')

@section('content')
<style>
    .sys-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; }
    .sys-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 1rem 1.1rem; }
    .sys-card .lbl { font-size: .72rem; text-transform: uppercase; letter-spacing: .4px; color: #94a3b8; }
    .sys-card .val { font-size: 1.5rem; font-weight: 700; color: #111827; }
    .sys-notes { background: #0b1220; color: #cbd5e1; font-size: .8rem; line-height: 1.55; padding: .85rem 1rem;
        border-radius: 8px; max-height: 300px; overflow: auto; white-space: pre-wrap; word-break: break-word; }
</style>
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header" style="padding-bottom:.75rem;">
                <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                    <div>
                        <h4 class="mb-0">System</h4>
                        <small class="text-muted">Version info and core update status.</small>
                    </div>
                    <a href="{{ url('bp-admin/configuration/system?check=1') }}" class="btn btn-sm btn-outline-primary"><i class="fa fa-refresh"></i> Check for updates</a>
                </div>
            </div>
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">
                <div class="sys-grid mb-4">
                    <div class="sys-card"><div class="lbl">Beyond Plus CMS</div><div class="val">{{ $update['current'] }}</div></div>
                    <div class="sys-card"><div class="lbl">PHP</div><div class="val">{{ $php }}</div></div>
                    <div class="sys-card"><div class="lbl">Laravel</div><div class="val">{{ $laravel }}</div></div>
                </div>

                <h5 class="mb-2"><i class="fa fa-cloud-download text-muted"></i> Core updates</h5>
                @if(!($update['configured'] ?? false))
                    <div class="alert alert-secondary mb-0">Update checks are turned off. Enable “Check for core updates” in
                        <a href="{{ url('bp-admin/configuration') }}">Configuration</a>.</div>
                @elseif(!empty($update['error']))
                    <div class="alert alert-warning mb-0">Couldn’t reach GitHub to check for updates. Try again later.</div>
                @elseif(!empty($update['none']))
                    <div class="alert alert-info mb-0">No published release found for <code>{{ $repo }}</code> yet — you’re on <strong>{{ $update['current'] }}</strong>.</div>
                @elseif($update['update_available'])
                    <div class="alert alert-success">
                        <strong><i class="fa fa-arrow-up"></i> Update available:</strong>
                        version <strong>{{ $update['latest'] }}</strong> is out (you’re on {{ $update['current'] }}).
                        @if($update['url'])<a href="{{ $update['url'] }}" target="_blank" rel="noopener">View release <i class="fa fa-external-link"></i></a>@endif
                    </div>
                    @if($update['notes'])
                        <div class="mb-2 small text-muted">What’s new</div>
                        <div class="sys-notes">{{ $update['notes'] }}</div>
                    @endif
                    <p class="text-muted small mt-3 mb-0">To apply, pull the new release and run
                        <code>composer install</code> + <code>php artisan migrate</code> (a guided updater can automate this later).</p>
                @else
                    <div class="alert alert-success mb-0"><i class="fa fa-check"></i> You’re on the latest version (<strong>{{ $update['current'] }}</strong>).</div>
                @endif

                <div class="text-muted small mt-3">Checked against <code>github.com/{{ $repo }}</code> · cached for 6 hours.</div>
            </div>
        </div>
    </div>
</div>
@stop
