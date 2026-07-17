@extends('bp-admin.layouts.admin.index')

@section('title', 'Order '.$id)

@section('content')
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header d-flex justify-content-between align-items-center" style="padding-bottom:.75rem;">
                <div>
                    <h4 class="mb-0"><i class="fa fa-receipt"></i> Order {{ $id }}</h4>
                    <small class="text-muted">Read back live from the DOEH Orders API.</small>
                </div>
                <a href="{{ url('bp-admin/doeh-orders') }}" class="btn btn-sm btn-outline-secondary"><i class="fa fa-arrow-left"></i> All orders</a>
            </div>
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">
                @if (! $configured)
                    <div class="alert alert-info mb-0">
                        DOEH Commerce is not configured — set the merchant secret key in
                        <a href="{{ url('bp-admin/plugins') }}">Plugins → DOEH Commerce → Settings</a>.
                    </div>
                @elseif (! ($result['ok'] ?? false))
                    <div class="alert alert-warning mb-0">
                        {{ doeh_storefront_message($result['code'] ?? 'EDGE_TRANSPORT') }}
                        <span class="badge bg-secondary">{{ $result['code'] ?? 'EDGE_TRANSPORT' }}</span>
                    </div>
                @else
                    @php
                        $order = $result['order'] ?? [];
                        $totals = $order['totals'] ?? [];
                        $currency = (string) ($totals['currency'] ?? '');
                        $ft = $order['fulfillment']['type'] ?? (is_string($order['fulfillment'] ?? null) ? $order['fulfillment'] : null);
                        $phone = $order['customer']['phone'] ?? null;
                    @endphp

                    <div class="mb-3">
                        <span class="badge bg-secondary">{{ $order['status'] ?? 'received' }}</span>
                        <span class="badge bg-light text-dark border">{{ $order['payment_status'] ?? 'unpaid' }}</span>
                        @if ($ft)
                            <span class="badge bg-info text-dark">{{ ucfirst(str_replace('_', ' ', $ft)) }}</span>
                        @endif
                        @if (! empty($order['created_at']))
                            <span class="text-muted small ms-2">placed {{ $order['created_at'] }}</span>
                        @endif
                        @if ($phone)
                            <span class="text-muted small ms-2"><i class="fa fa-phone"></i> {{ $phone }}</span>
                        @endif
                    </div>

                    @if (! empty($order['lines']))
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th style="width:120px">SKU</th>
                                    <th style="width:80px" class="text-end">Qty</th>
                                    <th style="width:160px" class="text-end">Line total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order['lines'] as $line)
                                    <tr>
                                        <td>{{ $line['name'] ?? $line['sku'] ?? '—' }}</td>
                                        <td class="text-muted">{{ $line['sku'] ?? '—' }}</td>
                                        <td class="text-end">{{ $line['qty'] ?? '—' }}</td>
                                        <td class="text-end" style="font-variant-numeric:tabular-nums;">
                                            {{ isset($line['line_total_minor']) ? doeh_storefront_format_money($line['line_total_minor'], $currency) : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    @if (isset($totals['grand_total_minor']))
                        <div class="text-end fs-5" style="font-variant-numeric:tabular-nums;">
                            <span class="text-muted me-2">Total</span>
                            <strong>{{ doeh_storefront_format_money($totals['grand_total_minor'], $currency) }}</strong>
                        </div>
                    @endif

                    <details class="mt-3">
                        <summary class="text-muted small" style="cursor:pointer;">Raw order payload</summary>
                        <pre class="small bg-light p-2 rounded mt-2" style="max-height:320px; overflow:auto;">{{ json_encode($order, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </details>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
