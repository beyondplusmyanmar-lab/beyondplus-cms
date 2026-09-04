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
        $showHero = (bp_option('sv_show_hero', 'yes') ?: 'yes') === 'yes';
        $showLoyalty = (bp_option('sv_show_loyalty', 'yes') ?: 'yes') === 'yes';
        $loyalty = ($showLoyalty && function_exists('bp_apply_filters')) ? trim(bp_apply_filters('doeh_loyalty_panel', '')) : '';
        $note = trim((string) bp_option('sv_note')) ?: ($mm ? 'ဈေးနှုန်းများကို ချိန်းဆိုချိန်တွင် အတည်ပြုပါသည်။' : 'Prices are confirmed at booking.');

        $confirmTime = trim((string) bp_option('sv_confirm_time')) ?: ($mm ? 'တစ်ရက်အတွင်း အတည်ပြုပါမည်' : 'We confirm within a day');
        $showSteps = (bp_option('sv_show_steps', 'yes') ?: 'yes') === 'yes';
        $addr = trim((string) bp_option('sv_addr'));
        $hours = trim((string) bp_option('sv_hours'));
        $phone = trim((string) bp_option('sv_phone'));
        $showVisit = (bp_option('sv_show_visit', 'yes') ?: 'yes') === 'yes' && ($addr !== '' || $hours !== '' || $phone !== '');
    @endphp

    @if ($showHero)
        <section class="sv-band sv-hero">
            <div class="sv-wrap inner sv-reveal">
                <div class="sv-eyebrow">{{ $mm ? 'ချိန်းဆို၍ ဝန်ဆောင်သည်' : 'By appointment' }}</div>
                <h1 class="sv-display" style="margin-top:12px;">{{ $heroTitle }}</h1>
                <p class="sv-lead" style="margin:16px auto 0;">{{ $heroSub }}</p>
                <div class="cta-row">
                    <a class="sv-btn" href="{{ url('/store') }}">{{ $mm ? 'ဝန်ဆောင်မှုများ ကြည့်ရန်' : 'View services' }}</a>
                </div>
            </div>
        </section>
    @endif

    <div class="sv-wrap" style="padding-top:52px;">

        @if (! empty($featured))
            <section class="sv-section">
                <div class="sv-head-row">
                    <h2 class="sv-h2">{{ $mm ? 'ဝန်ဆောင်မှုများ' : 'Our services' }}</h2>
                    <a href="{{ url('/store') }}">{{ $mm ? 'အားလုံး ကြည့်ရန်' : 'All services' }}</a>
                </div>
                <hr class="sv-rule">
                <div class="sv-card" style="padding:2px 26px;">
                    @foreach ($featured as $p)
                        @include('theme.doeh-service.partials.service', ['p' => $p, 'ready' => $ready, 'mm' => $mm])
                    @endforeach
                </div>
                <p class="sv-muted sv-small" style="margin:12px 2px 0;">{{ $note }}</p>
            </section>
        @endif

        @if ($showSteps)
            <section class="sv-section">
                <div class="sv-head-row"><h2 class="sv-h2">{{ $mm ? 'ချိန်းဆိုပုံ' : 'How booking works' }}</h2></div>
                <hr class="sv-rule">
                <div class="sv-steps" style="margin-top:22px;">
                    <div class="sv-step">
                        <h3 class="sv-h3">{{ $mm ? 'ဝန်ဆောင်မှု ရွေးပါ' : 'Choose a service' }}</h3>
                        <p>{{ $mm ? 'လိုအပ်သည့် ဝန်ဆောင်မှုကို ရွေးပြီး တောင်းဆိုပါ။' : 'Pick what you need and send the request.' }}</p>
                    </div>
                    <div class="sv-step">
                        <h3 class="sv-h3">{{ $mm ? 'ကျွန်ုပ်တို့ ဆက်သွယ်သည်' : 'We get in touch' }}</h3>
                        <p>{{ $confirmTime }}</p>
                    </div>
                    <div class="sv-step">
                        <h3 class="sv-h3">{{ $mm ? 'လာရောက်ပါ' : 'Come in' }}</h3>
                        <p>{{ $mm ? 'သတ်မှတ်ချိန်တွင် လာရောက်ပါ။ ငွေကို ပြီးမှ ပေးပါ။' : 'Arrive at the agreed time. Payment happens afterwards.' }}</p>
                    </div>
                </div>
            </section>
        @endif

        @if ($loyalty !== '')
            <section class="sv-section">
                <div class="sv-card" style="padding:24px 26px; display:flex; gap:22px; align-items:center; flex-wrap:wrap;">
                    <div style="flex:1 1 240px;">
                        <h2 class="sv-h3">{{ $mm ? 'လာရောက်တိုင်း အမှတ်ရယူပါ' : 'Earn points on every visit' }}</h2>
                        <p class="sv-muted sv-small" style="margin:6px 0 0; max-width:40ch;">{{ $mm ? 'ဆုမှတ်များကို နောက်တစ်ကြိမ် လာချိန်တွင် သုံးနိုင်သည်။' : 'Points build up visit by visit.' }}</p>
                    </div>
                    <div style="flex:1 1 240px;">{!! $loyalty !!}</div>
                </div>
            </section>
        @endif

        @if ($showVisit)
            <section class="sv-section">
                <div class="sv-head-row"><h2 class="sv-h2">{{ $mm ? 'ကျွန်ုပ်တို့ဆီ လာရောက်ရန်' : 'Where to find us' }}</h2></div>
                <hr class="sv-rule">
                <div class="sv-card" style="padding:6px 26px; margin-top:14px;">
                    <div class="sv-rows">
                        @if ($addr !== '')<div class="r"><span class="k">{{ $mm ? 'လိပ်စာ' : 'Address' }}</span><span class="v">{{ $addr }}</span></div>@endif
                        @if ($hours !== '')<div class="r"><span class="k">{{ $mm ? 'ဖွင့်ချိန်' : 'Opening hours' }}</span><span class="v">{{ $hours }}</span></div>@endif
                        @if ($phone !== '')<div class="r"><span class="k">{{ $mm ? 'ဖုန်း' : 'Phone' }}</span><span class="v"><a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{{ $phone }}</a></span></div>@endif
                    </div>
                </div>
            </section>
        @endif

    </div>
@endsection
