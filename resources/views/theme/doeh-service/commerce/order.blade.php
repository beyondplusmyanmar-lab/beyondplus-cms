{{-- Overrides doeh-commerce-storefront::order — "request received". Data: ok, order, error. --}}
@extends('theme.doeh-service.layouts.app')
@section('title', 'Request received')

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

        <section style="max-width:540px; margin:0 auto;">
            <div style="text-align:center; margin-bottom:20px;">
                <div class="sv-eyebrow" style="color:var(--brand-deep); margin-bottom:12px;">✓ {{ $mm ? 'တောင်းဆိုမှု လက်ခံပြီး' : 'Request received' }}</div>
                <h1 style="font-size:32px;">{{ $mm ? 'ကျေးဇူးတင်ပါသည်' : 'Thank you' }}</h1>
                <p class="sv-muted" style="margin:8px 0 0;">{{ $mm ? 'သင့်တောင်းဆိုမှုကို လက်ခံရရှိပါပြီ — အချိန်ဇယား အတည်ပြုရန် ဆက်သွယ်ပါမည်။' : "We've received your request and will be in touch to confirm your appointment." }}</p>
            </div>

            <div class="sv-card" style="padding:24px 26px;">
                <div style="display:flex; justify-content:space-between; align-items:baseline;">
                    <span class="sv-eyebrow">{{ $mm ? 'တောင်းဆိုမှု' : 'Request' }}</span>
                    <span class="sv-serif" style="font-size:19px;">{{ $order['id'] ?? '—' }}</span>
                </div>
                <div class="sv-muted" style="font-size:14px; margin-top:4px;">{{ $order['status'] ?? 'received' }} · {{ $order['payment_status'] ?? 'unpaid' }}</div>

                @if (! empty($order['lines']))
                    <div style="border-top:1px solid var(--line); margin-top:16px; padding-top:10px;">
                        @foreach ($order['lines'] as $line)
                            <div style="display:flex; justify-content:space-between; gap:12px; padding:8px 0;">
                                <span>{{ $line['name'] ?? $line['sku'] }} <span class="sv-muted">× {{ $line['qty'] }}</span></span>
                                <span class="sv-price">{{ $fmt($line['line_total_minor'] ?? 0) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($grand !== null)
                    <div style="display:flex; justify-content:space-between; align-items:baseline; border-top:2px solid var(--ink); margin-top:12px; padding-top:14px;">
                        <span class="sv-serif" style="font-size:18px;">{{ $mm ? 'ခန့်မှန်း စုစုပေါင်း' : 'Estimated total' }}</span>
                        <span class="sv-price sv-serif" style="font-size:26px;">{{ $grand }} {{ $currency }}</span>
                    </div>
                @endif
            </div>
        </section>
    @else
        <h1 style="font-size:28px; text-align:center;">{{ $mm ? 'တောင်းဆိုမှု' : 'Request' }}</h1>
        <div class="sv-notice err" style="max-width:540px; margin:16px auto 0;">{{ $error ?? ($mm ? 'တောင်းဆိုမှုကို ရှာမတွေ့ပါ။' : 'That request could not be found.') }}</div>
    @endif

    <p style="margin-top:22px; text-align:center;"><a href="{{ url('/store') }}">{{ $mm ? '← ဝန်ဆောင်မှုများသို့ ပြန်ရန်' : '← Back to services' }}</a></p>
@endsection
