{{-- Overrides doeh-commerce-storefront::order. Data: ok, order, error. --}}
@extends('theme.doeh-retail.layouts.app')
@section('title', 'Order confirmed')

@section('content')
    @php $mm = app()->getLocale() === 'mm'; @endphp

    <div class="rt-wrap" style="padding-top:44px;">
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

        <section style="max-width:600px; margin:0 auto;">
            <div style="text-align:center; margin-bottom:26px;">
                <span class="rt-mark is-stock" style="justify-content:center; color:var(--money);"><span class="dot"></span>{{ $mm ? 'အော်ဒါ လက်ခံပြီး' : 'Order confirmed' }}</span>
                <h1 class="rt-h2" style="margin-top:10px;">{{ $mm ? 'ကျေးဇူးတင်ပါသည်' : 'Thanks for your order' }}</h1>
                <p class="rt-muted rt-small" style="margin:8px auto 0; max-width:40ch;">{{ $mm ? 'ဆိုင်တွင် ပြင်ဆင်ပြီးလျှင် အကြောင်းကြားပါမည်။' : 'The shop has it. You will hear from them when it is ready to collect.' }}</p>
            </div>

            {{-- The receipt. Ruled rows, tabular figures, total set apart by a heavy rule. --}}
            <div class="rt-panel" style="padding:24px 26px;">
                <div style="display:flex; justify-content:space-between; align-items:baseline; gap:16px;">
                    <span class="rt-muted rt-small">{{ $mm ? 'အော်ဒါ နံပါတ်' : 'Order number' }}</span>
                    <span class="rt-money" style="font-size:17px;">{{ $order['id'] ?? '—' }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:baseline; gap:16px; margin-top:8px;">
                    <span class="rt-muted rt-small">{{ $mm ? 'အခြေအနေ' : 'Status' }}</span>
                    <span class="rt-small" style="font-weight:600;">{{ $order['status'] ?? 'received' }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:baseline; gap:16px; margin-top:8px;">
                    <span class="rt-muted rt-small">{{ $mm ? 'ငွေပေးချေမှု' : 'Payment' }}</span>
                    <span class="rt-small" style="font-weight:600;">{{ $order['payment_status'] ?? 'unpaid' }}</span>
                </div>

                @if (! empty($order['lines']))
                    <div style="border-top:1px solid var(--rule-soft); margin-top:18px; padding-top:6px;">
                        @foreach ($order['lines'] as $line)
                            <div style="display:flex; justify-content:space-between; gap:14px; padding:9px 0; align-items:baseline;">
                                <span>{{ $line['name'] ?? $line['sku'] }}
                                    <span class="rt-muted" style="font-variant-numeric:tabular-nums;">&times;{{ $line['qty'] }}</span>
                                </span>
                                <span class="rt-money">{{ $fmt($line['line_total_minor'] ?? 0) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($grand !== null)
                    <div style="display:flex; justify-content:space-between; align-items:baseline; gap:14px;
                                border-top:2px solid var(--ink); margin-top:14px; padding-top:16px;">
                        <span style="font-weight:700;">{{ $mm ? 'စုစုပေါင်း' : 'Total' }}</span>
                        <span class="rt-money" style="font-size:27px;">{{ $grand }} <span class="rt-muted" style="font-size:16px; font-weight:600;">{{ $currency }}</span></span>
                    </div>
                @endif
            </div>
        </section>
    @else
        <section style="max-width:600px; margin:0 auto;">
            <h1 class="rt-h2" style="text-align:center;">{{ $mm ? 'အော်ဒါ' : 'Order' }}</h1>
            <div class="rt-notice err" style="margin-top:18px;">{{ $error ?? ($mm ? 'အော်ဒါကို ရှာမတွေ့ပါ။' : 'That order could not be found.') }}</div>
        </section>
    @endif

        <p style="margin-top:26px; text-align:center;"><a href="{{ url('/store') }}">{{ $mm ? 'ဈေးဆိုင်သို့ ပြန်ရန်' : 'Back to the shop' }}</a></p>
    </div>
@endsection
