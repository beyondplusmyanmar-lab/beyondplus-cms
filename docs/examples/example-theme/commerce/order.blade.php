{{-- Overrides doeh-commerce-storefront::order. Data: ok, order, error, fulfillment. --}}
@extends('theme.example-theme.layouts.app')
@section('title', 'Order')

@section('content')
    @if ($ok && $order)
        @php
            // MONEY IS MINOR UNITS, and MMK (and friends) are ZERO-DECIMAL —
            // dividing by 100 would show 1,500 MMK as "15". This is the
            // currency-aware formatter every theme must use.
            $totals = $order['totals'] ?? [];
            $currency = (string) ($totals['currency'] ?? '');
            $zeroDecimal = ['MMK', 'JPY', 'KRW', 'VND', 'IDR', 'LAK', 'KHR'];
            $exp = in_array(strtoupper($currency), $zeroDecimal, true) ? 0 : 2;
            $fmt = fn ($minor) => number_format($exp === 0 ? (int) $minor : $minor / (10 ** $exp), $exp);
        @endphp

        <h1 style="margin-bottom:6px;">✓ Thank you</h1>
        <p class="muted" style="margin-bottom:18px;">Your order was placed with DOEH.</p>

        <div class="card">
            <div><span class="muted">Order</span> <strong>{{ $order['id'] ?? '—' }}</strong></div>
            <div style="margin-top:6px;"><span class="muted">Status</span> {{ $order['status'] ?? 'received' }} · {{ $order['payment_status'] ?? 'unpaid' }}</div>
            @if (! empty($fulfillment))
                {{-- Show what THIS session chose; null when no choice was made. --}}
                <div style="margin-top:6px;"><span class="muted">Fulfilment</span> {{ ucfirst(str_replace('_', ' ', $fulfillment)) }}</div>
            @endif
            @if (! empty($order['lines']))
                <div style="border-top:1px solid var(--line); margin-top:12px; padding-top:8px;">
                    @foreach ($order['lines'] as $line)
                        <div style="display:flex; justify-content:space-between; padding:4px 0;">
                            <span>{{ $line['name'] ?? $line['sku'] }} <span class="muted">× {{ $line['qty'] }}</span></span>
                            <span class="money">{{ $fmt($line['line_total_minor'] ?? 0) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
            @if (isset($totals['grand_total_minor']))
                <div style="display:flex; justify-content:space-between; border-top:2px solid var(--ink); margin-top:10px; padding-top:10px; font-size:18px;">
                    <strong>Total</strong>
                    <strong class="money">{{ $fmt($totals['grand_total_minor']) }} {{ $currency }}</strong>
                </div>
            @endif
        </div>
    @else
        <h1>Order</h1>
        <div class="card" style="border-color:#dc2626; color:#dc2626;">{{ $error ?? 'That order could not be found.' }}</div>
    @endif

    <p><a href="{{ url('/store') }}">← Back to the shop</a></p>
@endsection
