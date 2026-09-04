@extends('theme.doeh-retail.layouts.app')
@section('title', 'Home')

@section('content')
    @php
        $mm = app()->getLocale() === 'mm';
        $heroTitle = trim((string) bp_option('rt_hero_title')) ?: ($mm ? 'အွန်လိုင်း ဝယ်၊ ဆိုင်တွင် ယူ။' : 'Shop online, pick up in store.');
        $heroSub = trim((string) bp_option('rt_hero_sub')) ?: ($mm ? 'စတော့အစစ်၊ ဈေးအစစ်၊ အသင့်ရှိသည်။' : 'Real stock, real prices, ready when you are.');
        $pickup = (bp_option('rt_pickup', 'yes') ?: 'yes') === 'yes';
        $products = function_exists('doeh_storefront_products') ? doeh_storefront_products() : [];
        $ready = function_exists('doeh_commerce') && doeh_commerce() !== null;
        $featured = array_slice($products, 0, 8);
        $showHero = (bp_option('rt_show_hero', 'yes') ?: 'yes') === 'yes';
        $showLoyalty = (bp_option('rt_show_loyalty', 'yes') ?: 'yes') === 'yes';
        $loyalty = ($showLoyalty && function_exists('bp_apply_filters')) ? trim(bp_apply_filters('doeh_loyalty_panel', '')) : '';

        $pickupTime = trim((string) bp_option('rt_pickup_time')) ?: ($mm ? 'တစ်နာရီအတွင်း' : 'Usually within the hour');
        $paymentNote = trim((string) bp_option('rt_payment_note')) ?: ($mm ? 'ကောင်တာတွင် ပေးရန်' : 'Pay at the counter');
        $pickupWhere = trim((string) bp_option('rt_pickup_where')) ?: ($mm ? 'ကောင်တာတွင် အမည်ပြောပါ' : 'Give your name at the counter');
        $showSteps = (bp_option('rt_show_steps', 'yes') ?: 'yes') === 'yes';
        $addr = trim((string) bp_option('rt_addr'));
        $hours = trim((string) bp_option('rt_hours'));
        $phone = trim((string) bp_option('rt_phone'));
        $showVisit = (bp_option('rt_show_visit', 'yes') ?: 'yes') === 'yes' && ($addr !== '' || $hours !== '' || $phone !== '');
    @endphp

    {{-- Hero: the one loud moment on the page. Full-bleed, so it reads as the shopfront
         rather than another card in the stack. --}}
    @if ($showHero)
        <section class="rt-band rt-hero">
            <div class="rt-wrap inner">
                <div class="rt-reveal">
                    <h1 class="rt-display">{{ $heroTitle }}</h1>
                    <p class="rt-lead" style="margin:18px 0 0;">{{ $heroSub }}</p>
                    <div class="cta-row">
                        <a class="rt-btn" href="{{ url('/store') }}">{{ $mm ? 'ဈေးဆိုင် ကြည့်ရန်' : 'Start shopping' }}</a>
                        @if (! empty($products))
                            <span class="rt-muted rt-small">{{ count($products) }} {{ $mm ? 'ပစ္စည်း ရနိုင်သည်' : 'items available' }}</span>
                        @endif
                    </div>
                </div>

                @if ($pickup)
                    <div class="rt-facts">
                        <div class="fact">
                            <span class="k">{{ $mm ? 'ယူရန် အသင့်' : 'Ready for pickup' }}</span>
                            <span class="v">{{ $pickupTime }}</span>
                        </div>
                        <div class="fact">
                            <span class="k">{{ $mm ? 'ငွေပေးချေမှု' : 'Payment' }}</span>
                            <span class="v">{{ $paymentNote }}</span>
                        </div>
                        @if ($loyalty !== '')
                            <div class="fact">
                                <span class="k">{{ $mm ? 'ဆုမှတ်' : 'Rewards' }}</span>
                                <span class="v" style="color:var(--money);">{{ $mm ? 'ဝယ်တိုင်း အမှတ်ရသည်' : 'Points on every order' }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </section>
    @endif

    <div class="rt-wrap" style="padding-top:56px;">

        {{-- Products come first: this is a shop. --}}
        @if (! empty($featured))
            <section class="rt-section">
                <div class="rt-head-row">
                    <h2 class="rt-h2">{{ $mm ? 'ရွေးချယ်ထားသော ပစ္စည်းများ' : 'Featured' }}</h2>
                    <a href="{{ url('/store') }}">{{ $mm ? 'အားလုံး ကြည့်ရန်' : 'Shop all' }}</a>
                </div>
                <hr class="rt-shelf-rule">
                <div class="rt-grid{{ bp_option('rt_grid', 'comfortable') === 'compact' ? ' rt-compact' : '' }}">
                    @foreach ($featured as $p)
                        @include('theme.doeh-retail.partials.card', ['p' => $p, 'ready' => $ready, 'mm' => $mm, 'pickup' => $pickup])
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Numbered because collecting an order genuinely is a sequence. --}}
        @if ($showSteps && $pickup)
            <section class="rt-section">
                <div class="rt-head-row"><h2 class="rt-h2">{{ $mm ? 'ဆိုင်တွင် ယူပုံ' : 'How pickup works' }}</h2></div>
                <hr class="rt-shelf-rule">
                <div class="rt-steps">
                    <div class="rt-step">
                        <h3 class="rt-h3">{{ $mm ? 'အော်ဒါတင်ပါ' : 'Place your order' }}</h3>
                        <p>{{ $mm ? 'ခြင်းထဲ ထည့်ပြီး အော်ဒါတင်လိုက်ပါ။ ငွေကြိုပေးရန် မလိုပါ။' : 'Add what you need and place the order. Nothing to pay up front.' }}</p>
                    </div>
                    <div class="rt-step">
                        <h3 class="rt-h3">{{ $mm ? 'ကျွန်ုပ်တို့ ပြင်ဆင်ပေးသည်' : 'We pack it' }}</h3>
                        <p>{{ $mm ? 'စတော့ကို စစ်ပြီး သင့်အမည်ဖြင့် သိမ်းထားပါသည်။' : 'We check stock and set it aside under your name.' }}</p>
                    </div>
                    <div class="rt-step">
                        <h3 class="rt-h3">{{ $mm ? 'လာယူပါ' : 'Collect it' }}</h3>
                        <p>{{ $pickupWhere }}</p>
                    </div>
                </div>
            </section>
        @endif

        {{-- Rewards (DOEH Identity). Only renders when the plugin returns a panel. --}}
        @if ($loyalty !== '')
            <section class="rt-section">
                <div class="rt-panel" style="padding:26px 28px; display:flex; gap:24px; align-items:center; flex-wrap:wrap;">
                    <div style="flex:1 1 260px;">
                        <h2 class="rt-h3">{{ $mm ? 'ဝယ်တိုင်း အမှတ် ရယူပါ' : 'Earn points on every order' }}</h2>
                        <p class="rt-muted rt-small" style="margin:6px 0 0; max-width:42ch;">{{ $mm ? 'ဆုမှတ်များကို ဆိုင်တွင်ရော အွန်လိုင်းတွင်ပါ စုနိုင်သည်။' : 'Points collect whether you order here or buy in the shop.' }}</p>
                    </div>
                    <div style="flex:1 1 260px;">{!! $loyalty !!}</div>
                </div>
            </section>
        @endif

        {{-- Visit us — only when the merchant has filled something in. --}}
        @if ($showVisit)
            <section class="rt-section">
                <div class="rt-head-row"><h2 class="rt-h2">{{ $mm ? 'ဆိုင်သို့ လာရောက်ရန်' : 'Visit the shop' }}</h2></div>
                <hr class="rt-shelf-rule">
                <div class="rt-visit">
                    @if ($addr !== '')
                        <div class="cell"><div class="k">{{ $mm ? 'လိပ်စာ' : 'Address' }}</div><div class="v">{{ $addr }}</div></div>
                    @endif
                    @if ($hours !== '')
                        <div class="cell"><div class="k">{{ $mm ? 'ဖွင့်ချိန်' : 'Opening hours' }}</div><div class="v">{{ $hours }}</div></div>
                    @endif
                    @if ($phone !== '')
                        <div class="cell"><div class="k">{{ $mm ? 'ဖုန်း' : 'Phone' }}</div><div class="v"><a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{{ $phone }}</a></div></div>
                    @endif
                </div>
            </section>
        @endif

    </div>
@endsection
