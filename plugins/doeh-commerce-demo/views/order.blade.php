@extends('doeh-commerce-demo::layout')
@section('title', 'DOEH Commerce demo — order')

@section('content')
    @if ($ok && $order)
        @php
            $totals = $order['totals'] ?? [];
            $grandMinor = $totals['grand_total_minor'] ?? null;
            $currency = $totals['currency'] ?? '';
            // Minor units → display. MMK has no minor unit in practice, but the API
            // is minor-unit throughout, so divide by 100 and drop a trailing .00.
            $grand = $grandMinor === null ? null : rtrim(rtrim(number_format($grandMinor / 100, 2), '0'), '.');
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
                            <td class="r">{{ rtrim(rtrim(number_format(($line['line_total_minor'] ?? 0) / 100, 2), '0'), '.') }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif
    @else
        <h1>Order</h1>
        <div class="err">{{ $error ?? 'That order could not be found.' }}</div>
    @endif

    <p style="margin-top:16px;"><a class="plain" href="{{ url('/doeh-demo') }}">← Back to the shop</a></p>
@endsection
