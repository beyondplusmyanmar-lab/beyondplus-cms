{{-- Overrides doeh-commerce-storefront::order. Data (ok, order, error) from the plugin route. --}}
@extends('theme.doeh-business.layouts.app')
@section('title', 'Order')

@section('content')
    @php $mm = app()->getLocale() === 'mm'; @endphp
    <div class="wrap" style="padding-top:42px;">
    @if ($ok && $order)
        @php
            $totals = $order['totals'] ?? [];
            $grandMinor = $totals['grand_total_minor'] ?? null;
            $currency = $totals['currency'] ?? '';
            // Currency-aware minor→display: MMK is zero-decimal, so no /100 (else
            // 1,500 MMK shows as "15"). Only 2-decimal currencies divide.
            $zeroDecimal = ['MMK', 'JPY', 'KRW', 'VND', 'IDR', 'LAK', 'KHR'];
            $exp = in_array(strtoupper((string) $currency), $zeroDecimal, true) ? 0 : 2;
            $fmt = fn ($minor) => number_format($exp === 0 ? (int) $minor : $minor / (10 ** $exp), $exp);
            $grand = $grandMinor === null ? null : $fmt($grandMinor);
        @endphp

        <section style="max-width:600px; margin:0 auto;">
            <div style="text-align:center; margin-bottom:26px;">
                <span class="mark is-jade jade"><span class="dot"></span>{{ $mm ? 'မှာယူမှု အတည်ပြုပြီး' : 'Order confirmed' }}</span>
                <h1 class="h2" style="margin-top:10px;">{{ $mm ? 'ကျေးဇူးတင်ပါသည်' : 'Thanks for your order' }}</h1>
                <p class="muted small" style="margin:8px auto 0; max-width:40ch;">{{ $mm ? 'အသင့်ဖြစ်လျှင် အကြောင်းကြားပါမည်။' : 'The shop has it, and will let you know when it is ready.' }}</p>
            </div>

            <div class="card" style="padding:22px 26px;">
                <div class="rows">
                    <div class="r"><span class="k">{{ $mm ? 'မှာယူမှု နံပါတ်' : 'Order number' }}</span><span class="v money">{{ $order['id'] ?? '—' }}</span></div>
                    <div class="r"><span class="k">{{ $mm ? 'အခြေအနေ' : 'Status' }}</span><span class="v">{{ $order['status'] ?? 'received' }}</span></div>
                    <div class="r"><span class="k">{{ $mm ? 'ငွေပေးချေမှု' : 'Payment' }}</span><span class="v">{{ $order['payment_status'] ?? 'unpaid' }}</span></div>
                </div>

                @if (! empty($order['lines']))
                    <div style="border-top:1px solid var(--rule-soft); margin-top:14px; padding-top:8px;">
                        @foreach ($order['lines'] as $line)
                            <div style="display:flex; justify-content:space-between; gap:14px; padding:8px 0; align-items:baseline;">
                                <span>{{ $line['name'] ?? $line['sku'] }} <span class="muted" style="font-variant-numeric:tabular-nums;">&times;{{ $line['qty'] }}</span></span>
                                <span class="money">{{ $fmt($line['line_total_minor'] ?? 0) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($grand !== null)
                    <div style="display:flex; justify-content:space-between; align-items:baseline; gap:14px; border-top:2px solid var(--ink); margin-top:14px; padding-top:16px;">
                        <span class="serif" style="font-weight:700; font-size:18px;">{{ $mm ? 'စုစုပေါင်း' : 'Total' }}</span>
                        <span class="money" style="font-size:27px;">{{ $grand }} <span class="muted" style="font-size:16px;">{{ $currency }}</span></span>
                    </div>
                @endif
            </div>
        </section>
    @else
        <section style="max-width:600px; margin:0 auto;">
            <h1 class="h2" style="text-align:center;">{{ $mm ? 'မှာယူမှု' : 'Order' }}</h1>
            <div class="notice err" style="margin-top:18px;">{{ $error ?? ($mm ? 'မှာယူမှုကို ရှာမတွေ့ပါ။' : 'That order could not be found.') }}</div>
        </section>
    @endif
        <p style="margin-top:26px; text-align:center;"><a href="{{ url('/store') }}">{{ $mm ? 'ဈေးဆိုင်သို့ ပြန်သွားရန်' : 'Back to the shop' }}</a></p>
    </div>
@endsection
