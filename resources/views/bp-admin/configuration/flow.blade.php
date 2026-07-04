@extends('bp-admin.layouts.admin.index')

@section('title', 'System flow')

@section('content')
<style>
    .flow-block { margin-bottom: 2rem; }
    .flow-block h5 { font-weight: 600; margin-bottom: 1rem; }
    .flow-row { display: flex; align-items: center; flex-wrap: wrap; gap: 0; }
    .flow-node {
        background: #fff; border: 1px solid #e5e7eb; border-left: 3px solid #cbd5e1;
        border-radius: 10px; padding: .65rem .85rem; min-width: 168px; position: relative;
        box-shadow: 0 1px 3px rgba(15,23,42,.06);
    }
    .flow-node .ttl { font-weight: 600; font-size: .88rem; color: #111827; }
    .flow-node .sub { font-size: .72rem; color: #64748b; margin-top: 1px; }
    .flow-node.trigger { border-left-color: #4f46e5; background: #eef2ff; }
    .flow-node.core    { border-left-color: #6366f1; }
    .flow-node.on      { border-left-color: #10b981; }
    .flow-node.off     { border-left-color: #cbd5e1; opacity: .65; }
    .flow-dot { position: absolute; top: .6rem; right: .6rem; width: 8px; height: 8px; border-radius: 50%; background: #cbd5e1; }
    .flow-dot.on { background: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,.15); }
    .flow-badge { display: inline-block; font-size: .6rem; text-transform: uppercase; letter-spacing: .3px;
        padding: 1px 5px; border-radius: 4px; margin-top: 4px; }
    .flow-badge.on  { background: #dcfce7; color: #15803d; }
    .flow-badge.off { background: #f1f5f9; color: #94a3b8; }
    .flow-arrow { flex: 0 0 44px; height: 2px; background: #cbd5e1; position: relative; }
    .flow-arrow::after { content: ''; position: absolute; right: -1px; top: -4px; border: 5px solid transparent; border-left-color: #cbd5e1; }
    .flow-col { display: flex; flex-direction: column; gap: .55rem; }
    @media (max-width: 720px) { .flow-arrow { flex-basis: 22px; } .flow-node { min-width: 130px; } }
</style>
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header" style="padding-bottom:.75rem;">
                <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                    <div>
                        <h4 class="mb-0">System flow</h4>
                        <small class="text-muted">How the CMS routes each service through your active plugins ({{ $activeCount }} active).</small>
                    </div>
                    <div class="d-flex align-items-center" style="gap:.6rem;">
                        <a href="{{ url('bp-admin/plugins') }}" class="btn btn-sm btn-outline-primary"><i class="fa fa-plug"></i> Plugins</a>
                        <a href="{{ url('bp-admin/configuration') }}" class="btn btn-sm btn-outline-secondary"><i class="fa fa-cog"></i> Configuration</a>
                    </div>
                </div>
            </div>
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">
                @foreach($flows as $flow)
                    <div class="flow-block">
                        <h5>{{ $flow['title'] }}</h5>
                        <div class="flow-row">
                            <div class="flow-node trigger">
                                <div class="ttl"><i class="fa {{ $flow['trigger']['icon'] }}"></i> {{ $flow['trigger']['label'] }}</div>
                            </div>
                            <div class="flow-arrow"></div>
                            <div class="flow-node core">
                                <div class="ttl">{{ $flow['core']['label'] }}</div>
                                <div class="sub">{{ $flow['core']['sub'] }}</div>
                            </div>
                            <div class="flow-arrow"></div>
                            <div class="flow-col">
                                @foreach($flow['providers'] as $p)
                                    <div class="flow-node {{ $p['active'] ? 'on' : 'off' }}">
                                        <span class="flow-dot {{ $p['active'] ? 'on' : '' }}"></span>
                                        <div class="ttl"><i class="fa {{ $p['icon'] }}"></i> {{ $p['label'] }}</div>
                                        <div class="sub">{{ $p['sub'] }}</div>
                                        @if(!empty($p['slug']))
                                            <span class="flow-badge {{ $p['active'] ? 'on' : 'off' }}">{{ $p['active'] ? 'active plugin' : 'inactive' }}</span>
                                        @elseif(!empty($p['fallback']))
                                            <span class="flow-badge {{ $p['active'] ? 'on' : 'off' }}">{{ $p['active'] ? 'in use' : 'standby' }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@stop
