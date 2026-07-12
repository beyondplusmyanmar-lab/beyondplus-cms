{{--
    Featured Products — POS / commerce integration point.

    Core CMS ships NO product code (products, cart and orders live in a separate
    commerce plugin). This section is a filter slot: it renders only when a
    plugin returns markup for it, so a site with no commerce plugin simply hides
    the whole section — no empty placeholder.

    Plugin contract (register from the plugin's main file):

        bp_add_filter('business_featured_products', function ($html) {
            // return HTML for the inner grid, e.g. a row of .bz-card product cards
            return $html . view('my-commerce::widgets.featured-grid')->render();
        });

    Related slots this theme also exposes: 'business_promotions',
    'business_store_locations', and 'business_hero_actions'.
--}}
@php
    $mm = app()->getLocale() === 'mm';
    $grid = trim(bp_apply_filters('business_featured_products', ''));
@endphp

@if($grid !== '')
<section id="products" class="bz-section bz-section--alt">
    <div class="container">
        <div class="bz-section-head">
            <span class="bz-eyebrow">{{ $mm ? 'ကုန်ပစ္စည်းများ' : 'Shop' }}</span>
            <h2 class="mt-2">{{ bp_option('biz_products_title', $mm ? 'အထူးရွေးချယ် ကုန်ပစ္စည်းများ' : 'Featured Products') }}</h2>
            <p class="bz-muted mb-0">{{ bp_option('biz_products_subtitle', $mm ? 'ကျွန်ုပ်တို့ဆိုင်မှ ရွေးချယ်ထားသော ကုန်ပစ္စည်းများ။' : 'A selection from our catalogue.') }}</p>
        </div>
        <div class="row g-4">
            {!! $grid !!}
        </div>
    </div>
</section>
@endif

{{-- Promotions slot (also plugin-driven, hidden when empty). --}}
@php $promos = trim(bp_apply_filters('business_promotions', '')); @endphp
@if($promos !== '')
<section id="promotions" class="bz-section">
    <div class="container">
        <div class="bz-section-head">
            <h2>{{ bp_option('biz_promotions_title', $mm ? 'ပရိုမိုးရှင်းများ' : 'Promotions') }}</h2>
        </div>
        <div class="row g-4">{!! $promos !!}</div>
    </div>
</section>
@endif
