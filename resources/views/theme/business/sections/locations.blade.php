{{-- Store Locations — plugin-driven slot. Renders only when a commerce/locations
     plugin returns branch markup via the `business_store_locations` filter, so a
     site with no branches hides the whole section. The plugin returns column
     items; this section supplies the heading and the `.row g-4` wrapper. --}}
@php
    $mm = app()->getLocale() === 'mm';
    $locations = trim(bp_apply_filters('business_store_locations', ''));
@endphp

@if($locations !== '')
<section id="locations" class="bz-section">
    <div class="container">
        <div class="bz-section-head">
            <span class="bz-eyebrow">{{ $mm ? 'လာရောက်ရန်' : 'Visit us' }}</span>
            <h2 class="mt-2">{{ bp_option('biz_locations_title', $mm ? 'ကျွန်ုပ်တို့၏ ဆိုင်ခွဲများ' : 'Our Locations') }}</h2>
        </div>
        <div class="row g-4">
            {!! $locations !!}
        </div>
    </div>
</section>
@endif
