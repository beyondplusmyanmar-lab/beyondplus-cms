@extends('theme.doeh-business.layouts.app')
@section('title', 'Home')

@section('content')
    @php
        $mm = app()->getLocale() === 'mm';
        $heroTitle = trim((string) bp_option('biz_hero_title')) ?: ($mm ? 'ကောင်းသောအရာများ၊ လွယ်ကူစွာ မှာယူပါ။' : 'Good things, ordered simply.');
        $heroSub = trim((string) bp_option('biz_hero_subtitle')) ?: ($mm ? 'ကြည့်ရှု၊ ဝင်ရောက်ပြီး မှာယူတိုင်း ဆုမှတ်များ ရယူပါ။' : 'Browse, sign in, and earn rewards on every order.');
        $products = function_exists('doeh_demo_products') ? doeh_demo_products() : [];
        $loyalty = function_exists('bp_apply_filters') ? trim(bp_apply_filters('doeh_loyalty_panel', '')) : '';
    @endphp

    {{-- Hero --}}
    <section class="card" style="padding:44px 40px; margin-bottom:28px; background:linear-gradient(180deg,#fff, #fdfaf5);">
        <h1 style="font-size:34px; margin:0 0 10px; max-width:22ch;">{{ $heroTitle }}</h1>
        <p class="muted" style="font-size:18px; margin:0 0 22px; max-width:48ch;">{{ $heroSub }}</p>
        <a class="btn" href="{{ url('/doeh-demo') }}">{{ $mm ? 'ဈေးဆိုင်သို့' : 'Shop now' }}</a>
    </section>

    {{-- Loyalty (DOEH Identity) — renders a sign-in prompt or the live balance --}}
    @if ($loyalty !== '')
        <section style="margin-bottom:28px;">
            <h2 style="font-size:20px; margin:0 0 12px;">{{ $mm ? 'ကျွန်ုပ်၏ ဆုမှတ်များ' : 'My rewards' }}</h2>
            <div class="card" style="padding:20px;">{!! $loyalty !!}</div>
        </section>
    @endif

    {{-- Featured products (DOEH Commerce fixture) --}}
    <section>
        <h2 style="font-size:20px; margin:0 0 12px;">{{ $mm ? 'ရွေးချယ်စရာများ' : 'Featured' }}</h2>
        @if (empty($products))
            <div class="card muted" style="padding:20px;">{{ $mm ? 'ကုန်ပစ္စည်းများ မကြာမီ။' : 'Products coming soon.' }}</div>
        @else
            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:16px;">
                @foreach ($products as $p)
                    <div class="card" style="padding:18px; display:flex; flex-direction:column; gap:10px;">
                        <div>
                            <div class="serif" style="font-size:18px; font-weight:700;">{{ $p['name'] }}</div>
                            <div class="muted" style="font-size:13px;">{{ $mm ? 'ကုဒ်' : 'SKU' }} {{ $p['sku'] }}</div>
                        </div>
                        @if ($p['price_hint'])<div class="jade" style="font-weight:700;">{{ $p['price_hint'] }}</div>@endif
                        <form method="POST" action="{{ url('/doeh-demo/cart/add') }}" style="margin-top:auto;">
                            @csrf
                            <input type="hidden" name="sku" value="{{ $p['sku'] }}">
                            <button class="btn sec" type="submit" style="width:100%;">{{ $mm ? 'ခြင်းထဲ ထည့်ရန်' : 'Add to cart' }}</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endsection
