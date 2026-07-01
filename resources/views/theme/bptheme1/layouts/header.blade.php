@php $siteName = optional(site_information('blogname'))->option_value ?: config('app.name'); @endphp
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand text-primary" href="{{ url('/') }}">{{ $siteName }}</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#bpNav"
                    aria-controls="bpNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="bpNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">{{ ucfirst(__('general.home') ?? 'Home') }}</a></li>

                    @foreach (bp_menu() as $menu)
                        @php
                            if (app()->getLocale() === 'mm' && isset($menu->translate) && $menu->translate->lang == 2) {
                                $menu = $menu->translate;
                            }
                            $hasChildren = isset($menu->children) && sizeof($menu->children) > 0;
                            $menuUrl = $menu->menu_type === 'default' ? url('/'.$menu->menu_link) : $menu->menu_link;
                        @endphp

                        @if($hasChildren)
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ $menu->menu_name }}
                                </a>
                                <ul class="dropdown-menu">
                                    @foreach ($menu->children as $sub)
                                        @php
                                            if (app()->getLocale() === 'mm' && isset($sub->translate) && $sub->translate->lang == 2) {
                                                $sub = $sub->translate;
                                            }
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

                    <li class="nav-item ms-lg-2">
                        @if (Auth::guard('customer_web')->check())
                            <a class="btn btn-sm btn-outline-primary" href="{{ url('customer/profile') }}">
                                <i class="bi bi-person-circle"></i> {{ Auth::guard('customer_web')->user()->first_name }}
                            </a>
                        @else
                            <a class="btn btn-sm btn-outline-primary" href="{{ url('/customer/sign-in') }}">
                                <i class="bi bi-person"></i> {{ ucfirst(__('general.login') ?? 'Login') }}
                            </a>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
