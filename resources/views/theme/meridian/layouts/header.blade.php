@php
    $siteName = optional(site_information('blogname'))->option_value ?: config('app.name');
    $mm = app()->getLocale() === 'mm';
@endphp
<header>
    {{-- Top bar: dateline, language, account — the small print of a masthead --}}
    <div class="md-topbar">
        <div class="container d-flex justify-content-between align-items-center py-1">
            <span class="d-none d-sm-inline">{{ \Carbon\Carbon::now()->translatedFormat('l, j F Y') }}</span>
            <div class="d-flex align-items-center gap-3">
                <span class="md-lang" role="group" aria-label="Language">
                    <a href="{{ url('lang/en') }}" class="{{ $mm ? '' : 'active' }}">EN</a>
                    <span aria-hidden="true">·</span>
                    <a href="{{ url('lang/mm') }}" class="{{ $mm ? 'active' : '' }}">မြန်မာ</a>
                </span>
                @if (Auth::guard('customer_web')->check())
                    <a href="{{ url('customer/profile') }}"><i class="bi bi-person-circle"></i> {{ Auth::guard('customer_web')->user()->first_name }}</a>
                @else
                    <a href="{{ url('/customer/sign-in') }}"><i class="bi bi-person"></i> {{ $mm ? 'ဝင်ရန်' : 'Sign in' }}</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Masthead wordmark --}}
    <div class="md-masthead">
        <div class="container text-center py-4">
            <a class="d-inline-block" href="{{ url('/') }}">
                <span class="md-wordmark" style="font-size:clamp(2rem,6vw,3.25rem);">{{ $siteName }}</span>
            </a>
            @php $tagline = optional(site_information('blogdescription'))->option_value; @endphp
            @if($tagline)
                <div class="md-kicker md-kicker--muted mt-2">{{ $tagline }}</div>
            @endif
        </div>
    </div>

    {{-- Section navigation --}}
    <nav class="navbar navbar-expand-lg md-nav sticky-top p-0">
        <div class="container">
            <button class="navbar-toggler my-2 border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mdNav"
                    aria-controls="mdNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-3"></i>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="mdNav">
                <ul class="navbar-nav mb-2 mb-lg-0 align-items-lg-center flex-wrap justify-content-center">
                    <li class="nav-item"><a class="nav-link active" href="{{ url('/') }}">{{ $mm ? 'ပင်မ' : 'Home' }}</a></li>

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

                    <li class="nav-item"><a class="nav-link" href="{{ url('/events') }}">{{ $mm ? 'ပွဲများ' : 'Events' }}</a></li>
                    @if(bp_option('faq_enabled', 'yes') === 'yes')
                        <li class="nav-item"><a class="nav-link" href="{{ url('/faq') }}">{{ $mm ? 'အမေးအဖြေ' : 'FAQ' }}</a></li>
                    @endif
                    <li class="nav-item"><a class="nav-link" href="{{ url('/contact') }}">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Contact' }}</a></li>

                    <li class="nav-item">
                        <form class="d-flex align-items-center ps-lg-2" role="search" action="{{ url('/search') }}" method="GET">
                            <input class="form-control form-control-sm border-0 bg-transparent" type="search" name="q" value="{{ request('q') }}"
                                   placeholder="{{ $mm ? 'ရှာဖွေရန်…' : 'Search…' }}" aria-label="Search" style="max-width:130px;">
                            <button class="btn btn-sm border-0 p-1" type="submit" aria-label="Search"><i class="bi bi-search"></i></button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
