@php
    $siteName = trim((string) bp_option('biz_shop_name')) ?: (optional(site_information('blogname'))->option_value ?: config('app.name'));
    $mm = app()->getLocale() === 'mm';
    $cartCount = array_sum(array_map('intval', (array) session('doeh_store_cart', [])));
@endphp
<header class="site">
    <div class="wrap bar">
        <a class="brand serif" href="{{ url('/') }}">{{ $siteName }}</a>
        <nav>
            <a href="{{ url('/') }}">{{ $mm ? 'ပင်မ' : 'Home' }}</a>
            <a href="{{ url('/store') }}">{{ $mm ? 'ဈေးဆိုင်' : 'Shop' }}</a>
        </nav>
        <span class="spacer"></span>
        <a class="cart-link" href="{{ url('/store/cart') }}">
            {{ $mm ? 'ခြင်း' : 'Cart' }}@if($cartCount > 0) ({{ $cartCount }})@endif
        </a>
        {{-- DOEH Identity account slot — theme-owned UI, driven by window.DoehIdentity
             from the footer script (the theme never sees a token). Hidden until the
             identity plugin is enabled. --}}
        @if (function_exists('doeh_identity_enabled') && doeh_identity_enabled())
            <span id="biz-account"></span>
        @endif
    </div>
</header>
