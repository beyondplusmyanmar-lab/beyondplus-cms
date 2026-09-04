{{-- Overrides doeh-commerce-storefront::cart. Data: lines, ready. --}}
@extends('theme.doeh-retail.layouts.app')
@section('title', 'Your bag')

@section('content')
    @php
        $mm = app()->getLocale() === 'mm';
        $count = array_sum(array_map(fn ($l) => (int) ($l['qty'] ?? 0), (array) $lines));
    @endphp

    <div class="rt-wrap" style="padding-top:44px;">
        <div class="rt-head-row">
            <h1 class="rt-h2">{{ $mm ? 'သင့်ခြင်း' : 'Your bag' }}</h1>
            @if (! empty($lines))
                <span class="rt-muted rt-small">{{ $count }} {{ $mm ? 'ခု' : ($count === 1 ? 'item' : 'items') }}</span>
            @endif
        </div>
        <hr class="rt-shelf-rule">

        @if (empty($lines))
            <div class="rt-panel" style="padding:48px 24px; text-align:center;">
                <p class="rt-h3" style="margin:0 0 8px;">{{ $mm ? 'ခြင်း ဗလာဖြစ်နေသည်' : 'Your bag is empty' }}</p>
                <p class="rt-muted rt-small" style="margin:0 0 20px;">{{ $mm ? 'ပစ္စည်းရွေးပြီး ဤနေရာတွင် ပြန်ကြည့်ပါ။' : 'Pick something from the shelves and it will show up here.' }}</p>
                <a class="rt-btn" href="{{ url('/store') }}">{{ $mm ? 'ဈေးဆိုင် ကြည့်ရန်' : 'Start shopping' }}</a>
            </div>
        @else
            <div class="rt-two-col">
                <div class="rt-panel" style="padding:4px 22px;">
                    @foreach ($lines as $l)
                        <div style="display:flex; align-items:center; gap:15px; padding:18px 0; {{ ! $loop->last ? 'border-bottom:1px solid var(--rule-soft);' : '' }}">
                            <div aria-hidden="true" style="width:54px; height:54px; flex:0 0 auto; border-radius:13px; display:grid; place-items:center;
                                        background:linear-gradient(168deg, var(--brand-wash), #fff 62%); box-shadow: var(--inset-niche);
                                        font-weight:700; font-size:22px; letter-spacing:-.03em; color:color-mix(in srgb, var(--brand) 30%, #fff);">{{ mb_strtoupper(mb_substr($l['name'], 0, 1)) }}</div>
                            <div style="flex:1 1 auto; min-width:0;">
                                <div style="font-weight:600; letter-spacing:-.012em;">{{ $l['name'] }}</div>
                                <div class="rt-muted rt-micro" style="margin-top:2px;">
                                    <span style="font-variant-numeric:tabular-nums;">{{ $mm ? 'အရေအတွက်' : 'Qty' }} {{ $l['qty'] }}</span>
                                    @if ($l['price_hint'])<span aria-hidden="true"> &nbsp;·&nbsp; </span><span class="rt-money" style="font-weight:600;">{{ $l['price_hint'] }}</span>@endif
                                </div>
                            </div>
                            <form method="POST" action="{{ url('/store/cart/remove') }}">
                                @csrf
                                <input type="hidden" name="sku" value="{{ $l['sku'] }}">
                                <button class="rt-btn ghost sm" type="submit">{{ $mm ? 'ဖယ်ရန်' : 'Remove' }}</button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <form method="POST" action="{{ url('/store/checkout') }}" class="rt-panel" style="padding:24px 26px;">
                    @csrf
                    <h2 class="rt-h3" style="margin-bottom:16px;">{{ $mm ? 'အော်ဒါ အတည်ပြုရန်' : 'Review and order' }}</h2>

                    <label for="phone" class="rt-small" style="display:block; font-weight:600; margin-bottom:6px;">{{ $mm ? 'ဖုန်း' : 'Phone' }}
                        <span class="rt-muted" style="font-weight:400;">{{ $mm ? '(မထည့်လည်းရ)' : '(optional)' }}</span>
                    </label>
                    <input id="phone" name="phone" type="tel" placeholder="+95 9 123 456 78"
                           style="width:100%; padding:12px 14px; border:1px solid var(--rule); border-radius:12px; margin-bottom:14px;
                                  font:inherit; background:var(--paper); color:var(--ink);">
                    <p class="rt-muted rt-small" style="margin:0 0 18px;">{{ $mm ? 'စုစုပေါင်းကို DOEH က checkout တွင် တွက်ပေးသည်။' : 'DOEH works out your total at checkout.' }}</p>

                    <button class="rt-btn block" type="submit" @unless($ready) disabled @endunless>{{ $mm ? 'အော်ဒါ တင်ရန်' : 'Place order' }}</button>
                    @unless ($ready)
                        <p class="rt-muted rt-small" style="margin:14px 0 0; text-align:center;">{{ $mm ? 'DOEH Commerce ချိန်ညှိပြီးမှ အော်ဒါတင်နိုင်သည်။' : 'Ordering opens once DOEH Commerce is configured.' }}</p>
                    @endunless
                </form>
            </div>
            <p style="margin-top:22px;"><a href="{{ url('/store') }}">{{ $mm ? 'ဆက်လက် ဝယ်ယူရန်' : 'Keep shopping' }}</a></p>
        @endif
    </div>
@endsection
