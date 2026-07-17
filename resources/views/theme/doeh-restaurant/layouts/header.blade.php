@php
    $siteName = trim((string) bp_option('r_name')) ?: (optional(site_information('blogname'))->option_value ?: config('app.name'));
    $mm = app()->getLocale() === 'mm';
    $logoOpt = trim((string) bp_option('r_logo'));
    $logoUrl = $logoOpt === '' ? '' : (\Illuminate\Support\Str::startsWith($logoOpt, ['http', '/']) ? $logoOpt : bp_upload_url($logoOpt));
    $cartCount = array_sum(array_map('intval', (array) session('doeh_store_cart', [])));
@endphp
<header class="r-head">
    <div class="r-wrap row">
        <a class="r-brand" href="{{ url('/') }}">@if ($logoUrl)<img src="{{ $logoUrl }}" alt="{{ $siteName }}" style="height:36px; width:auto; display:block;">@else{{ $siteName }}@endif</a>
        <nav class="r-nav">
            <a href="{{ url('/') }}">{{ $mm ? 'ပင်မ' : 'Home' }}</a>
            <a href="{{ url('/store') }}">{{ $mm ? 'မီနူး' : 'Menu' }}</a>
        </nav>
        <span class="r-spacer"></span>
        <a class="r-cart" href="{{ url('/store/cart') }}">
            {{ $mm ? 'အော်ဒါ' : 'Order' }}@if($cartCount > 0)<span class="r-badge">{{ $cartCount }}</span>@endif
        </a>
        @if (function_exists('doeh_identity_enabled') && doeh_identity_enabled())
            <span id="r-account"></span>
        @endif
    </div>
</header>
