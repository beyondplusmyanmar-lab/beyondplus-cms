@php
    $siteName = optional(site_information('blogname'))->option_value ?: config('app.name');
    $mm = app()->getLocale() === 'mm';
    $ctaLabel = bp_option('biz_nav_cta_label', $mm ? 'ဆက်သွယ်ရန်' : 'Get in touch');
    $ctaUrl   = bp_option('biz_nav_cta_url') ?: url('/contact');
@endphp
<header>
    <nav class="navbar navbar-expand-lg bz-nav sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">{{ $siteName }}</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#bzNav"
                    aria-controls="bzNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="bzNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
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
                                <ul class="dropdown-menu">
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

                    <li class="nav-item"><a class="nav-link" href="{{ url('/blog') }}">{{ $mm ? 'သတင်း' : 'News' }}</a></li>
                    @if(bp_option('faq_enabled', 'yes') === 'yes')
                        <li class="nav-item"><a class="nav-link" href="{{ url('/faq') }}">{{ $mm ? 'အမေးအဖြေ' : 'FAQ' }}</a></li>
                    @endif

                    <li class="nav-item ms-lg-3">
                        <form class="d-flex" role="search" action="{{ url('/search') }}" method="GET">
                            <div class="input-group input-group-sm">
                                <input class="form-control" type="search" name="q" value="{{ request('q') }}"
                                       placeholder="{{ $mm ? 'ရှာဖွေရန်…' : 'Search…' }}" aria-label="Search" style="max-width:150px;">
                                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                            </div>
                        </form>
                    </li>

                    <li class="nav-item ms-lg-3 d-flex align-items-center">
                        <div class="bz-lang" role="group" aria-label="Language">
                            <a href="{{ url('lang/en') }}" class="bz-lang__opt {{ $mm ? '' : 'active' }}">EN</a>
                            <a href="{{ url('lang/mm') }}" class="bz-lang__opt {{ $mm ? 'active' : '' }}">မြန်မာ</a>
                        </div>
                    </li>

                    @if (Auth::guard('customer_web')->check())
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-sm btn-outline-primary" href="{{ url('customer/profile') }}">
                                <i class="bi bi-person-circle"></i> {{ Auth::guard('customer_web')->user()->first_name }}
                            </a>
                        </li>
                    @else
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-sm btn-outline-primary" href="{{ url('/customer/sign-in') }}">
                                <i class="bi bi-person"></i> {{ $mm ? 'ဝင်ရန်' : 'Login' }}
                            </a>
                        </li>
                    @endif

                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-sm btn-primary" href="{{ $ctaUrl }}">{{ $ctaLabel }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
