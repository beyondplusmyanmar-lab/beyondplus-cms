@extends('theme.doeh-restaurant.layouts.app')
@section('title', 'Home')

@section('content')
    @php
        $mm = app()->getLocale() === 'mm';
        $siteName = trim((string) bp_option('r_name')) ?: (optional(site_information('blogname'))->option_value ?: config('app.name'));
        $tagline = trim((string) bp_option('r_tagline')) ?: ($mm ? 'အသစ်ချက်၊ အော်ဒါတင်ရန် အသင့်။' : 'Freshly made, ready to order.');
        $products = function_exists('doeh_storefront_products') ? doeh_storefront_products() : [];
        $picks = array_slice($products, 0, 4);
        $loyalty = function_exists('bp_apply_filters') ? trim(bp_apply_filters('doeh_loyalty_panel', '')) : '';
    @endphp

    {{-- Masthead: the restaurant leads with itself, not a marketing card --}}
    <section style="padding:22px 0 34px; text-align:center;">
        <div class="r-eyebrow" style="margin-bottom:12px;">{{ $mm ? 'ယနေ့ မီနူး' : "Today's kitchen" }}</div>
        <h1 style="font-size:44px; line-height:1.05; margin-bottom:12px;">{{ $siteName }}</h1>
        <p class="r-muted" style="font-size:19px; max-width:34ch; margin:0 auto 22px;">{{ $tagline }}</p>
        <a class="r-btn" href="{{ url('/store') }}">{{ $mm ? 'မီနူး ကြည့်ရန်' : 'See the menu' }}</a>
    </section>

    {{-- Rewards (DOEH Identity): a sign-in prompt, or the live balance --}}
    @if ($loyalty !== '')
        <section class="r-card" style="padding:20px 24px; margin-bottom:30px; display:flex; align-items:center; gap:18px; flex-wrap:wrap;">
            <div style="flex:1 1 220px;">
                <div class="r-eyebrow" style="color:var(--saffron);">{{ $mm ? 'ဆုမှတ်များ' : 'Rewards' }}</div>
                <div class="r-serif" style="font-size:20px; margin-top:2px;">{{ $mm ? 'အော်ဒါတိုင်း အမှတ်ရယူပါ' : 'Earn points on every order' }}</div>
            </div>
            <div style="flex:1 1 240px;">{!! $loyalty !!}</div>
        </section>
    @endif

    {{-- Today's picks: the menu leader-dot rows, up top where a menu belongs --}}
    @if (! empty($picks))
        <section>
            <div style="display:flex; align-items:baseline; justify-content:space-between; margin-bottom:6px;">
                <h2 style="font-size:24px;">{{ $mm ? 'ရွေးချယ်စရာများ' : "Today's picks" }}</h2>
                <a href="{{ url('/store') }}">{{ $mm ? 'အားလုံး →' : 'Full menu →' }}</a>
            </div>
            <div class="r-card" style="padding:6px 24px;">
                @foreach ($picks as $p)
                    <div class="r-menu-row">
                        <div>
                            <div class="mr-name">{{ $p['name'] }}</div>
                            <div class="mr-sku">{{ $p['sku'] }}</div>
                        </div>
                        <span class="mr-dots"></span>
                        @if ($p['price_hint'])<span class="mr-price">{{ $p['price_hint'] }}</span>@endif
                        <form method="POST" action="{{ url('/store/cart/add') }}">
                            @csrf
                            <input type="hidden" name="sku" value="{{ $p['sku'] }}">
                            <button class="r-btn sm" type="submit">{{ $mm ? 'ထည့်ရန်' : 'Add' }}</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endsection
