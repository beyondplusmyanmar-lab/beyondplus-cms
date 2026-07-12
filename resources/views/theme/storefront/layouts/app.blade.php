<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

    @php
        $siteName = optional(site_information('blogname'))->option_value ?: config('app.name');
        $siteDesc = optional(site_information('blogdescription'))->option_value ?: 'Shop online.';
        $metaDesc = trim($__env->yieldContent('meta_description')) ?: $siteDesc;
    @endphp

    <title>@hasSection('title')@yield('title') — {{ $siteName }}@else{{ $siteName }}@endif</title>
    <meta name="description" content="{{ $metaDesc }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="@hasSection('title')@yield('title')@else{{ $siteName }}@endif">
    <meta property="og:description" content="{{ $metaDesc }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+Myanmar:wght@400;500;600;700&display=swap">

    <style>
        :root {
            --sf-primary:      {{ bp_option('sf_color_primary',      '#ee4d2d') }};
            --sf-primary-dark: {{ bp_option('sf_color_primary_dark', '#d73211') }};
            --sf-accent:       {{ bp_option('sf_color_accent',       '#ffb400') }};
            --sf-bg:           #f5f5f5;
            --sf-surface:      #ffffff;
            --sf-text:         #222;
            --sf-muted:        #757575;
            --sf-border:       #ececec;
            --bs-primary: var(--sf-primary);
            --bs-link-color: var(--sf-primary);
            --bs-link-hover-color: var(--sf-primary-dark);
        }
        body { font-family: 'Inter', 'Noto Sans Myanmar', system-ui, sans-serif; color: var(--sf-text); background: var(--sf-bg); }
        a { text-decoration: none; }
        .sf-muted { color: var(--sf-muted) !important; }
        .text-primary { color: var(--sf-primary) !important; }
        .btn-primary { --bs-btn-bg: var(--sf-primary); --bs-btn-border-color: var(--sf-primary); --bs-btn-hover-bg: var(--sf-primary-dark); --bs-btn-hover-border-color: var(--sf-primary-dark); --bs-btn-active-bg: var(--sf-primary-dark); }
        .btn-outline-primary { --bs-btn-color: var(--sf-primary); --bs-btn-border-color: var(--sf-primary); --bs-btn-hover-bg: var(--sf-primary); --bs-btn-hover-border-color: var(--sf-primary); }
        .btn { font-weight: 600; }

        /* Top bar + header */
        .sf-topbar { background: var(--sf-primary-dark); color: #fff; font-size: .8rem; }
        .sf-topbar a { color: #fff; opacity: .9; }
        .sf-header { background: var(--sf-primary); color: #fff; }
        .sf-header .navbar-brand { color: #fff !important; font-weight: 800; letter-spacing: .3px; font-size: 1.4rem; }
        .sf-search .form-control { border: none; }
        .sf-search .btn { background: var(--sf-primary-dark); color: #fff; border: none; }
        .sf-cart { color: #fff; position: relative; font-size: 1.5rem; }
        .sf-cart .badge { position: absolute; top: -6px; right: -10px; background: #fff; color: var(--sf-primary); font-size: .62rem; border-radius: 999px; }
        .sf-navlink { color: #fff; opacity: .95; font-weight: 500; font-size: .9rem; }
        .sf-navlink:hover { color: #fff; opacity: 1; text-decoration: underline; }

        /* Product cards (commerce cards can reuse .bz-card; storefront styles both) */
        .bz-card, .sf-card { background: #fff; border: 1px solid var(--sf-border); border-radius: 4px; overflow: hidden; transition: box-shadow .12s ease, transform .12s ease; height: 100%; }
        .bz-card:hover, .sf-card:hover { box-shadow: 0 .35rem 1rem rgba(0,0,0,.12); transform: translateY(-2px); border-color: var(--sf-primary); }
        .bz-card .fw-bold, .sf-price { color: var(--sf-primary) !important; }

        .sf-section { padding: 1.5rem 0; }
        .sf-panel { background: #fff; border-radius: 4px; padding: 1rem 1.1rem; }
        .sf-panel-title { font-size: .95rem; text-transform: uppercase; letter-spacing: .04em; color: var(--sf-muted); font-weight: 700; margin: 0; }
        .sf-cat { display: flex; flex-direction: column; align-items: center; gap: .5rem; padding: 1rem .5rem; background: #fff; border: 1px solid var(--sf-border); border-radius: 6px; color: var(--sf-text); text-align: center; height: 100%; }
        .sf-cat:hover { border-color: var(--sf-primary); color: var(--sf-primary); }
        .sf-cat i { font-size: 1.6rem; color: var(--sf-primary); }
        .sf-cat span { font-size: .8rem; }

        .bp-content { line-height: 1.75; } .bp-content img { max-width: 100%; height: auto; }

        footer.sf-footer { background: #fff; border-top: 3px solid var(--sf-primary); color: var(--sf-muted); }
        footer.sf-footer h6 { color: var(--sf-text); font-size: .85rem; text-transform: uppercase; letter-spacing: .04em; }
        footer.sf-footer a { color: var(--sf-muted); }
        footer.sf-footer a:hover { color: var(--sf-primary); }
        .sf-social a { width: 36px; height: 36px; display:inline-flex; align-items:center; justify-content:center; border-radius:50%; background: var(--sf-bg); color: var(--sf-primary); }

        @media (prefers-reduced-motion: reduce) { .bz-card, .sf-card { transition: none; } }
        :focus-visible { outline: 3px solid color-mix(in srgb, var(--sf-primary) 45%, transparent); outline-offset: 2px; }
    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    @include('theme.storefront.layouts.header')
    <main class="flex-grow-1">
        @yield('content')
    </main>
    @include('theme.storefront.layouts.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
