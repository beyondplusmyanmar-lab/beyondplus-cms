@extends('doeh-commerce-storefront::layout')
@section('title', 'DOEH Commerce — order')

@section('content')
    @if ($ok && $order)
        @php
            $totals = $order['totals'] ?? [];
            $grandMinor = $totals['grand_total_minor'] ?? null;
            $currency = $totals['currency'] ?? '';
            // Minor units → display, currency-aware. MMK (and other zero-decimal
            // currencies) ARE stored as whole units, so dividing by 100 would show
            // 1,500 MMK as "15". Only 2-decimal currencies get the /100.
            $zeroDecimal = ['MMK', 'JPY', 'KRW', 'VND', 'IDR', 'LAK', 'KHR'];
            $exp = in_array(strtoupper((string) $currency), $zeroDecimal, true) ? 0 : 2;
            $fmt = fn ($minor) => number_format($exp === 0 ? (int) $minor : $minor / (10 ** $exp), $exp);
            $grand = $grandMinor === null ? null : $fmt($grandMinor);
        @endphp
        <h1><span class="ok-badge">✓ Thank you!</span></h1>
        <p class="sub">Your order was placed with DOEH.</p>

        <div class="card">
            <div class="row">
                <div class="muted">Order</div>
                <div class="name">{{ $order['id'] ?? '—' }}</div>
            </div>
            <div class="row" style="margin-top:8px;">
                <div class="muted">Status</div>
                <div>{{ $order['status'] ?? 'received' }} · {{ $order['payment_status'] ?? 'unpaid' }}</div>
            </div>
            @if (! empty($fulfillment))
                <div class="row" style="margin-top:8px;">
                    <div class="muted">Fulfilment</div>
                    <div>{{ doeh_storefront_fulfillment_label($fulfillment)[0] }}</div>
                </div>
            @endif
            @if ($grand !== null)
                <div style="margin-top:16px; text-align:right;">
                    <div class="muted">Total</div>
                    <div class="total">{{ $grand }} {{ $currency }}</div>
                </div>
            @endif
        </div>

        @if (! empty($order['lines']))
            <div class="card">
                <table>
                    @foreach ($order['lines'] as $line)
                        <tr>
                            <td>
                                <div class="name">{{ $line['name'] ?? $line['sku'] }}</div>
                                <div class="hint">SKU {{ $line['sku'] }} · qty {{ $line['qty'] }}</div>
                            </td>
                            <td class="r">{{ $fmt($line['line_total_minor'] ?? 0) }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif
    @else
        <h1>Order</h1>
        <div class="err">{{ $error ?? 'That order could not be found.' }}</div>
    @endif

    <p style="margin-top:16px;"><a class="plain" href="{{ url('/store') }}">← Back to the shop</a></p>
@endsection
