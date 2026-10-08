{{-- Overrides doeh-commerce-storefront::order. Data: ok, order, error, fulfillment. --}}
@extends('theme.doeh-mart.layouts.app')
@section('title', 'Order')

@section('content')
    @php $mm = app()->getLocale() === 'mm'; @endphp

    <div class="container pt-3" style="max-width:680px;">
    @if ($ok && $order)
        @php
            $totals = $order['totals'] ?? [];
            $grandMinor = $totals['grand_total_minor'] ?? null;
            $currency = $totals['currency'] ?? '';
            // Currency-aware minor→display: MMK (and other zero-decimal currencies) are whole units.
            $zeroDecimal = ['MMK', 'JPY', 'KRW', 'VND', 'IDR', 'LAK', 'KHR'];
            $exp = in_array(strtoupper((string) $currency), $zeroDecimal, true) ? 0 : 2;
            $fmt = fn ($minor) => number_format($exp === 0 ? (int) $minor : $minor / (10 ** $exp), $exp);
            $grand = $grandMinor === null ? null : $fmt($grandMinor);
            $ftLabel = empty($fulfillment) ? null : ([
                'pickup' => $mm ? 'လာယူမည်' : 'Pickup',
                'delivery' => $mm ? 'အိမ်အရောက် ပို့မည်' : 'Delivery',
                'dine_in' => $mm ? 'ဆိုင်တွင် သုံးဆောင်မည်' : 'Dine in',
            ][$fulfillment] ?? ucfirst(str_replace('_', ' ', $fulfillment)));
        @endphp

        <div class="mt-panel text-center py-4 mb-3">
            <i class="bi bi-check-circle-fill" style="font-size:2.4rem; color:var(--mt-ok);"></i>
            <h1 class="h4 fw-bold mt-2 mb-1">{{ $mm ? 'ကျေးဇူးတင်ပါသည်' : 'Thanks for your order' }}</h1>
            <p class="small mt-muted mb-0">{{ $mm ? 'ဆိုင်တွင် ပြင်ဆင်ပြီးလျှင် အကြောင်းကြားပါမည်။' : (($fulfillment ?? null) === 'delivery' ? 'The shop has it. You will hear from them when it is on its way.' : 'The shop has it. You will hear from them when it is ready to collect.') }}</p>
        </div>

        <div class="mt-panel">
            <dl class="row small mb-0">
                <dt class="col-5 mt-muted fw-normal">{{ $mm ? 'အော်ဒါ နံပါတ်' : 'Order number' }}</dt>
                <dd class="col-7 text-end mt-money mb-2">{{ $order['order_number'] ?? $order['id'] ?? '—' }}</dd>
                <dt class="col-5 mt-muted fw-normal">{{ $mm ? 'အခြေအနေ' : 'Status' }}</dt>
                <dd class="col-7 text-end fw-semibold mb-2">{{ doeh_storefront_status_label('status', $order['status'] ?? 'received') }}</dd>
                <dt class="col-5 mt-muted fw-normal">{{ $mm ? 'ငွေပေးချေမှု' : 'Payment' }}</dt>
                <dd class="col-7 text-end fw-semibold mb-2">{{ doeh_storefront_status_label('payment', $order['payment_status'] ?? 'unpaid') }}</dd>
                @if ($ftLabel)
                    <dt class="col-5 mt-muted fw-normal">{{ $mm ? 'ရယူမည့်ပုံစံ' : 'Fulfilment' }}</dt>
                    <dd class="col-7 text-end fw-semibold mb-2">{{ $ftLabel }}</dd>
                @endif
            </dl>

            @if (! empty($order['lines']))
                <div class="border-top mt-2 pt-2">
                    @foreach ($order['lines'] as $line)
                        <div class="d-flex justify-content-between py-2" style="gap:1rem;">
                            <span>{{ $line['name'] ?? $line['sku'] }} <span class="mt-muted mt-money" style="font-weight:500;">&times;{{ $line['qty'] }}</span></span>
                            <span class="mt-money">{{ $fmt($line['line_total_minor'] ?? 0) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($grand !== null)
                <div class="d-flex justify-content-between align-items-baseline border-top border-2 border-dark mt-2 pt-3" style="gap:1rem;">
                    <span class="fw-bold">{{ $mm ? 'စုစုပေါင်း' : 'Total' }}</span>
                    <span class="mt-money" style="font-size:1.6rem; color:var(--mt-primary);">{{ $grand }} <span class="mt-muted" style="font-size:1rem;">{{ $currency }}</span></span>
                </div>
            @endif
        </div>
    @else
        <div class="mt-panel">
            <h1 class="h5 fw-bold">{{ $mm ? 'အော်ဒါ' : 'Order' }}</h1>
            <div class="alert alert-danger mb-0">{{ $error ?? ($mm ? 'အော်ဒါကို ရှာမတွေ့ပါ။' : 'That order could not be found.') }}</div>
        </div>
    @endif
        <p class="text-center mt-3 mb-0"><a href="{{ url('/store') }}"><i class="bi bi-arrow-left"></i> {{ $mm ? 'ဈေးဆိုင်သို့ ပြန်ရန်' : 'Back to the shop' }}</a></p>
    </div>
@endsection
