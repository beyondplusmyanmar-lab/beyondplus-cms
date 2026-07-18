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
            /* Alias the tokens the Commerce plugin's cards use so they inherit
               the storefront palette instead of the Business theme's defaults. */
            --bz-primary: var(--sf-primary);
            --bz-surface: var(--sf-bg);
            --bz-muted:   var(--sf-muted);
            --bz-border:  var(--sf-border);
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

        /* ── Shopee-style hero: carousel + side promo tiles ── */
        .sf-hero-main { border-radius:6px; overflow:hidden; }
        .sf-hero-slide { min-height:340px; padding:2rem; color:#fff; display:flex; align-items:center;
            background:linear-gradient(120deg, var(--sf-primary), var(--sf-accent)); }
        .sf-hero-slide.has-img { background-size:cover; background-position:center; }
        .sf-hero-title { font-size:clamp(1.6rem,3.4vw,2.6rem); font-weight:800; line-height:1.1; }
        .sf-hero-side { display:flex; flex-direction:column; gap:.75rem; height:100%; }
        .sf-hero-tile { flex:1; border-radius:6px; padding:1.1rem 1.25rem; color:#fff; display:flex;
            flex-direction:column; justify-content:center; min-height:110px; transition:filter .15s ease; }
        .sf-hero-tile:hover { filter:brightness(1.05); color:#fff; }
        .sf-hero-tile .t { font-weight:800; font-size:1.05rem; }
        .sf-hero-tile i { font-size:1.4rem; }
        .sf-hero-main .carousel-indicators [data-bs-target] { width:8px; height:8px; border-radius:50%; border:0; }
        .sf-hero-main .carousel-control-prev, .sf-hero-main .carousel-control-next { width:6%; }

        /* ── Shopee-style flash sale ── */
        .sf-flash { background:#fff; border-radius:4px; overflow:hidden; }
        .sf-flash-bar { display:flex; align-items:center; gap:1rem; flex-wrap:wrap;
            padding:.85rem 1.1rem; border-bottom:1px solid var(--sf-border); }
        .sf-flash-logo { font-weight:800; font-style:italic; letter-spacing:.01em; color:var(--sf-primary);
            font-size:1.35rem; text-transform:uppercase; display:inline-flex; align-items:center; gap:.4rem; }
        .sf-countdown { display:inline-flex; align-items:center; gap:.28rem; }
        .sf-countdown .u { background:#2b2b2b; color:#fff; font-weight:700; border-radius:3px;
            padding:.14rem .38rem; min-width:1.75rem; text-align:center; font-variant-numeric:tabular-nums; font-size:.95rem; }
        .sf-countdown .sep { color:#2b2b2b; font-weight:800; }
        .sf-flash-link { margin-left:auto; font-size:.85rem; font-weight:600; color:var(--sf-primary); }

        /* ── Trending keywords under the search bar ── */
        .sf-trend { display:flex; flex-wrap:wrap; gap:.15rem .95rem; }
        .sf-trend a { color:#fff; opacity:.82; font-size:.76rem; font-weight:400; }
        .sf-trend a:hover { opacity:1; text-decoration:underline; }

        /* ── Circular category tiles (Shopee look) ── */
        .sf-cat i { width:3rem; height:3rem; border-radius:50%;
            background: color-mix(in srgb, var(--sf-primary) 12%, #fff); display:flex; align-items:center; justify-content:center; }
        .sf-cat { border:0; }
        .sf-cat:hover i { background: color-mix(in srgb, var(--sf-primary) 20%, #fff); }

        /* ── Service / guarantee strip ── */
        .sf-services { display:grid; grid-template-columns:repeat(2,1fr); gap:.75rem; }
        @media (min-width:768px){ .sf-services { grid-template-columns:repeat(4,1fr); } }
        .sf-service { display:flex; align-items:center; gap:.6rem; color:var(--sf-text); font-size:.85rem; }
        .sf-service i { color:var(--sf-primary); font-size:1.35rem; }

        /* ── Shopee-tight product cards (plugin emits .bz-card) ── */
        .bz-card, .sf-card { border-radius:2px; }
        .bz-card:hover, .sf-card:hover { border-color:var(--sf-primary); box-shadow:0 .25rem .9rem rgba(238,77,45,.16); }
        .bz-card h6, .sf-card h6 { font-size:.82rem; line-height:1.3; font-weight:400;
            display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; min-height:2.1rem; }
        .bz-card .fw-bold, .sf-price { font-size:1.05rem; }
        .sf-onsale { position:absolute; top:0; right:0; background:var(--sf-accent); color:#7a4a00;
            font-size:.66rem; font-weight:800; padding:.12rem .35rem; border-bottom-left-radius:4px; }

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
