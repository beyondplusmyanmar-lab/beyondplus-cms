{{-- Overrides doeh-commerce-storefront::order. Data: ok, order, error. --}}
@extends('theme.doeh-retail.layouts.app')
@section('title', 'Order confirmed')

@section('content')
    @php $mm = app()->getLocale() === 'mm'; @endphp
    @if ($ok && $order)
        @php
            $totals = $order['totals'] ?? [];
            $grandMinor = $totals['grand_total_minor'] ?? null;
            $currency = $totals['currency'] ?? '';
            // Currency-aware minor→display: MMK (and other zero-decimal currencies)
            // are whole units — no /100 (else 1,500 MMK renders as "15").
            $zeroDecimal = ['MMK', 'JPY', 'KRW', 'VND', 'IDR', 'LAK', 'KHR'];
            $exp = in_array(strtoupper((string) $currency), $zeroDecimal, true) ? 0 : 2;
            $fmt = fn ($minor) => number_format($exp === 0 ? (int) $minor : $minor / (10 ** $exp), $exp);
            $grand = $grandMinor === null ? null : $fmt($grandMinor);
        @endphp

        <section style="max-width:560px; margin:0 auto;">
            <div style="text-align:center; margin-bottom:20px;">
                <div class="rt-chip stock" style="font-size:13px;">✓ {{ $mm ? 'အော်ဒါ လက်ခံပြီး' : 'Order confirmed' }}</div>
                <h1 style="font-size:30px; margin-top:12px;">{{ $mm ? 'ကျေးဇူးတင်ပါသည်' : 'Thanks for your order' }}</h1>
                <p class="rt-muted" style="margin:6px 0 0;">{{ $mm ? 'အော်ဒါကို DOEH တွင် မှတ်တမ်းတင်ပြီးပါပြီ။' : 'Your order is placed with DOEH.' }}</p>
            </div>

            <div class="rt-card" style="padding:22px 24px;">
                <div style="display:flex; justify-content:space-between; align-items:baseline;">
                    <span class="rt-muted" style="font-size:12px; font-weight:600; letter-spacing:.06em; text-transform:uppercase;">{{ $mm ? 'အော်ဒါ' : 'Order' }}</span>
                    <span class="rt-display" style="font-size:18px;">{{ $order['id'] ?? '—' }}</span>
                </div>
                <div class="rt-muted" style="font-size:14px; margin-top:4px;">{{ $order['status'] ?? 'received' }} · {{ $order['payment_status'] ?? 'unpaid' }}</div>

                @if (! empty($order['lines']))
                    <div style="border-top:1px solid var(--line); margin-top:16px; padding-top:8px;">
                        @foreach ($order['lines'] as $line)
                            <div style="display:flex; justify-content:space-between; gap:12px; padding:7px 0;">
                                <span>{{ $line['name'] ?? $line['sku'] }} <span class="rt-muted">× {{ $line['qty'] }}</span></span>
                                <span class="rt-price">{{ $fmt($line['line_total_minor'] ?? 0) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($grand !== null)
                    <div style="display:flex; justify-content:space-between; align-items:baseline; border-top:2px solid var(--ink); margin-top:12px; padding-top:14px;">
                        <span style="font-weight:600; font-family:'Space Grotesk',system-ui,sans-serif;">{{ $mm ? 'စုစုပေါင်း' : 'Total' }}</span>
                        <span class="rt-price" style="font-size:26px; color:var(--money);">{{ $grand }} {{ $currency }}</span>
                    </div>
                @endif
            </div>
        </section>
    @else
        <h1 style="font-size:28px; text-align:center;">{{ $mm ? 'အော်ဒါ' : 'Order' }}</h1>
        <div class="rt-notice err" style="max-width:560px; margin:16px auto 0;">{{ $error ?? ($mm ? 'အော်ဒါကို ရှာမတွေ့ပါ။' : 'That order could not be found.') }}</div>
    @endif

    <p style="margin-top:22px; text-align:center;"><a href="{{ url('/store') }}">{{ $mm ? '← ဈေးဆိုင်သို့ ပြန်ရန်' : '← Back to the shop' }}</a></p>
@endsection
