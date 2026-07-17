{{-- Overrides doeh-commerce-storefront::shop — the menu card. Data: products, cart, ready. --}}
@extends('theme.doeh-restaurant.layouts.app')
@section('title', 'Menu')

@section('content')
    @php
        $mm = app()->getLocale() === 'mm';
        $note = trim((string) bp_option('r_menu_note')) ?: ($mm ? 'စျေးနှုန်းများကို checkout တွင် အတည်ပြုသည်။' : 'Prices are confirmed at checkout.');
    @endphp

    <header style="text-align:center; margin-bottom:26px;">
        <div class="r-eyebrow" style="margin-bottom:10px;">{{ $mm ? 'ယနေ့ မီနူး' : "Today's menu" }}</div>
        <h1 style="font-size:38px;">{{ $mm ? 'မီနူး' : 'Menu' }}</h1>
        <p class="r-muted" style="margin:8px 0 0;">{{ $note }}</p>
    </header>

    @unless ($ready)
        <div class="r-notice err">{{ $mm ? 'DOEH Commerce မချိန်ညှိရသေးပါ — အော်ဒါ ခဏ ပိတ်ထားသည်။' : 'DOEH Commerce is not configured — ordering is paused.' }}</div>
    @endunless

    @if (empty($products))
        <div class="r-card r-muted" style="padding:24px; text-align:center;">{{ $mm ? 'မီနူး မကြာမီ ရောက်လာမည်။' : 'The menu is coming soon.' }}</div>
    @else
        <div class="r-card" style="padding:6px 26px;">
            @foreach ($products as $p)
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
                        <button class="r-btn sm" type="submit" @unless($ready) disabled @endunless>{{ $mm ? 'ထည့်ရန်' : 'Add' }}</button>
                    </form>
                </div>
            @endforeach
        </div>
        <p style="margin-top:22px; text-align:center;"><a href="{{ url('/store/cart') }}">{{ $mm ? 'အော်ဒါ ကြည့်ရန် →' : 'View your order →' }}</a></p>
    @endif
@endsection
