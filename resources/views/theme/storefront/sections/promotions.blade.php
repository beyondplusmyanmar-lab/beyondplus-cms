{{-- Promotions — fills from the Commerce plugin's business_promotions hook. --}}
@php
    $mm = app()->getLocale() === 'mm';
    $promos = trim(bp_apply_filters('business_promotions', ''));
@endphp
@if($promos !== '')
<section class="sf-section pt-0">
    <div class="container">
        <div class="sf-panel">
            <h2 class="sf-panel-title mb-3" style="color:var(--sf-accent);"><i class="bi bi-megaphone"></i> {{ $mm ? 'ပရိုမိုးရှင်းများ' : 'Promotions' }}</h2>
            <div class="row g-2 g-md-3">
                {!! $promos !!}
            </div>
        </div>
    </div>
</section>
@endif
