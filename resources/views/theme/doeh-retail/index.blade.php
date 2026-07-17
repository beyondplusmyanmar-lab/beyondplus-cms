@extends('theme.doeh-retail.layouts.app')
@section('title', 'Home')

@section('content')
    @php
        $mm = app()->getLocale() === 'mm';
        $heroTitle = trim((string) bp_option('rt_hero_title')) ?: ($mm ? 'အွန်လိုင်း ဝယ်၊ ဆိုင်တွင် ယူ။' : 'Shop online, pick up in store.');
        $heroSub = trim((string) bp_option('rt_hero_sub')) ?: ($mm ? 'စတော့အစစ်၊ စျေးအစစ်၊ အသင့်ရှိသည်။' : 'Real stock, real prices, ready when you are.');
        $pickup = (bp_option('rt_pickup', 'yes') ?: 'yes') === 'yes';
        $products = function_exists('doeh_storefront_products') ? doeh_storefront_products() : [];
        $ready = function_exists('doeh_commerce') && doeh_commerce() !== null;
        $featured = array_slice($products, 0, 8);
        $loyalty = function_exists('bp_apply_filters') ? trim(bp_apply_filters('doeh_loyalty_panel', '')) : '';
    @endphp

    {{-- Hero: a confident retail promise, no image needed --}}
    <section class="rt-card" style="padding:44px 40px; margin-bottom:28px; display:flex; flex-wrap:wrap; gap:20px; align-items:center; background:linear-gradient(135deg, var(--brand-tint), #fff);">
        <div style="flex:1 1 320px;">
            <h1 style="font-size:38px; line-height:1.05; margin-bottom:12px;">{{ $heroTitle }}</h1>
            <p class="rt-muted" style="font-size:18px; margin:0 0 22px; max-width:44ch;">{{ $heroSub }}</p>
            <a class="rt-btn" href="{{ url('/store') }}">{{ $mm ? 'ဈေးဆိုင်သို့' : 'Start shopping' }}</a>
        </div>
        @if ($pickup)
            <div style="flex:0 0 auto; display:flex; gap:8px; flex-wrap:wrap;">
                <span class="rt-chip pickup">{{ $mm ? 'ဆိုင်တွင် ယူ' : 'Store pickup' }}</span>
                <span class="rt-chip stock">{{ $mm ? 'စတော့ရှိ' : 'In stock' }}</span>
            </div>
        @endif
    </section>

    {{-- Rewards (DOEH Identity) --}}
    @if ($loyalty !== '')
        <section class="rt-card" style="padding:20px 24px; margin-bottom:28px; display:flex; gap:18px; align-items:center; flex-wrap:wrap;">
            <div style="flex:1 1 220px;">
                <div class="rt-muted" style="font-size:12px; font-weight:600; letter-spacing:.06em; text-transform:uppercase; color:var(--money);">{{ $mm ? 'ဆုမှတ်များ' : 'Rewards' }}</div>
                <div class="rt-display" style="font-size:19px; margin-top:2px;">{{ $mm ? 'ဝယ်တိုင်း အမှတ်ရယူပါ' : 'Earn points on every order' }}</div>
            </div>
            <div style="flex:1 1 240px;">{!! $loyalty !!}</div>
        </section>
    @endif

    {{-- Featured products --}}
    @if (! empty($featured))
        <section>
            <div style="display:flex; align-items:baseline; justify-content:space-between; margin-bottom:14px;">
                <h2 style="font-size:22px;">{{ $mm ? 'ရွေးချယ်ထားသော' : 'Featured' }}</h2>
                <a href="{{ url('/store') }}">{{ $mm ? 'အားလုံး →' : 'Shop all →' }}</a>
            </div>
            <div class="rt-grid">
                @foreach ($featured as $p)
                    @include('theme.doeh-retail.partials.card', ['p' => $p, 'ready' => $ready, 'mm' => $mm, 'pickup' => $pickup])
                @endforeach
            </div>
        </section>
    @endif
@endsection
