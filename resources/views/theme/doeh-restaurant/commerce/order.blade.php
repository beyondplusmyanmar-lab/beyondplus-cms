{{-- Overrides doeh-commerce-storefront::order — "the kitchen ticket". Data: ok, order, error. --}}
@extends('theme.doeh-restaurant.layouts.app')
@section('title', 'Order received')

@section('content')
    @php $mm = app()->getLocale() === 'mm'; @endphp
    @if ($ok && $order)
        @php
            $totals = $order['totals'] ?? [];
            $grandMinor = $totals['grand_total_minor'] ?? null;
            $currency = $totals['currency'] ?? '';
            // Minor units → display, currency-aware: MMK (and other zero-decimal
            // currencies) are stored as whole units, so dividing by 100 would show
            // 1,500 MMK as "15". Only 2-decimal currencies get the /100.
            $zeroDecimal = ['MMK', 'JPY', 'KRW', 'VND', 'IDR', 'LAK', 'KHR'];
            $exp = in_array(strtoupper((string) $currency), $zeroDecimal, true) ? 0 : 2;
            $fmt = fn ($minor) => number_format($exp === 0 ? (int) $minor : $minor / (10 ** $exp), $exp);
            $grand = $grandMinor === null ? null : $fmt($grandMinor);
        @endphp

        <section style="text-align:center; margin-bottom:22px;">
            <div class="r-eyebrow" style="color:var(--jade); margin-bottom:10px;">✓ {{ $mm ? 'အော်ဒါ လက်ခံပြီး' : 'Order received' }}</div>
            <h1 style="font-size:36px;">{{ $mm ? 'ကျေးဇူးတင်ပါသည်' : 'Thank you' }}</h1>
            <p class="r-muted" style="margin:8px 0 0;">{{ $mm ? 'မီးဖိုချောင်က သင့်အော်ဒါကို လက်ခံရရှိပါပြီ။' : 'The kitchen has your order.' }}</p>
        </section>

        {{-- The ticket --}}
        <div class="r-card" style="padding:24px 26px; max-width:520px; margin:0 auto;">
            <div style="display:flex; justify-content:space-between; align-items:baseline;">
                <span class="r-eyebrow">{{ $mm ? 'အော်ဒါ' : 'Order' }}</span>
                <span class="r-serif" style="font-size:20px;">{{ $order['id'] ?? '—' }}</span>
            </div>
            <div class="r-muted" style="font-size:14px; margin-top:4px;">{{ $order['status'] ?? 'received' }} · {{ $order['payment_status'] ?? 'unpaid' }}</div>
            @if (! empty($fulfillment))
                @php
                    $ftLabel = [
                        'pickup'   => $mm ? 'လာယူမည်' : 'Pickup',
                        'dine_in'  => $mm ? 'ဆိုင်တွင် သုံးဆောင်မည်' : 'Dine in',
                        'delivery' => $mm ? 'အိမ်အရောက် ပို့မည်' : 'Delivery',
                    ][$fulfillment] ?? ucfirst($fulfillment);
                @endphp
                <div style="font-size:14px; margin-top:8px;"><span class="r-eyebrow" style="font-size:11px;">{{ $mm ? 'ရယူမည့်ပုံစံ' : 'Fulfilment' }}</span> · {{ $ftLabel }}</div>
            @endif

            @if (! empty($order['lines']))
                <div style="border-top:1px dashed var(--line); margin-top:16px; padding-top:8px;">
                    @foreach ($order['lines'] as $line)
                        <div style="display:flex; justify-content:space-between; gap:12px; padding:7px 0;">
                            <span>{{ $line['name'] ?? $line['sku'] }} <span class="r-muted">× {{ $line['qty'] }}</span></span>
                            <span class="r-money">{{ $fmt($line['line_total_minor'] ?? 0) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($grand !== null)
                <div style="display:flex; justify-content:space-between; align-items:baseline; border-top:2px solid var(--ink); margin-top:12px; padding-top:14px;">
                    <span class="r-serif" style="font-size:18px;">{{ $mm ? 'စုစုပေါင်း' : 'Total' }}</span>
                    <span class="r-jade r-serif" style="font-size:28px;">{{ $grand }} {{ $currency }}</span>
                </div>
            @endif
        </div>
    @else
        <h1 style="font-size:30px; text-align:center;">{{ $mm ? 'အော်ဒါ' : 'Order' }}</h1>
        <div class="r-notice err" style="max-width:520px; margin:18px auto 0;">{{ $error ?? ($mm ? 'အော်ဒါကို ရှာမတွေ့ပါ။' : 'That order could not be found.') }}</div>
    @endif

    <p style="margin-top:22px; text-align:center;"><a href="{{ url('/store') }}">{{ $mm ? '← မီနူးသို့ ပြန်ရန်' : '← Back to the menu' }}</a></p>
@endsection
