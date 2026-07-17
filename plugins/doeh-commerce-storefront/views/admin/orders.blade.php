@extends('bp-admin.layouts.admin.index')

@section('title', 'DOEH Orders')

@section('content')
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header d-flex justify-content-between align-items-center" style="padding-bottom:.75rem;">
                <div>
                    <h4 class="mb-0"><i class="fa fa-receipt"></i> DOEH Orders</h4>
                    <small class="text-muted">Live from the DOEH Orders API — the same orders your POS sees. Read-only.</small>
                </div>
                <form method="GET" action="{{ url('bp-admin/doeh-orders') }}" class="d-flex" style="gap:.5rem;">
                    <input type="text" name="q" class="form-control form-control-sm" placeholder="Find by order id (ord_…)" style="width:220px;">
                    <button class="btn btn-sm btn-primary" type="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">
                @component('bp-admin.inc.alert')@endcomponent
                @if ($errors->any())
                    <div class="alert alert-warning">{{ $errors->first() }}</div>
                @endif

                @if (! $configured)
                    <div class="alert alert-info mb-0">
                        DOEH Commerce is not configured — set the merchant secret key in
                        <a href="{{ url('bp-admin/plugins') }}">Plugins → DOEH Commerce → Settings</a> to see orders here.
                    </div>
                @else
                    <form method="GET" action="{{ url('bp-admin/doeh-orders') }}" class="row g-2 align-items-end mb-3">
                        <div class="col-auto">
                            <label class="form-label small text-muted mb-1">From (UTC)</label>
                            <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-auto">
                            <label class="form-label small text-muted mb-1">To (inclusive)</label>
                            <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-auto">
                            <label class="form-label small text-muted mb-1">Status</label>
                            <input type="text" name="status" value="{{ $status }}" placeholder="any" class="form-control form-control-sm" style="width:110px;">
                        </div>
                        <div class="col-auto">
                            <label class="form-label small text-muted mb-1">Branch</label>
                            <input type="number" name="branch_id" value="{{ $branch }}" placeholder="all" class="form-control form-control-sm" style="width:90px;">
                        </div>
                        <div class="col-auto">
                            <label class="form-label small text-muted mb-1">Limit</label>
                            <input type="number" name="limit" value="{{ $limit }}" min="1" max="200" class="form-control form-control-sm" style="width:90px;">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-sm btn-outline-primary" type="submit"><i class="fa fa-filter"></i> Apply</button>
                        </div>
                    </form>

                    @if (! ($result['ok'] ?? false))
                        <div class="alert alert-warning mb-0">
                            {{ doeh_storefront_message($result['code'] ?? 'EDGE_TRANSPORT') }}
                            <span class="badge bg-secondary">{{ $result['code'] ?? 'EDGE_TRANSPORT' }}</span>
                            @if (($result['code'] ?? '') === 'EDGE_BAD_BODY')
                                <div class="small text-muted mt-1">The API refuses an over-large result rather than truncating it — narrow the date window or raise the limit.</div>
                            @endif
                        </div>
                    @else
                        @php $orders = $result['orders'] ?? []; @endphp
                        @if (empty($orders))
                            <div class="alert alert-light border mb-0">No orders in this window.</div>
                        @else
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Placed</th>
                                        <th>Status</th>
                                        <th>Customer</th>
                                        <th>Fulfilment</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $o)
                                        @php
                                            $oid = $o['id'] ?? '';
                                            // Report rows carry money as total.amount_minor with an
                                            // AUTHORITATIVE scale (0 for MMK) — prefer it over guessing.
                                            $tot = $o['total'] ?? null;
                                            $ft = $o['fulfillment']['type'] ?? (is_string($o['fulfillment'] ?? null) ? $o['fulfillment'] : null);
                                        @endphp
                                        <tr>
                                            <td><a href="{{ url('bp-admin/doeh-orders/view/'.$oid) }}"><strong>{{ $oid ?: '—' }}</strong></a></td>
                                            <td class="text-muted">{{ $o['created_at'] ?? $o['placed_at'] ?? '—' }}</td>
                                            <td><span class="badge bg-secondary">{{ $o['status'] ?? 'received' }}</span></td>
                                            <td class="text-muted">{{ $o['customer']['name'] ?? '—' }}</td>
                                            <td>{{ $ft ? ucfirst(str_replace('_', ' ', $ft)) : '—' }}</td>
                                            <td class="text-end" style="font-variant-numeric:tabular-nums;">
                                                @if (isset($tot['amount_minor']))
                                                    @php $scale = $tot['scale'] ?? null; @endphp
                                                    {{ $scale !== null
                                                        ? number_format($tot['amount_minor'] / (10 ** (int) $scale), (int) $scale).' '.($tot['currency'] ?? '')
                                                        : doeh_storefront_format_money($tot['amount_minor'], (string) ($tot['currency'] ?? '')) }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="small text-muted mt-2">{{ count($orders) }} order(s) · window {{ $from }} → {{ $to }} (UTC) · fulfilment shows when the API reports it.</div>
                        @endif
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
