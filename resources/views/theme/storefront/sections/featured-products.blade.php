{{-- Flash Sale — Shopee-style. Fills from the Commerce plugin's
     business_featured_products hook (cards already include Add-to-cart when
     Commerce Checkout is active). Hidden when Commerce is inactive or empty.
     The countdown is a marketing timer that resets at midnight; it does not
     imply per-product discounts (the product model has no sale-price field). --}}
@php
    $mm = app()->getLocale() === 'mm';
    $grid = trim(bp_apply_filters('business_featured_products', ''));
@endphp
@if($grid !== '')
<section id="products" class="sf-section pt-0">
    <div class="container">
        <div class="sf-flash">
            <div class="sf-flash-bar">
                <span class="sf-flash-logo"><i class="bi bi-lightning-charge-fill"></i>{{ $mm ? 'ဖလက်ရှ် စေး' : 'Flash Sale' }}</span>
                <span class="d-none d-sm-inline sf-muted small">{{ $mm ? 'ကုန်ဆုံးရန်' : 'Ending in' }}</span>
                <span class="sf-countdown" id="sfCountdown" aria-label="{{ $mm ? 'ကျန်ချိန်' : 'Time remaining' }}">
                    <span class="u" data-h>00</span><span class="sep">:</span>
                    <span class="u" data-m>00</span><span class="sep">:</span>
                    <span class="u" data-s>00</span>
                </span>
                <a href="{{ url('/shop') }}" class="sf-flash-link">{{ $mm ? 'အားလုံး ကြည့်ရန်' : 'See All' }} <i class="bi bi-chevron-right"></i></a>
            </div>
            <div class="p-2 p-md-3">
                <div class="row g-2 g-md-3">
                    {!! $grid !!}
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
<script>
(function () {
    var el = document.getElementById('sfCountdown');
    if (!el) return;
    var h = el.querySelector('[data-h]'), m = el.querySelector('[data-m]'), s = el.querySelector('[data-s]');
    var pad = function (n) { return (n < 10 ? '0' : '') + n; };
    function tick() {
        var now = new Date(), end = new Date(now); end.setHours(24, 0, 0, 0);
        var d = Math.max(0, Math.floor((end - now) / 1000));
        h.textContent = pad(Math.floor(d / 3600));
        m.textContent = pad(Math.floor((d % 3600) / 60));
        s.textContent = pad(d % 60);
    }
    tick(); setInterval(tick, 1000);
})();
</script>
@endpush
@else
{{-- Commerce inactive: point the owner at how to enable the shop. --}}
@if(Auth::guard('admins')->check())
<section class="sf-section pt-0">
    <div class="container"><div class="sf-panel text-center text-muted py-4">
        <i class="bi bi-box-seam" style="font-size:1.6rem;"></i>
        <p class="mb-1 mt-2">{{ $mm ? 'ကုန်ပစ္စည်းများ မပြသေးပါ။' : 'No products showing yet.' }}</p>
        <small>{{ $mm ? 'Commerce ပလပ်အင်ကို activate လုပ်၍ ကုန်ပစ္စည်းများ ထည့်ပါ။' : 'Activate the Commerce plugin and add featured products.' }} <a href="{{ url('bp-admin/plugins') }}">{{ $mm ? 'ပလပ်အင်များ' : 'Plugins' }}</a></small>
    </div></div>
</section>
@endif
@endif
