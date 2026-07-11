@php
    $siteName = optional(site_information('blogname'))->option_value ?: config('app.name');
    $mm = app()->getLocale() === 'mm';
@endphp
<header>
    <nav class="navbar navbar-expand-lg pl-nav sticky-top">
        <div class="container py-2">
            <a class="navbar-brand pl-grad-text fs-4" href="{{ url('/') }}">{{ $siteName }}</a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#plNav"
                    aria-controls="plNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-3"></i>
            </button>

            <div class="collapse navbar-collapse" id="plNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-1">
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">{{ $mm ? 'ပင်မ' : 'Home' }}</a></li>

                    @foreach (bp_menu() as $menu)
                        @php
                            if ($mm && isset($menu->translate) && $menu->translate->lang == 2) { $menu = $menu->translate; }
                            $hasChildren = isset($menu->children) && sizeof($menu->children) > 0;
                            $menuUrl = $menu->menu_type === 'default' ? url('/'.$menu->menu_link) : $menu->menu_link;
                        @endphp
                        @if($hasChildren)
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">{{ $menu->menu_name }}</a>
                                <ul class="dropdown-menu border-0 shadow" style="border-radius:16px;">
                                    @foreach ($menu->children as $sub)
                                        @php
                                            if ($mm && isset($sub->translate) && $sub->translate->lang == 2) { $sub = $sub->translate; }
                                            $subUrl = $sub->menu_type === 'default' ? url('/'.$sub->menu_link) : $sub->menu_link;
                                        @endphp
                                        <li><a class="dropdown-item" href="{{ $subUrl }}">{{ $sub->menu_name }}</a></li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <li class="nav-item"><a class="nav-link" href="{{ $menuUrl }}">{{ $menu->menu_name }}</a></li>
                        @endif
                    @endforeach

                    <li class="nav-item"><a class="nav-link" href="{{ url('/events') }}">{{ $mm ? 'ပွဲများ' : 'Events' }}</a></li>
                    @if(bp_option('faq_enabled', 'yes') === 'yes')
                        <li class="nav-item"><a class="nav-link" href="{{ url('/faq') }}">{{ $mm ? 'အမေးအဖြေ' : 'FAQ' }}</a></li>
                    @endif

                    <li class="nav-item ms-lg-2">
                        <form class="d-flex" role="search" action="{{ url('/search') }}" method="GET">
                            <div class="input-group input-group-sm">
                                <input class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="{{ $mm ? 'ရှာဖွေရန်…' : 'Search…' }}" aria-label="Search" style="max-width:150px;border-radius:999px 0 0 999px;">
                                <button class="btn btn-pl-soft btn-sm" type="submit" style="border-radius:0 999px 999px 0;"><i class="bi bi-search"></i></button>
                            </div>
                        </form>
                    </li>

                    <li class="nav-item ms-lg-2 d-flex align-items-center gap-2">
                        <span class="small pl-muted">
                            <a href="{{ url('lang/en') }}" class="{{ $mm ? 'pl-muted' : 'fw-bold text-primary' }}">EN</a> /
                            <a href="{{ url('lang/mm') }}" class="{{ $mm ? 'fw-bold text-primary' : 'pl-muted' }}">မြန်မာ</a>
                        </span>
                        @if (Auth::guard('customer_web')->check())
                            <a class="btn btn-pl-soft btn-sm" href="{{ url('customer/profile') }}"><i class="bi bi-person"></i> {{ Auth::guard('customer_web')->user()->first_name }}</a>
                        @else
                            <a class="btn btn-pl btn-sm" href="{{ url('/customer/sign-in') }}">{{ $mm ? 'ဝင်ရန်' : 'Login' }}</a>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
