@php
    $siteName = trim((string) bp_option('ex_name')) ?: (optional(site_information('blogname'))->option_value ?: config('app.name'));
    // The logo pattern: an uploads path or a full URL, same resolution as the
    // Customize preview. When unset, the text brand renders instead.
    $logoOpt = trim((string) bp_option('ex_logo'));
    $logoUrl = $logoOpt === '' ? '' : (\Illuminate\Support\Str::startsWith($logoOpt, ['http', '/']) ? $logoOpt : bp_upload_url($logoOpt));
    $cartCount = array_sum(array_map('intval', (array) session('doeh_store_cart', [])));
@endphp
<header style="border-bottom:1px solid var(--line);">
    <div class="wrap" style="display:flex; align-items:center; gap:18px; padding-block:14px;">
        <a href="{{ url('/') }}" style="font-weight:700; color:var(--ink);">
            @if ($logoUrl)<img src="{{ $logoUrl }}" alt="{{ $siteName }}" style="height:36px; width:auto; display:block;">@else{{ $siteName }}@endif
        </a>
        <nav style="display:flex; gap:14px; margin-left:auto;">
            <a href="{{ url('/store') }}">Shop</a>
            <a href="{{ url('/store/cart') }}">Cart{{ $cartCount ? " ({$cartCount})" : '' }}</a>
        </nav>
        {{-- The identity account slot: an empty mount the footer script fills.
             Only render it when the plugin is on, so the theme degrades. --}}
        @if (function_exists('doeh_identity_enabled') && doeh_identity_enabled())
            <span id="ex-account"></span>
        @endif
    </div>
</header>
