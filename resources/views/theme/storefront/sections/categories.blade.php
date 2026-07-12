{{-- Category shortcuts strip. From sf_categories_json (repeater); hides if empty. --}}
@php
    $mm = app()->getLocale() === 'mm';
    $items = json_decode(bp_option('sf_categories_json', ''), true);
    if (!is_array($items)) {
        $items = [
            ['icon' => 'bi-phone', 'name' => 'Electronics', 'url' => '/shop'],
            ['icon' => 'bi-bag', 'name' => 'Fashion', 'url' => '/shop'],
            ['icon' => 'bi-house', 'name' => 'Home', 'url' => '/shop'],
            ['icon' => 'bi-heart', 'name' => 'Beauty', 'url' => '/shop'],
        ];
    }
@endphp
@if(count($items))
<section class="sf-section pt-0">
    <div class="container">
        <div class="sf-panel">
            <h2 class="sf-panel-title mb-3">{{ $mm ? 'အမျိုးအစားများ' : 'Categories' }}</h2>
            <div class="row g-2 row-cols-3 row-cols-md-6">
                @foreach($items as $c)
                    @php $c = (array) $c; @endphp
                    <div class="col">
                        <a class="sf-cat" href="{{ $c['url'] ?? '/shop' }}">
                            <i class="bi {{ $c['icon'] ?? 'bi-tag' }}"></i>
                            <span>{{ $c['name'] ?? '' }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
