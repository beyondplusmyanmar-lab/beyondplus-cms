{{-- Overrides doeh-commerce-storefront::shop — the flow plugin renders this when the
     DOEH Business theme is active, so the shop lives in the theme's own chrome.
     Data (products, cart, ready) comes from the plugin's route. --}}
@extends('theme.doeh-business.layouts.app')
@section('title', 'Shop')

@section('content')
    @php $mm = app()->getLocale() === 'mm'; @endphp
    <h1 style="font-size:26px; margin:0 0 6px;">{{ $mm ? 'ဈေးဆိုင်' : 'Shop' }}</h1>
    <p class="muted" style="margin:0 0 22px;">{{ $mm ? 'ခြင်းထဲထည့်ပြီး မှာယူပါ — DOEH က စျေးနှုန်းတွက်ပေးသည်။' : 'Add to your cart and check out — DOEH prices every order.' }}</p>

    @unless ($ready)
        <div class="notice err">{{ $mm ? 'DOEH Commerce မချိန်ညှိရသေးပါ။' : 'DOEH Commerce is not configured yet.' }}</div>
    @endunless

    @if (empty($products))
        <div class="card muted" style="padding:20px;">{{ $mm ? 'ကုန်ပစ္စည်း မရှိသေးပါ။' : 'No products configured yet.' }}</div>
    @else
        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:16px;">
            @foreach ($products as $p)
                <div class="card" style="padding:18px; display:flex; flex-direction:column; gap:10px;">
                    <div>
                        <div class="serif" style="font-size:18px; font-weight:700;">{{ $p['name'] }}</div>
                        <div class="muted" style="font-size:13px;">{{ $mm ? 'ကုဒ်' : 'SKU' }} {{ $p['sku'] }}</div>
                    </div>
                    @if ($p['price_hint'])<div class="jade" style="font-weight:700;">{{ $p['price_hint'] }}</div>@endif
                    <form method="POST" action="{{ url('/store/cart/add') }}" style="margin-top:auto;">
                        @csrf
                        <input type="hidden" name="sku" value="{{ $p['sku'] }}">
                        <button class="btn" type="submit" style="width:100%;">{{ $mm ? 'ခြင်းထဲ ထည့်ရန်' : 'Add to cart' }}</button>
                    </form>
                </div>
            @endforeach
        </div>
        <p style="margin-top:20px;"><a href="{{ url('/store/cart') }}">{{ $mm ? 'ခြင်းကြည့်ရန် →' : 'View cart →' }}</a></p>
    @endif
@endsection
