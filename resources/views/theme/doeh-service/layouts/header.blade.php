@php
    $siteName = trim((string) bp_option('sv_name')) ?: (optional(site_information('blogname'))->option_value ?: config('app.name'));
    $mm = app()->getLocale() === 'mm';
    $logoOpt = trim((string) bp_option('sv_logo'));
    $logoUrl = $logoOpt === '' ? '' : (\Illuminate\Support\Str::startsWith($logoOpt, ['http', '/']) ? $logoOpt : bp_upload_url($logoOpt));
    $reqCount = array_sum(array_map('intval', (array) session('doeh_store_cart', [])));
@endphp
<header class="sv-head">
    <div class="sv-wrap row">
        <a class="sv-brand" href="{{ url('/') }}">@if ($logoUrl)<img src="{{ $logoUrl }}" alt="{{ $siteName }}" style="height:36px; width:auto; display:block;">@else{{ $siteName }}@endif</a>
        <nav class="sv-nav">
            <a href="{{ url('/') }}">{{ $mm ? 'ပင်မ' : 'Home' }}</a>
            <a href="{{ url('/store') }}">{{ $mm ? 'ဝန်ဆောင်မှုများ' : 'Services' }}</a>
        </nav>
        <span class="sv-spacer"></span>
        <a class="sv-req" href="{{ url('/store/cart') }}">
            {{ $mm ? 'တောင်းဆိုမှု' : 'Requests' }}@if($reqCount > 0)<span class="sv-badge">{{ $reqCount }}</span>@endif
        </a>
        @if (function_exists('doeh_identity_enabled') && doeh_identity_enabled())
            <span id="sv-account"></span>
        @endif
    </div>
</header>
