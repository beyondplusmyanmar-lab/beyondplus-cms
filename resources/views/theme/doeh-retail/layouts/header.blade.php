@php
    $siteName = trim((string) bp_option('rt_name')) ?: (optional(site_information('blogname'))->option_value ?: config('app.name'));
    $mm = app()->getLocale() === 'mm';
    $cartCount = array_sum(array_map('intval', (array) session('doeh_store_cart', [])));
@endphp
<header class="rt-head">
    <div class="rt-wrap row">
        <a class="rt-brand" href="{{ url('/') }}">{{ $siteName }}</a>
        <nav class="rt-nav">
            <a href="{{ url('/') }}">{{ $mm ? 'ပင်မ' : 'Home' }}</a>
            <a href="{{ url('/store') }}">{{ $mm ? 'ဈေးဆိုင်' : 'Shop' }}</a>
        </nav>
        <span class="rt-spacer"></span>
        <a class="rt-cart" href="{{ url('/store/cart') }}">
            {{ $mm ? 'ခြင်း' : 'Bag' }}@if($cartCount > 0)<span class="rt-badge">{{ $cartCount }}</span>@endif
        </a>
        @if (function_exists('doeh_identity_enabled') && doeh_identity_enabled())
            <span id="rt-account"></span>
        @endif
    </div>
</header>
