@php
    $siteName = trim((string) bp_option('mt_name')) ?: (optional(site_information('blogname'))->option_value ?: config('app.name'));
    $mm = app()->getLocale() === 'mm';
    $logoOpt = trim((string) bp_option('mt_logo'));
    $logoUrl = $logoOpt === '' ? '' : (\Illuminate\Support\Str::startsWith($logoOpt, ['http', '/']) ? $logoOpt : bp_upload_url($logoOpt));
    $cartCount = array_sum(array_map('intval', (array) session('doeh_store_cart', [])));
    $types = function_exists('doeh_storefront_fulfillment_types') ? doeh_storefront_fulfillment_types() : ['pickup'];
    $strip = trim((string) bp_option('mt_strip_note')) ?: (in_array('delivery', $types, true)
        ? ($mm ? 'အွန်လိုင်း မှာယူ၍ ဆိုင်တွင် ယူနိုင် သို့ အိမ်အရောက် ပို့ပေးသည်' : 'Order online — pick up or get it delivered')
        : ($mm ? 'အွန်လိုင်း မှာယူ၍ ဆိုင်တွင် လာယူနိုင်သည်' : 'Order online, pick up at the shop'));
    $onShop = request()->is('store') || request()->is('store/*');
@endphp
{{-- The top strip and the orange bar are siblings at page level, not inside one wrapper: a
     sticky element only sticks within its parent, so a wrapper would let the bar scroll away. --}}
<div class="mt-topbar">
    <div class="container d-flex justify-content-between align-items-center py-1" style="gap:1rem;">
        <span class="text-truncate"><i class="bi {{ in_array('delivery', $types, true) ? 'bi-truck' : 'bi-shop' }}"></i> {{ $strip }}</span>
        <span class="d-flex align-items-center flex-shrink-0" style="gap:.9rem;">
            <a href="{{ url('lang/en') }}" class="{{ $mm ? '' : 'fw-bold' }}">EN</a>
            <a href="{{ url('lang/mm') }}" class="{{ $mm ? 'fw-bold' : '' }}">မြန်မာ</a>
        </span>
    </div>
</div>

<header class="mt-header sticky-top">
    <div class="container">
        <div class="d-flex align-items-center py-2" style="gap:1.1rem;">
            <a class="mt-brand" href="{{ url('/') }}">
                @if ($logoUrl)<img src="{{ $logoUrl }}" alt="{{ $siteName }}" style="height:36px; width:auto;">@else<i class="bi bi-shop"></i> {{ $siteName }}@endif
            </a>

            <form class="mt-search flex-grow-1 d-none d-md-block" role="search" action="{{ url('/store') }}" method="GET">
                <div class="input-group">
                    <input class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="{{ $mm ? 'ကုန်ပစ္စည်း ရှာရန်…' : 'Search products…' }}" aria-label="{{ $mm ? 'ရှာရန်' : 'Search' }}">
                    <button class="btn" type="submit" aria-label="{{ $mm ? 'ရှာရန်' : 'Search' }}"><i class="bi bi-search"></i></button>
                </div>
            </form>

            <a href="{{ url('/store/cart') }}" class="mt-cart ms-auto ms-md-0" aria-label="{{ $mm ? 'ခြင်း' : 'Cart' }}">
                <i class="bi bi-cart3"></i>
                @if ($cartCount > 0)<span class="badge">{{ $cartCount }}</span>@endif
            </a>

            @if (function_exists('doeh_identity_enabled') && doeh_identity_enabled())
                {{-- DOEH account slot; the footer script fills it through window.DoehIdentity. --}}
                <div class="dropdown d-none d-sm-block" id="mt-doeh-account"></div>
            @endif
        </div>

        <form class="mt-search d-md-none pb-2" role="search" action="{{ url('/store') }}" method="GET">
            <div class="input-group">
                <input class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="{{ $mm ? 'ကုန်ပစ္စည်း ရှာရန်…' : 'Search products…' }}" aria-label="{{ $mm ? 'ရှာရန်' : 'Search' }}">
                <button class="btn" type="submit" aria-label="{{ $mm ? 'ရှာရန်' : 'Search' }}"><i class="bi bi-search"></i></button>
            </div>
        </form>

        <nav class="d-flex flex-wrap align-items-center pb-2" style="gap:.4rem 1.4rem;">
            <a class="mt-navlink {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">{{ $mm ? 'ပင်မ' : 'Home' }}</a>
            <a class="mt-navlink {{ $onShop ? 'active' : '' }}" href="{{ url('/store') }}">{{ $mm ? 'ဈေးဆိုင်' : 'Shop' }}</a>
            @foreach (bp_menu() as $menu)
                @php
                    if ($mm && isset($menu->translate) && $menu->translate->lang == 2) { $menu = $menu->translate; }
                    $menuUrl = $menu->menu_type === 'default' ? url('/'.$menu->menu_link) : $menu->menu_link;
                @endphp
                <a class="mt-navlink" href="{{ $menuUrl }}">{{ $menu->menu_name }}</a>
            @endforeach
        </nav>
    </div>
</header>
