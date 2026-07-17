@extends('theme.doeh-service.layouts.app')
@section('title', 'Home')

@section('content')
    @php
        $mm = app()->getLocale() === 'mm';
        $heroTitle = trim((string) bp_option('sv_hero_title')) ?: ($mm ? 'ဂရုတစိုက် ဝန်ဆောင်မှု၊ ချိန်းဆို၍။' : 'Care, by appointment.');
        $heroSub = trim((string) bp_option('sv_hero_sub')) ?: ($mm ? 'ဝန်ဆောင်မှုရွေးပြီး အချိန်တောင်းဆိုပါ — ကျွန်ုပ်တို့ အတည်ပြုပေးပါမည်။' : "Choose a service and request a time — we'll confirm with you.");
        $products = function_exists('doeh_storefront_products') ? doeh_storefront_products() : [];
        $ready = function_exists('doeh_commerce') && doeh_commerce() !== null;
        $featured = array_slice($products, 0, 4);
        $loyalty = function_exists('bp_apply_filters') ? trim(bp_apply_filters('doeh_loyalty_panel', '')) : '';
    @endphp

    {{-- Hero: a calm promise --}}
    <section style="padding:20px 0 36px; text-align:center;">
        <div class="sv-eyebrow" style="margin-bottom:14px;">{{ $mm ? 'ချိန်းဆို၍' : 'By appointment' }}</div>
        <h1 style="font-size:40px; line-height:1.1; margin-bottom:14px;">{{ $heroTitle }}</h1>
        <p class="sv-muted" style="font-size:18px; max-width:42ch; margin:0 auto 24px;">{{ $heroSub }}</p>
        <a class="sv-btn" href="{{ url('/store') }}">{{ $mm ? 'ဝန်ဆောင်မှုများ ကြည့်ရန်' : 'View services' }}</a>
    </section>

    {{-- Rewards (DOEH Identity) --}}
    @if ($loyalty !== '')
        <section class="sv-card" style="padding:20px 24px; margin-bottom:30px; display:flex; gap:18px; align-items:center; flex-wrap:wrap;">
            <div style="flex:1 1 220px;">
                <div class="sv-eyebrow" style="color:var(--price);">{{ $mm ? 'ဆုမှတ်များ' : 'Rewards' }}</div>
                <div class="sv-serif" style="font-size:20px; margin-top:2px;">{{ $mm ? 'လာရောက်တိုင်း အမှတ်ရယူပါ' : 'Earn points on every visit' }}</div>
            </div>
            <div style="flex:1 1 240px;">{!! $loyalty !!}</div>
        </section>
    @endif

    {{-- Featured services --}}
    @if (! empty($featured))
        <section>
            <div style="display:flex; align-items:baseline; justify-content:space-between; margin-bottom:6px;">
                <h2 style="font-size:24px;">{{ $mm ? 'ဝန်ဆောင်မှုများ' : 'Our services' }}</h2>
                <a href="{{ url('/store') }}">{{ $mm ? 'အားလုံး →' : 'All services →' }}</a>
            </div>
            <div class="sv-card" style="padding:6px 26px;">
                @foreach ($featured as $p)
                    @include('theme.doeh-service.partials.service', ['p' => $p, 'ready' => $ready, 'mm' => $mm])
                @endforeach
            </div>
        </section>
    @endif
@endsection
