{{-- Featured products — fills from the Commerce plugin's business_featured_products
     hook (cards already include Add-to-cart when Commerce Checkout is active).
     Hidden when Commerce is inactive or has no featured products. --}}
@php
    $mm = app()->getLocale() === 'mm';
    $grid = trim(bp_apply_filters('business_featured_products', ''));
@endphp
@if($grid !== '')
<section id="products" class="sf-section pt-0">
    <div class="container">
        <div class="sf-panel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="sf-panel-title mb-0" style="color:var(--sf-primary);"><i class="bi bi-fire"></i> {{ $mm ? 'အထူးရွေးချယ် ကုန်ပစ္စည်းများ' : 'Featured Products' }}</h2>
                <a href="{{ url('/shop') }}" class="small fw-semibold">{{ $mm ? 'အားလုံး ကြည့်ရန်' : 'See all' }} <i class="bi bi-chevron-right"></i></a>
            </div>
            <div class="row g-2 g-md-3">
                {!! $grid !!}
            </div>
            <div class="text-center mt-3">
                <a href="{{ url('/shop') }}" class="btn btn-outline-primary">{{ $mm ? 'ကုန်ပစ္စည်း အားလုံး' : 'Browse all products' }}</a>
            </div>
        </div>
    </div>
</section>
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
