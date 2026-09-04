@extends('theme.doeh-business.layouts.app')
@section('title', 'Home')

@section('content')
    @php
        $mm = app()->getLocale() === 'mm';
        $heroTitle = trim((string) bp_option('biz_hero_title')) ?: ($mm ? 'ကောင်းသောအရာများ၊ လွယ်ကူစွာ မှာယူပါ။' : 'Good things, ordered simply.');
        $heroSub = trim((string) bp_option('biz_hero_subtitle')) ?: ($mm ? 'ကြည့်ရှု၊ ဝင်ရောက်ပြီး မှာယူတိုင်း ဆုမှတ်များ ရယူပါ။' : 'Browse, sign in, and earn rewards on every order.');
        $products = function_exists('doeh_storefront_products') ? doeh_storefront_products() : [];
        $ready = function_exists('doeh_commerce') && doeh_commerce() !== null;
        $featured = array_slice($products, 0, 6);
        $showHero = (bp_option('biz_show_hero', 'yes') ?: 'yes') === 'yes';
        $showLoyalty = (bp_option('biz_show_loyalty', 'yes') ?: 'yes') === 'yes';
        $loyalty = ($showLoyalty && function_exists('bp_apply_filters')) ? trim(bp_apply_filters('doeh_loyalty_panel', '')) : '';

        $readyTime = trim((string) bp_option('biz_ready_time')) ?: ($mm ? 'တစ်နာရီအတွင်း' : 'Usually within the hour');
        $payNote = trim((string) bp_option('biz_payment_note')) ?: ($mm ? 'ကောင်တာတွင် ပေးရန်' : 'Pay when you collect');
        $collectNote = trim((string) bp_option('biz_collect_note')) ?: ($mm ? 'ကောင်တာတွင် အမည်ပြောပါ' : 'Give your name at the counter');
        $showSteps = (bp_option('biz_show_steps', 'yes') ?: 'yes') === 'yes';
        $addr = trim((string) bp_option('biz_addr'));
        $hours = trim((string) bp_option('biz_hours'));
        $phone = trim((string) bp_option('biz_phone'));
        $showVisit = (bp_option('biz_show_visit', 'yes') ?: 'yes') === 'yes' && ($addr !== '' || $hours !== '' || $phone !== '');
    @endphp

    @if ($showHero)
        <section class="band hero">
            <div class="wrap inner reveal">
                <h1 class="display" style="max-width:19ch;">{{ $heroTitle }}</h1>
                <p class="lead" style="margin:18px 0 0;">{{ $heroSub }}</p>
                <div class="cta-row">
                    <a class="btn" href="{{ url('/store') }}">{{ $mm ? 'ဈေးဆိုင်သို့' : 'Shop now' }}</a>
                    @if (! empty($products))
                        <span class="muted small">{{ count($products) }} {{ $mm ? 'ပစ္စည်း ရနိုင်သည်' : 'items available' }}</span>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <div class="wrap" style="padding-top:54px;">

        <section class="section">
            <div class="head-row">
                <h2 class="h2">{{ $mm ? 'ရွေးချယ်စရာများ' : 'Featured' }}</h2>
                @if (! empty($products))<a href="{{ url('/store') }}">{{ $mm ? 'အားလုံး ကြည့်ရန်' : 'See everything' }}</a>@endif
            </div>
            <hr class="rule">
            @if (empty($featured))
                <div class="card" style="padding:40px 24px; text-align:center;">
                    <p class="h3 serif" style="margin:0 0 6px;">{{ $mm ? 'ကုန်ပစ္စည်းများ မကြာမီ' : 'Nothing listed yet' }}</p>
                    <p class="muted small" style="margin:0 auto; max-width:36ch;">{{ $mm ? 'ပစ္စည်းများ ထည့်ပြီးသည်နှင့် ဤနေရာတွင် ပေါ်လာပါမည်။' : 'Products appear here as soon as they are added in DOEH.' }}</p>
                </div>
            @else
                <div class="grid">
                    @foreach ($featured as $p)
                        @include('theme.doeh-business.partials.card', ['p' => $p, 'ready' => $ready, 'mm' => $mm])
                    @endforeach
                </div>
            @endif
        </section>

        @if ($showSteps)
            <section class="section">
                <div class="head-row"><h2 class="h2">{{ $mm ? 'မှာယူပုံ' : 'How ordering works' }}</h2></div>
                <hr class="rule">
                <div class="steps">
                    <div class="step">
                        <h3 class="h3">{{ $mm ? 'အော်ဒါတင်ပါ' : 'Place your order' }}</h3>
                        <p>{{ $mm ? 'ခြင်းထဲ ထည့်ပြီး အော်ဒါတင်လိုက်ပါ။' : 'Add what you need and place the order online.' }}</p>
                    </div>
                    <div class="step">
                        <h3 class="h3">{{ $mm ? 'ကျွန်ုပ်တို့ အတည်ပြုသည်' : 'We confirm it' }}</h3>
                        <p>{{ $readyTime }}</p>
                    </div>
                    <div class="step">
                        <h3 class="h3">{{ $mm ? 'လာယူပါ' : 'Collect and pay' }}</h3>
                        <p>{{ $collectNote }} — {{ $payNote }}</p>
                    </div>
                </div>
            </section>
        @endif

        @if ($loyalty !== '')
            <section class="section">
                <div class="card" style="padding:26px 28px; display:flex; gap:24px; align-items:center; flex-wrap:wrap;">
                    <div style="flex:1 1 260px;">
                        <h2 class="h3 serif">{{ $mm ? 'ကျွန်ုပ်၏ ဆုမှတ်များ' : 'My rewards' }}</h2>
                        <p class="muted small" style="margin:6px 0 0; max-width:42ch;">{{ $mm ? 'မှာယူတိုင်း အမှတ်များ စုဆောင်းပါ။' : 'Points collect on every order you place here.' }}</p>
                    </div>
                    <div style="flex:1 1 260px;">{!! $loyalty !!}</div>
                </div>
            </section>
        @endif

        @if ($showVisit)
            <section class="section">
                <div class="head-row"><h2 class="h2">{{ $mm ? 'ဆိုင်သို့ လာရောက်ရန်' : 'Find us' }}</h2></div>
                <hr class="rule">
                <div class="card" style="padding:8px 24px;">
                    <div class="rows">
                        @if ($addr !== '')<div class="r"><span class="k">{{ $mm ? 'လိပ်စာ' : 'Address' }}</span><span class="v">{{ $addr }}</span></div>@endif
                        @if ($hours !== '')<div class="r"><span class="k">{{ $mm ? 'ဖွင့်ချိန်' : 'Opening hours' }}</span><span class="v">{{ $hours }}</span></div>@endif
                        @if ($phone !== '')<div class="r"><span class="k">{{ $mm ? 'ဖုန်း' : 'Phone' }}</span><span class="v"><a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{{ $phone }}</a></span></div>@endif
                    </div>
                </div>
            </section>
        @endif

    </div>
@endsection
