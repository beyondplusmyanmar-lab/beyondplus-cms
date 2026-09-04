@extends('theme.doeh-restaurant.layouts.app')
@section('title', 'Home')

@section('content')
    @php
        $mm = app()->getLocale() === 'mm';
        $siteName = trim((string) bp_option('r_name')) ?: (optional(site_information('blogname'))->option_value ?: config('app.name'));
        $tagline = trim((string) bp_option('r_tagline')) ?: ($mm ? 'အသစ်ချက်၊ အော်ဒါတင်ရန် အသင့်။' : 'Freshly made, ready to order.');
        $products = function_exists('doeh_storefront_products') ? doeh_storefront_products() : [];
        $ready = function_exists('doeh_commerce') && doeh_commerce() !== null;
        $picks = array_slice($products, 0, 5);
        $showHero = (bp_option('r_show_hero', 'yes') ?: 'yes') === 'yes';
        $showLoyalty = (bp_option('r_show_loyalty', 'yes') ?: 'yes') === 'yes';
        $loyalty = ($showLoyalty && function_exists('bp_apply_filters')) ? trim(bp_apply_filters('doeh_loyalty_panel', '')) : '';
        $menuNote = trim((string) bp_option('r_menu_note')) ?: ($mm ? 'ဈေးနှုန်းများကို checkout တွင် အတည်ပြုပါသည်။' : 'Prices are confirmed at checkout.');

        $readyTime = trim((string) bp_option('r_ready_time')) ?: ($mm ? 'မှာပြီး ၂၀ မိနစ်ခန့်' : 'About 20 minutes from ordering');
        $showSteps = (bp_option('r_show_steps', 'yes') ?: 'yes') === 'yes';
        $addr = trim((string) bp_option('r_addr'));
        $hours = trim((string) bp_option('r_hours'));
        $phone = trim((string) bp_option('r_phone'));
        $showVisit = (bp_option('r_show_visit', 'yes') ?: 'yes') === 'yes' && ($addr !== '' || $hours !== '' || $phone !== '');
    @endphp

    {{-- Masthead: the restaurant leads with itself, not a marketing card. --}}
    @if ($showHero)
        <section class="r-band r-hero">
            <div class="r-wrap inner r-reveal">
                <div class="r-eyebrow">{{ $mm ? 'ယနေ့ မီနူး' : "Today's kitchen" }}</div>
                <h1 class="r-display" style="margin-top:12px;">{{ $siteName }}</h1>
                <p class="r-lead" style="margin:14px auto 0;">{{ $tagline }}</p>
                <div class="cta-row">
                    <a class="r-btn" href="{{ url('/store') }}">{{ $mm ? 'မီနူး ကြည့်ရန်' : 'See the menu' }}</a>
                </div>
            </div>
        </section>
    @endif

    <div class="r-wrap" style="padding-top:50px;">

        {{-- Today's picks: menu leader-dot rows, up top where a menu belongs. --}}
        @if (! empty($picks))
            <section class="r-section">
                <div class="r-head-row">
                    <h2 class="r-h2">{{ $mm ? 'ယနေ့ ရွေးချယ်စရာ' : "Today's picks" }}</h2>
                    <a href="{{ url('/store') }}">{{ $mm ? 'မီနူး အပြည့်အစုံ' : 'The full menu' }}</a>
                </div>
                <hr class="r-rule">
                <div class="r-card" style="padding:4px 26px;">
                    @foreach ($picks as $p)
                        <div class="r-menu-row">
                            <div>
                                <div class="mr-name">{{ $p['name'] }}</div>
                                <div class="mr-sku">{{ $mm ? 'ကုဒ်' : 'No.' }} {{ $p['sku'] }}</div>
                            </div>
                            <span class="mr-dots" aria-hidden="true"></span>
                            @if ($p['price_hint'])<span class="mr-price">{{ $p['price_hint'] }}</span>@endif
                            <form method="POST" action="{{ url('/store/cart/add') }}">
                                @csrf
                                <input type="hidden" name="sku" value="{{ $p['sku'] }}">
                                <button class="r-btn sm" type="submit" @unless($ready) disabled @endunless>{{ $mm ? 'ထည့်ရန်' : 'Add' }}</button>
                            </form>
                        </div>
                    @endforeach
                </div>
                <p class="r-muted r-small" style="margin:12px 2px 0;">{{ $menuNote }}</p>
            </section>
        @endif

        @if ($showSteps)
            <section class="r-section">
                <div class="r-head-row"><h2 class="r-h2">{{ $mm ? 'အော်ဒါတင်ပုံ' : 'How ordering works' }}</h2></div>
                <hr class="r-rule">
                <div class="r-steps" style="margin-top:22px;">
                    <div class="r-step">
                        <h3 class="r-h3">{{ $mm ? 'မီနူးမှ ရွေးပါ' : 'Order from the menu' }}</h3>
                        <p>{{ $mm ? 'လိုချင်သည်များ ထည့်ပြီး အော်ဒါတင်ပါ။' : 'Add what you want and send the order.' }}</p>
                    </div>
                    <div class="r-step">
                        <h3 class="r-h3">{{ $mm ? 'မီးဖိုချောင်မှ ချက်သည်' : 'The kitchen cooks' }}</h3>
                        <p>{{ $readyTime }}</p>
                    </div>
                    <div class="r-step">
                        <h3 class="r-h3">{{ $mm ? 'ယူပါ သို့မဟုတ် ထိုင်စားပါ' : 'Collect or dine in' }}</h3>
                        <p>{{ $mm ? 'ကောင်တာတွင် အမည်ပြောပါ။' : 'Give your name at the counter.' }}</p>
                    </div>
                </div>
            </section>
        @endif

        @if ($loyalty !== '')
            <section class="r-section">
                <div class="r-card" style="padding:24px 26px; display:flex; align-items:center; gap:22px; flex-wrap:wrap;">
                    <div style="flex:1 1 240px;">
                        <h2 class="r-h3 r-serif">{{ $mm ? 'အော်ဒါတိုင်း အမှတ်ရယူပါ' : 'Earn points on every order' }}</h2>
                        <p class="r-muted r-small" style="margin:6px 0 0; max-width:40ch;">{{ $mm ? 'ဆုမှတ်များကို နောက်တစ်ကြိမ် လာချိန်တွင် သုံးနိုင်သည်။' : 'Points build up meal by meal.' }}</p>
                    </div>
                    <div style="flex:1 1 240px;">{!! $loyalty !!}</div>
                </div>
            </section>
        @endif

        @if ($showVisit)
            <section class="r-section">
                <div class="r-head-row"><h2 class="r-h2">{{ $mm ? 'ဆိုင်သို့ လာရောက်ရန်' : 'Find the kitchen' }}</h2></div>
                <hr class="r-rule">
                <div class="r-card" style="padding:6px 26px; margin-top:14px;">
                    <div class="r-rows">
                        @if ($addr !== '')<div class="r"><span class="k">{{ $mm ? 'လိပ်စာ' : 'Address' }}</span><span class="v">{{ $addr }}</span></div>@endif
                        @if ($hours !== '')<div class="r"><span class="k">{{ $mm ? 'ဖွင့်ချိန်' : 'Opening hours' }}</span><span class="v">{{ $hours }}</span></div>@endif
                        @if ($phone !== '')<div class="r"><span class="k">{{ $mm ? 'ဖုန်း' : 'Phone' }}</span><span class="v"><a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{{ $phone }}</a></span></div>@endif
                    </div>
                </div>
            </section>
        @endif

    </div>
@endsection
