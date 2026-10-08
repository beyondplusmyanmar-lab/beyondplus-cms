@extends('theme.doeh-mart.layouts.app')
@section('title', 'Home')

@section('content')
    @php
        $mm = app()->getLocale() === 'mm';
        $siteName = trim((string) bp_option('mt_name')) ?: (optional(site_information('blogname'))->option_value ?: config('app.name'));
        $heroTitle = trim((string) bp_option('mt_hero_title')) ?: $siteName;
        $heroSub = trim((string) bp_option('mt_hero_sub')) ?: ($mm ? 'အရည်အသွေးမြင့် ကုန်ပစ္စည်းများ — အွန်လိုင်း မှာယူနိုင်ပါပြီ။' : 'Quality products, ready when you are.');
        $heroCta = trim((string) bp_option('mt_hero_cta')) ?: ($mm ? 'ဈေးဝယ်ရန်' : 'Shop now');
        $types = function_exists('doeh_storefront_fulfillment_types') ? doeh_storefront_fulfillment_types() : ['pickup'];
        $pickup = in_array('pickup', $types, true);
        $delivery = in_array('delivery', $types, true);
        $products = function_exists('doeh_storefront_products') ? doeh_storefront_products() : [];
        $ready = function_exists('doeh_commerce') && doeh_commerce() !== null;
        $featured = array_slice($products, 0, 12);

        $cats = json_decode((string) bp_option('mt_categories_json', ''), true);
        if (! is_array($cats)) {
            $cats = [
                ['icon' => 'bi-phone', 'name' => 'Electronics', 'q' => ''],
                ['icon' => 'bi-bag', 'name' => 'Fashion', 'q' => ''],
                ['icon' => 'bi-house', 'name' => 'Home', 'q' => ''],
                ['icon' => 'bi-heart', 'name' => 'Beauty', 'q' => ''],
                ['icon' => 'bi-cup-hot', 'name' => 'Grocery', 'q' => ''],
                ['icon' => 'bi-controller', 'name' => 'Toys', 'q' => ''],
            ];
        }
        $cats = array_values(array_filter(array_map(fn ($c) => (array) $c, $cats), fn ($c) => trim((string) ($c['name'] ?? '')) !== ''));
        $showCats = (bp_option('mt_show_categories', 'yes') ?: 'yes') === 'yes' && count($cats) > 0;
        $showServices = (bp_option('mt_show_services', 'yes') ?: 'yes') === 'yes';
        $payNote = trim((string) bp_option('mt_payment_note')) ?: ($mm ? 'ယူချိန်တွင် ငွေပေးရန်' : 'Pay when you collect');
    @endphp

    {{-- Banner --}}
    <section class="mt-section pt-3">
        <div class="container">
            <div class="mt-hero">
                <div class="inner">
                    <h1>{{ $heroTitle }}</h1>
                    <p>{{ $heroSub }}</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ url('/store') }}" class="btn btn-light">{{ $heroCta }} <i class="bi bi-arrow-right"></i></a>
                        <a href="{{ url('/store/cart') }}" class="btn btn-ghost"><i class="bi bi-cart3"></i> {{ $mm ? 'ခြင်း' : 'Cart' }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Category shortcuts: each opens the shop filtered by its word. --}}
    @if ($showCats)
        <section class="mt-section pt-0">
            <div class="container">
                <div class="mt-panel">
                    <h2 class="mt-panel-title mb-3">{{ $mm ? 'အမျိုးအစားများ' : 'Categories' }}</h2>
                    <div class="row g-2 row-cols-3 row-cols-md-6">
                        @foreach ($cats as $c)
                            @php $word = trim((string) ($c['q'] ?? '')) ?: trim((string) $c['name']); @endphp
                            <div class="col">
                                <a class="mt-cat" href="{{ url('/store').'?q='.urlencode($word) }}">
                                    <i class="bi {{ preg_replace('/[^a-z0-9-]/', '', (string) ($c['icon'] ?? 'bi-tag')) ?: 'bi-tag' }}"></i>
                                    <span>{{ $c['name'] }}</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- What ordering here means, in four facts. Delivery only when the shop offers it. --}}
    @if ($showServices)
        <section class="mt-section pt-0">
            <div class="container">
                <div class="mt-panel">
                    <div class="mt-services">
                        @if ($pickup)
                            <div class="mt-service"><i class="bi bi-shop"></i><span>{{ $mm ? 'ဆိုင်တွင် လာယူနိုင်' : 'Pick up at the shop' }}</span></div>
                        @endif
                        @if ($delivery)
                            <div class="mt-service"><i class="bi bi-truck"></i><span>{{ $mm ? 'အိမ်အရောက် ပို့ဆောင်' : 'Delivered to your door' }}</span></div>
                        @endif
                        <div class="mt-service"><i class="bi bi-cash-coin"></i><span>{{ $payNote }}</span></div>
                        <div class="mt-service"><i class="bi bi-receipt"></i><span>{{ $mm ? 'ဆိုင်မှ အတည်ပြုပေးသည်' : 'Confirmed by the shop' }}</span></div>
                        @unless ($delivery)
                            <div class="mt-service"><i class="bi bi-patch-check"></i><span>{{ $mm ? 'စတော့ အစစ်၊ ဈေး အစစ်' : 'Real stock, real prices' }}</span></div>
                        @endunless
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Products --}}
    <section class="mt-section pt-0">
        <div class="container">
            <div class="mt-panel">
                <div class="d-flex align-items-center justify-content-between mb-3" style="gap:1rem;">
                    <h2 class="mt-panel-title hot"><i class="bi bi-fire"></i> {{ $mm ? 'အထူးရွေးချယ် ကုန်ပစ္စည်းများ' : 'Featured products' }}</h2>
                    <a href="{{ url('/store') }}" class="small fw-semibold text-nowrap">{{ $mm ? 'အားလုံး ကြည့်ရန်' : 'See all' }} <i class="bi bi-chevron-right"></i></a>
                </div>
                @if (empty($featured))
                    <div class="text-center mt-muted py-4">
                        <i class="bi bi-box-seam" style="font-size:1.8rem;"></i>
                        <p class="mb-0 mt-2">{{ $mm ? 'ပစ္စည်းများ မကြာမီ ရောက်လာမည်။' : 'Products are on their way.' }}</p>
                    </div>
                @else
                    <div class="row g-2 g-md-3 row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6">
                        @foreach ($featured as $p)
                            <div class="col">@include('theme.doeh-mart.partials.card', ['p' => $p, 'ready' => $ready, 'mm' => $mm, 'pickup' => $pickup])</div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Rewards (DOEH Identity). Only renders when the plugin returns a panel. --}}
    @php $loyalty = function_exists('bp_apply_filters') ? trim(bp_apply_filters('doeh_loyalty_panel', '')) : ''; @endphp
    @if ($loyalty !== '')
        <section class="mt-section pt-0">
            <div class="container"><div class="mt-panel">{!! $loyalty !!}</div></div>
        </section>
    @endif
@endsection
