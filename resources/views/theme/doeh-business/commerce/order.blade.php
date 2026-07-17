{{-- Overrides doeh-commerce-demo::order. Data (ok, order, error) from the plugin route. --}}
@extends('theme.doeh-business.layouts.app')
@section('title', 'Order')

@section('content')
    @php $mm = app()->getLocale() === 'mm'; @endphp
    @if ($ok && $order)
        @php
            $totals = $order['totals'] ?? [];
            $grandMinor = $totals['grand_total_minor'] ?? null;
            $currency = $totals['currency'] ?? '';
            $grand = $grandMinor === null ? null : rtrim(rtrim(number_format($grandMinor / 100, 2), '0'), '.');
        @endphp
        <div class="card" style="padding:32px 30px; margin-bottom:20px; text-align:center;">
            <div class="jade" style="font-size:15px; font-weight:700; letter-spacing:.5px;">✓ {{ $mm ? 'ကျေးဇူးတင်ပါသည်' : 'THANK YOU' }}</div>
            <h1 style="font-size:28px; margin:8px 0 4px;">{{ $mm ? 'မှာယူမှု အတည်ပြုပြီး' : 'Order confirmed' }}</h1>
            <p class="muted" style="margin:0;">{{ $mm ? 'သင့်မှာယူမှုကို DOEH တွင် မှတ်တမ်းတင်ပြီးပါပြီ။' : 'Your order was placed with DOEH.' }}</p>
        </div>

        <div class="card" style="padding:22px 24px;">
            <div style="display:flex; justify-content:space-between; padding:8px 0;">
                <span class="muted">{{ $mm ? 'မှာယူမှု' : 'Order' }}</span>
                <span class="serif" style="font-weight:700;">{{ $order['id'] ?? '—' }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; padding:8px 0; border-top:1px solid var(--line);">
                <span class="muted">{{ $mm ? 'အခြေအနေ' : 'Status' }}</span>
                <span>{{ $order['status'] ?? 'received' }} · {{ $order['payment_status'] ?? 'unpaid' }}</span>
            </div>
            @if (! empty($order['lines']))
                <div style="border-top:1px solid var(--line); margin-top:8px; padding-top:8px;">
                    @foreach ($order['lines'] as $line)
                        <div style="display:flex; justify-content:space-between; padding:6px 0;">
                            <span>{{ $line['name'] ?? $line['sku'] }} <span class="muted">× {{ $line['qty'] }}</span></span>
                            <span>{{ rtrim(rtrim(number_format(($line['line_total_minor'] ?? 0) / 100, 2), '0'), '.') }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
            @if ($grand !== null)
                <div style="display:flex; justify-content:space-between; align-items:baseline; border-top:2px solid var(--line); margin-top:10px; padding-top:14px;">
                    <span style="font-weight:700;">{{ $mm ? 'စုစုပေါင်း' : 'Total' }}</span>
                    <span class="jade serif" style="font-size:26px; font-weight:800;">{{ $grand }} {{ $currency }}</span>
                </div>
            @endif
        </div>
    @else
        <h1 style="font-size:26px;">{{ $mm ? 'မှာယူမှု' : 'Order' }}</h1>
        <div class="notice err">{{ $error ?? ($mm ? 'မှာယူမှုကို ရှာမတွေ့ပါ။' : 'That order could not be found.') }}</div>
    @endif

    <p style="margin-top:18px;"><a href="{{ url('/doeh-demo') }}">{{ $mm ? '← ဈေးဆိုင်သို့ ပြန်သွားရန်' : '← Back to the shop' }}</a></p>
@endsection
