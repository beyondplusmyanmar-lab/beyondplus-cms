<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

    @php
        $siteName  = optional(site_information('blogname'))->option_value ?: config('app.name');
        $siteDesc  = optional(site_information('blogdescription'))->option_value ?: 'Quality products and professional services.';
        $metaDesc  = trim($__env->yieldContent('meta_description')) ?: $siteDesc;
        $ogImage   = bp_option('biz_og_image') ? bp_upload_url(bp_option('biz_og_image')) : asset('favicon.svg');
        $canonical = url()->current();
        $bizPhone  = bp_option('biz_phone');
        $socials   = array_filter([
            bp_option('biz_social_facebook'), bp_option('biz_social_twitter'),
            bp_option('biz_social_instagram'), bp_option('biz_social_youtube'),
            bp_option('biz_social_linkedin'), bp_option('biz_social_tiktok'),
        ]);
    @endphp

    <title>@hasSection('title')@yield('title') — {{ $siteName }}@else{{ $siteName }}@endif</title>
    <meta name="description" content="{{ $metaDesc }}">
    <link rel="canonical" href="{{ $canonical }}">

    {{-- Open Graph / Twitter --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="@hasSection('title')@yield('title')@else{{ $siteName }}@endif">
    <meta property="og:description" content="{{ $metaDesc }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta name="twitter:card" content="summary_large_image">

    {{-- Structured data: Organization --}}
    <script type="application/ld+json">
    {!! json_encode(array_filter([
        '@context'  => 'https://schema.org',
        '@type'     => 'Organization',
        'name'      => $siteName,
        'url'       => url('/'),
        'logo'      => $ogImage,
        'description' => $siteDesc,
        'telephone' => $bizPhone ?: null,
        'sameAs'    => array_values($socials) ?: null,
    ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    {{-- Bootstrap 5 + icons (CDN) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&family=Noto+Sans+Myanmar:wght@400;500;600;700&display=swap">

    <style>
        /* ---- Design tokens (override any of these from the admin options) ----
           Defaults matter: they are what an unconfigured site ships with. These
           moved off the generic blue/slate pairing onto pine, gold and ink. ---- */
        :root {
            --bz-primary:      {{ bp_option('theme_color_primary',   '#1d5c54') }};
            --bz-primary-dark: {{ bp_option('theme_color_primary_dark', '#14433d') }};
            --bz-secondary:    {{ bp_option('theme_color_secondary', '#14201e') }};
            --bz-accent:       {{ bp_option('theme_color_accent',    '#c08a2e') }};
            --bz-success:      {{ bp_option('theme_color_success',   '#2f7d5b') }};
            --bz-warning:      {{ bp_option('theme_color_warning',   '#b8791f') }};
            --bz-danger:       {{ bp_option('theme_color_danger',    '#b0402f') }};
            --bz-bg:           {{ bp_option('theme_color_bg',        '#ffffff') }};
            --bz-surface:      {{ bp_option('theme_color_surface',   '#f5f7f5') }};
            --bz-text:         {{ bp_option('theme_color_text',      '#17211f') }};
            --bz-muted:        {{ bp_option('theme_color_muted',     '#6b7671') }};
            --bz-border:       {{ bp_option('theme_color_border',    '#e3e8e5') }};
            --bz-font-heading: {{ bp_option('theme_font_heading', "'Plus Jakarta Sans'") }}, "Noto Sans Myanmar", system-ui, sans-serif;
            --bz-font-body:    {{ bp_option('theme_font_body',    "'Public Sans'") }}, "Noto Sans Myanmar", system-ui, -apple-system, sans-serif;

            /* Radius and elevation vary BY ROLE. One radius plus one shadow on every
               block is what flattens a page into a card kit. */
            --bz-radius: 14px;
            --bz-radius-sm: 10px;
            --bz-radius-lg: 20px;
            --bz-lift-1: 0 1px 2px rgba(20,32,30,.05), 0 2px 8px -2px rgba(20,32,30,.06);
            --bz-lift-2: 0 1px 2px rgba(20,32,30,.06), 0 16px 36px -14px rgba(20,32,30,.22);
            --bz-shadow: var(--bz-lift-1);   /* kept: older markup may reference it */

            --bs-primary: var(--bz-primary);
            --bs-link-color: var(--bz-primary-dark);
            --bs-link-hover-color: var(--bz-primary);
            --bs-body-color: var(--bz-text);
            --bs-border-color: var(--bz-border);
        }

        body { font-family: var(--bz-font-body); color: var(--bz-text); background: var(--bz-bg); line-height: 1.65; }
        h1,h2,h3,h4,h5,.bz-display { font-family: var(--bz-font-heading); font-weight: 700; letter-spacing: -.018em; line-height: 1.15; }
        p { max-width: 72ch; }
        a { text-decoration: none; }
        a:hover { text-decoration: underline; text-underline-offset: 3px; }
        .bz-muted { color: var(--bz-muted) !important; }
        .text-primary { color: var(--bz-primary) !important; }

        /* A label, not tracked-out caps. The caps treatment above every heading is
           template chrome and appears whatever the subject. */
        .bz-eyebrow { text-transform: none; letter-spacing: 0; font-size: .9rem; font-weight: 600; color: var(--bz-primary); }

        .bz-section { padding: 4.5rem 0; }
        .bz-section--alt { background: var(--bz-surface); }
        /* Left-aligned by default; centring everything is its own tell. Opt in per section. */
        .bz-section-head { max-width: 62ch; margin: 0 0 2.5rem; text-align: left; }
        .bz-section-head--center { margin-left: auto; margin-right: auto; text-align: center; }
        .bz-section-head h2 { font-size: clamp(1.6rem, 3vw, 2.25rem); }
        .bz-section-head p { margin-bottom: 0; color: var(--bz-muted); }
        .bz-rule { height: 1px; border: 0; margin: 0 0 2.25rem;
                   background: linear-gradient(90deg, var(--bz-border), color-mix(in srgb, var(--bz-border) 40%, transparent) 55%, transparent); }

        .btn-primary { --bs-btn-bg: var(--bz-primary); --bs-btn-border-color: var(--bz-primary); --bs-btn-hover-bg: var(--bz-primary-dark); --bs-btn-hover-border-color: var(--bz-primary-dark); --bs-btn-active-bg: var(--bz-primary-dark); }
        .btn-outline-primary { --bs-btn-color: var(--bz-primary-dark); --bs-btn-border-color: var(--bz-border); --bs-btn-hover-bg: var(--bz-primary); --bs-btn-hover-border-color: var(--bz-primary); }
        .btn { border-radius: var(--bz-radius-sm); font-weight: 600; box-shadow: none; transition: background-color .16s ease, box-shadow .16s ease, transform .08s ease; }
        .btn:hover { box-shadow: var(--bz-lift-1); }
        .btn:active { transform: translateY(1px); }
        .btn-lg { padding: .75rem 1.6rem; }

        /* Cards respond on hover; they do not move. A lift on every card reads as generated. */
        .bz-card { background: #fff; border: 1px solid var(--bz-border); border-radius: var(--bz-radius);
                   box-shadow: none; transition: box-shadow .18s ease, border-color .18s ease; }
        .bz-card:hover { box-shadow: var(--bz-lift-1); border-color: color-mix(in srgb, var(--bz-primary) 28%, var(--bz-border)); }
        .bz-card:focus-within { box-shadow: var(--bz-lift-1); border-color: var(--bz-primary); }
        .bz-ico { width: 52px; height: 52px; display: inline-flex; align-items: center; justify-content: center;
                  border-radius: var(--bz-radius-sm); font-size: 1.4rem; color: var(--bz-primary);
                  background: color-mix(in srgb, var(--bz-primary) 10%, transparent);
                  border: 1px solid color-mix(in srgb, var(--bz-primary) 18%, transparent); }

        /* Navbar */
        .bz-nav { background: color-mix(in srgb, var(--bz-bg) 82%, #fff); backdrop-filter: saturate(1.3) blur(10px); border-bottom: 1px solid var(--bz-border); }
        .navbar-brand { font-family: var(--bz-font-heading); font-weight: 800; color: var(--bz-secondary) !important; letter-spacing: -.03em; }
        .bz-nav .nav-link { font-weight: 500; color: var(--bz-text); }
        .bz-nav .nav-link:hover { color: var(--bz-primary); }
        .dropdown-menu { border-color: var(--bz-border); border-radius: var(--bz-radius); box-shadow: var(--bz-lift-2); padding: .4rem; }
        .dropdown-item { border-radius: var(--bz-radius-sm); }
        .bz-lang { display:inline-flex; border:1px solid var(--bz-border); border-radius:999px; overflow:hidden; }
        .bz-lang__opt { padding:.24rem .7rem; font-size:.8rem; font-weight:600; color:var(--bz-muted); line-height:1.5; }
        .bz-lang__opt:hover { background:var(--bz-surface); color:var(--bz-primary); text-decoration:none; }
        .bz-lang__opt.active { background:var(--bz-primary); color:#fff; }

        /* Hero: a deep ink ground with one wash of the brand, not a two-stop diagonal
           gradient. Text stays light, so existing hero markup is unaffected. */
        .bz-hero { position: relative; background: var(--bz-secondary); color:#fff; padding: 5.5rem 0; overflow: hidden; }
        .bz-hero::after { content:''; position:absolute; inset:0; pointer-events:none;
            background: radial-gradient(58% 85% at 82% 4%, color-mix(in srgb, var(--bz-primary) 46%, transparent), transparent 62%); }
        .bz-hero h1 { font-size: clamp(2rem, 5vw, 3.4rem); font-weight: 800; line-height: 1.06; letter-spacing: -.03em; }
        .bz-hero .lead { font-size: 1.12rem; color: rgba(255,255,255,.82); max-width: 52ch; }
        .bz-hero--img { background-size: cover; background-position: center; }
        .bz-hero--img::before { content:''; position:absolute; inset:0;
            background: linear-gradient(180deg, rgba(12,20,18,.32) 0%, rgba(12,20,18,.78) 100%); }
        .bz-hero > .container { position: relative; z-index: 1; }

        /* Stats: figures line up, so they are tabular. */
        .bz-stat { text-align:center; }
        .bz-stat__num { font-family: var(--bz-font-heading); font-weight: 800; font-size: clamp(1.9rem, 4vw, 2.8rem);
                        color: var(--bz-primary); line-height: 1; font-variant-numeric: tabular-nums; }
        .bz-stat__label { color: var(--bz-muted); font-weight: 500; margin-top: .35rem; }

        /* Content (editor HTML) — shared contract across all themes */
        .bp-content { line-height: 1.75; }
        .bp-content img { max-width: 100%; height: auto; border-radius: var(--bz-radius-sm); }
        .bp-content h2, .bp-content h3 { margin-top: 1.6rem; }
        .bp-content p { max-width: 72ch; }

        /* Footer */
        footer.bz-footer { background: var(--bz-secondary); color: #c3ccc9; }
        footer.bz-footer h6 { color:#fff; font-family: var(--bz-font-heading); }
        footer.bz-footer a { color: #d7dedb; }
        footer.bz-footer a:hover { color: #fff; }
        .bz-social a { width:38px; height:38px; display:inline-flex; align-items:center; justify-content:center;
                       border-radius:50%; background:rgba(255,255,255,.08); }
        .bz-social a:hover { background: var(--bz-primary); }

        @media (max-width: 768px) { .bz-section { padding: 3rem 0; } .bz-hero { padding: 3.5rem 0; } }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
        :focus-visible { outline: 2.5px solid var(--bz-primary); outline-offset: 2px; border-radius: 3px; }
    </style>

    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    @include('theme.business.layouts.header')

    <main class="flex-grow-1">
        @yield('content')
    </main>

    @include('theme.business.layouts.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
