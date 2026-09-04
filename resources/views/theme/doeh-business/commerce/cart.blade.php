{{-- Overrides doeh-commerce-storefront::cart. Data (lines, ready) from the plugin route. --}}
@extends('theme.doeh-business.layouts.app')
@section('title', 'Cart')

@section('content')
    @php
        $mm = app()->getLocale() === 'mm';
        $count = array_sum(array_map(fn ($l) => (int) ($l['qty'] ?? 0), (array) $lines));
    @endphp
    <div class="wrap" style="padding-top:42px;">
        <div class="head-row">
            <h1 class="h2">{{ $mm ? 'ခြင်း' : 'Your cart' }}</h1>
            @if (! empty($lines))<span class="muted small">{{ $count }} {{ $mm ? 'ခု' : ($count === 1 ? 'item' : 'items') }}</span>@endif
        </div>
        <hr class="rule">

        @if (empty($lines))
            <div class="card" style="padding:44px 24px; text-align:center;">
                <p class="h3 serif" style="margin:0 0 8px;">{{ $mm ? 'ခြင်း ဗလာဖြစ်နေသည်' : 'Your cart is empty' }}</p>
                <p class="muted small" style="margin:0 0 20px;">{{ $mm ? 'ပစ္စည်းရွေးပြီး ဤနေရာတွင် ပြန်ကြည့်ပါ။' : 'Pick something out and it will show up here.' }}</p>
                <a class="btn" href="{{ url('/store') }}">{{ $mm ? 'ဈေးဆိုင်သို့' : 'Shop now' }}</a>
            </div>
        @else
            <div class="two-col">
                <div class="card" style="padding:6px 22px;">
                    @foreach ($lines as $l)
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:14px; padding:16px 0; {{ ! $loop->last ? 'border-bottom:1px solid var(--rule-soft);' : '' }}">
                            <div>
                                <div class="serif" style="font-size:17px; font-weight:600;">{{ $l['name'] }}</div>
                                <div class="muted small" style="margin-top:2px; font-variant-numeric:tabular-nums;">
                                    {{ $mm ? 'ကုဒ်' : 'SKU' }} {{ $l['sku'] }} &nbsp;&middot;&nbsp; {{ $mm ? 'အရေအတွက်' : 'qty' }} {{ $l['qty'] }}@if($l['price_hint']) &nbsp;&middot;&nbsp; <span class="money" style="font-weight:600;">{{ $l['price_hint'] }}</span>@endif
                                </div>
                            </div>
                            <form method="POST" action="{{ url('/store/cart/remove') }}">
                                @csrf
                                <input type="hidden" name="sku" value="{{ $l['sku'] }}">
                                <button class="btn sec" type="submit">{{ $mm ? 'ဖယ်ရှားရန်' : 'Remove' }}</button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <form method="POST" action="{{ url('/store/checkout') }}" class="card" style="padding:24px;">
                    @csrf
                    <h2 class="h3 serif" style="margin-bottom:16px;">{{ $mm ? 'အော်ဒါ အတည်ပြုရန်' : 'Review and order' }}</h2>
                    <label for="phone" class="small" style="display:block; font-weight:600; margin-bottom:6px;">{{ $mm ? 'ဖုန်းနံပါတ်' : 'Phone' }}
                        <span class="muted" style="font-weight:400;">{{ $mm ? '(မထည့်လည်းရ)' : '(optional)' }}</span>
                    </label>
                    <input id="phone" name="phone" type="tel" placeholder="+95 9 123 456 78"
                           style="width:100%; padding:12px 14px; border:1px solid var(--line); border-radius:11px; margin-bottom:14px; font:inherit; background:var(--bg); color:var(--ink);">
                    <p class="muted small" style="margin:0 0 18px;">{{ $mm ? 'စုစုပေါင်းကို DOEH က checkout တွင် တွက်ပေးသည်။' : 'DOEH works out the real total at checkout.' }}</p>
                    <button class="btn big" type="submit" @unless($ready) disabled @endunless>{{ $mm ? 'မှာယူရန်' : 'Place order' }}</button>
                    @unless ($ready)<p class="muted small" style="margin:14px 0 0; text-align:center;">{{ $mm ? 'DOEH Commerce ချိန်ညှိပြီးမှ မှာယူနိုင်သည်။' : 'Ordering opens once DOEH Commerce is configured.' }}</p>@endunless
                </form>
            </div>
            <p style="margin-top:22px;"><a href="{{ url('/store') }}">{{ $mm ? 'ဆက်လက် ကြည့်ရှုရန်' : 'Keep shopping' }}</a></p>
        @endif
    </div>
@endsection
