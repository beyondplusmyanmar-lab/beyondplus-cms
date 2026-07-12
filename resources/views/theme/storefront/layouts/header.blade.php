@php
    $siteName = optional(site_information('blogname'))->option_value ?: config('app.name');
    $mm = app()->getLocale() === 'mm';
    $cartCount = array_sum(array_map('intval', (array) session('commerce_cart', [])));
    $shipNote = bp_option('sf_free_shipping_note') ?: ($mm ? 'အိမ်တိုင်ရာရောက် ပို့ဆောင်ပေးသည်' : 'Fast delivery, cash on delivery');
@endphp
<header>
    {{-- Top strip --}}
    <div class="sf-topbar">
        <div class="container d-flex justify-content-between align-items-center py-1">
            <span><i class="bi bi-truck"></i> {{ $shipNote }}</span>
            <span class="d-none d-sm-flex align-items-center" style="gap:.9rem;">
                <a href="{{ url('/faq') }}">{{ $mm ? 'အကူအညီ' : 'Help' }}</a>
                <a href="{{ url('lang/en') }}" class="{{ $mm ? '' : 'fw-bold' }}">EN</a>
                <a href="{{ url('lang/mm') }}" class="{{ $mm ? 'fw-bold' : '' }}">မြန်မာ</a>
            </span>
        </div>
    </div>

    {{-- Main header: brand · search · cart --}}
    <div class="sf-header sticky-top">
        <div class="container">
            <div class="d-flex align-items-center py-2" style="gap:1rem;">
                <a class="navbar-brand mb-0" href="{{ url('/') }}"><i class="bi bi-shop"></i> {{ $siteName }}</a>

                <form class="sf-search flex-grow-1 d-none d-md-block" role="search" action="{{ url('/search') }}" method="GET">
                    <div class="input-group">
                        <input class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="{{ $mm ? 'ကုန်ပစ္စည်း ရှာရန်…' : 'Search products…' }}" aria-label="Search">
                        <button class="btn" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>

                <a href="{{ url('/cart') }}" class="sf-cart ms-auto ms-md-0" aria-label="{{ $mm ? 'ခြင်း' : 'Cart' }}">
                    <i class="bi bi-cart3"></i>
                    @if($cartCount > 0)<span class="badge">{{ $cartCount }}</span>@endif
                </a>

                @if (Auth::guard('customer_web')->check())
                    <a class="sf-navlink d-none d-sm-inline" href="{{ url('customer/profile') }}"><i class="bi bi-person-circle"></i> {{ Auth::guard('customer_web')->user()->first_name }}</a>
                @else
                    <a class="sf-navlink d-none d-sm-inline" href="{{ url('/customer/sign-in') }}"><i class="bi bi-person"></i> {{ $mm ? 'ဝင်ရန်' : 'Login' }}</a>
                @endif
            </div>

            {{-- Mobile search --}}
            <form class="sf-search d-md-none pb-2" role="search" action="{{ url('/search') }}" method="GET">
                <div class="input-group">
                    <input class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="{{ $mm ? 'ကုန်ပစ္စည်း ရှာရန်…' : 'Search products…' }}" aria-label="Search">
                    <button class="btn" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>

            {{-- Nav row (menu items) --}}
            <div class="d-flex flex-wrap align-items-center pb-2" style="gap:1rem;">
                <a class="sf-navlink" href="{{ url('/') }}">{{ $mm ? 'ပင်မ' : 'Home' }}</a>
                <a class="sf-navlink" href="{{ url('/shop') }}">{{ $mm ? 'ဈေးဆိုင်' : 'Shop' }}</a>
                @foreach (bp_menu() as $menu)
                    @php
                        if ($mm && isset($menu->translate) && $menu->translate->lang == 2) { $menu = $menu->translate; }
                        $menuUrl = $menu->menu_type === 'default' ? url('/'.$menu->menu_link) : $menu->menu_link;
                    @endphp
                    <a class="sf-navlink" href="{{ $menuUrl }}">{{ $menu->menu_name }}</a>
                @endforeach
            </div>
        </div>
    </div>
</header>
