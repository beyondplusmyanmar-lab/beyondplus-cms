{{-- Loyalty — fills from the DOEH Identity plugin's doeh_loyalty_panel filter.
     The panel is a browser-side widget: signed-out visitors see a sign-in
     prompt, signed-in customers their live points. Hidden entirely when the
     plugin is inactive or unconfigured. --}}
@php
    $mm = app()->getLocale() === 'mm';
    $loyalty = function_exists('bp_apply_filters') ? trim(bp_apply_filters('doeh_loyalty_panel', '')) : '';
@endphp
@if($loyalty !== '')
<section class="sf-section pt-0">
    <div class="container">
        <div class="sf-panel">
            <h2 class="sf-panel-title mb-3"><i class="bi bi-stars"></i> {{ $mm ? 'ကျွန်ုပ်၏ ဆုမှတ်များ' : 'My rewards' }}</h2>
            {!! $loyalty !!}
        </div>
    </div>
</section>
@endif
