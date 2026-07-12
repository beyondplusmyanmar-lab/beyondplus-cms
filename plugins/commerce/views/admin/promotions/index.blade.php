@extends('bp-admin.layouts.admin.index')

@section('title', 'Promotions')

@section('content')
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header d-flex justify-content-between align-items-center" style="padding-bottom:.75rem;">
                <div>
                    <h4 class="mb-0"><i class="fa fa-tags"></i> Promotions</h4>
                    <small class="text-muted">Campaigns shown on the Business theme homepage while active and within their date window.</small>
                </div>
                <a href="{{ url('bp-admin/commerce/promotions/create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add promotion</a>
            </div>
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">
                @component('bp-admin.inc.alert')@endcomponent
                @include('commerce::admin._tabs')

                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th style="width:100px">Badge</th>
                            <th style="width:230px">Window</th>
                            <th style="width:100px">Status</th>
                            <th style="width:110px" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($promos as $p)
                            @php
                                $now = now();
                                $started = ! $p->starts_at || \Illuminate\Support\Carbon::parse($p->starts_at)->lte($now);
                                $notEnded = ! $p->ends_at || \Illuminate\Support\Carbon::parse($p->ends_at)->gte($now);
                                $live = $p->is_active && $started && $notEnded;
                            @endphp
                            <tr>
                                <td><strong>{{ $p->title }}</strong></td>
                                <td>@if($p->badge)<span class="badge badge-warning">{{ $p->badge }}</span>@endif</td>
                                <td class="small text-muted">
                                    {{ $p->starts_at ? \Illuminate\Support\Carbon::parse($p->starts_at)->format('d M Y') : '—' }}
                                    &rarr;
                                    {{ $p->ends_at ? \Illuminate\Support\Carbon::parse($p->ends_at)->format('d M Y') : '—' }}
                                </td>
                                <td>
                                    @if($live)<span class="badge badge-success">Live</span>
                                    @elseif(! $p->is_active)<span class="badge badge-secondary">Off</span>
                                    @else<span class="badge badge-info">Scheduled</span>@endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ url('bp-admin/commerce/promotions/'.$p->id.'/edit') }}" class="btn btn-xs btn-outline-secondary"><i class="fa fa-pencil"></i></a>
                                    <a href="{{ url('bp-admin/commerce/promotions/'.$p->id.'/delete') }}" class="btn btn-xs btn-outline-danger" onclick="return confirm('Delete this promotion?')"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No promotions yet. <a href="{{ url('bp-admin/commerce/promotions/create') }}">Add one</a>.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
