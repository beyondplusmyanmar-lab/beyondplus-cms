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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&family=Noto+Sans+Myanmar:wght@400;500;600;700&display=swap">

    <style>
        /* ---- Design tokens (override any of these from the admin options) ---- */
        :root {
            --bz-primary:      {{ bp_option('theme_color_primary',   '#2563eb') }};
            --bz-primary-dark: {{ bp_option('theme_color_primary_dark', '#1d4ed8') }};
            --bz-secondary:    {{ bp_option('theme_color_secondary', '#0f172a') }};
            --bz-accent:       {{ bp_option('theme_color_accent',    '#f59e0b') }};
            --bz-success:      {{ bp_option('theme_color_success',   '#16a34a') }};
            --bz-warning:      {{ bp_option('theme_color_warning',   '#d97706') }};
            --bz-danger:       {{ bp_option('theme_color_danger',    '#dc2626') }};
            --bz-bg:           {{ bp_option('theme_color_bg',        '#ffffff') }};
            --bz-surface:      {{ bp_option('theme_color_surface',   '#f8fafc') }};
            --bz-text:         {{ bp_option('theme_color_text',      '#1f2937') }};
            --bz-muted:        {{ bp_option('theme_color_muted',     '#64748b') }};
            --bz-border:       {{ bp_option('theme_color_border',    '#e5e7eb') }};
            --bz-font-heading: {{ bp_option('theme_font_heading', "'Plus Jakarta Sans'") }}, "Noto Sans Myanmar", system-ui, sans-serif;
            --bz-font-body:    {{ bp_option('theme_font_body',    "'Inter'") }}, "Noto Sans Myanmar", system-ui, -apple-system, sans-serif;
            --bz-radius: 14px;
            --bz-shadow: 0 1px 2px rgba(15,23,42,.04), 0 8px 24px rgba(15,23,42,.06);

            /* Map Bootstrap primaries onto the brand token */
            --bs-primary: var(--bz-primary);
            --bs-link-color: var(--bz-primary);
            --bs-link-hover-color: var(--bz-primary-dark);
        }

        body { font-family: var(--bz-font-body); color: var(--bz-text); background: var(--bz-bg); }
        h1,h2,h3,h4,h5,.bz-display { font-family: var(--bz-font-heading); font-weight: 700; letter-spacing: -.01em; }
        a { text-decoration: none; }
        .bz-muted { color: var(--bz-muted) !important; }
        .text-primary { color: var(--bz-primary) !important; }
        .bz-eyebrow { text-transform: uppercase; letter-spacing: .12em; font-size: .78rem; font-weight: 700; color: var(--bz-primary); }
        .bz-section { padding: 4.5rem 0; }
        .bz-section--alt { background: var(--bz-surface); }
        .bz-section-head { max-width: 640px; margin: 0 auto 3rem; text-align: center; }
        .bz-section-head h2 { font-size: clamp(1.6rem, 3vw, 2.25rem); }

        .btn-primary { --bs-btn-bg: var(--bz-primary); --bs-btn-border-color: var(--bz-primary); --bs-btn-hover-bg: var(--bz-primary-dark); --bs-btn-hover-border-color: var(--bz-primary-dark); --bs-btn-active-bg: var(--bz-primary-dark); }
        .btn-outline-primary { --bs-btn-color: var(--bz-primary); --bs-btn-border-color: var(--bz-primary); --bs-btn-hover-bg: var(--bz-primary); --bs-btn-hover-border-color: var(--bz-primary); }
        .btn { border-radius: 10px; font-weight: 600; }
        .btn-lg { padding: .7rem 1.5rem; }

        .bz-card { background: #fff; border: 1px solid var(--bz-border); border-radius: var(--bz-radius); transition: transform .15s ease, box-shadow .15s ease; }
        .bz-card:hover { transform: translateY(-4px); box-shadow: var(--bz-shadow); }
        .bz-ico { width: 52px; height: 52px; display: inline-flex; align-items: center; justify-content: center;
                  border-radius: 12px; font-size: 1.4rem; color: var(--bz-primary);
                  background: color-mix(in srgb, var(--bz-primary) 12%, transparent); }

        /* Navbar */
        .bz-nav { background: rgba(255,255,255,.9); backdrop-filter: blur(8px); border-bottom: 1px solid var(--bz-border); }
        .navbar-brand { font-family: var(--bz-font-heading); font-weight: 800; color: var(--bz-secondary) !important; }
        .bz-nav .nav-link { font-weight: 500; color: var(--bz-text); }
        .bz-nav .nav-link:hover { color: var(--bz-primary); }
        .bz-lang { display:inline-flex; border:1px solid var(--bz-border); border-radius:999px; overflow:hidden; }
        .bz-lang__opt { padding:.24rem .7rem; font-size:.8rem; font-weight:600; color:var(--bz-muted); line-height:1.5; }
        .bz-lang__opt:hover { background:var(--bz-surface); color:var(--bz-primary); }
        .bz-lang__opt.active { background:var(--bz-primary); color:#fff; }

        /* Hero */
        .bz-hero { position: relative; background: linear-gradient(135deg, var(--bz-secondary), var(--bz-primary-dark)); color:#fff; padding: 5.5rem 0; overflow: hidden; }
        .bz-hero::after { content:''; position:absolute; inset:0; background: radial-gradient(60% 90% at 85% 10%, rgba(255,255,255,.14), transparent 60%); pointer-events:none; }
        .bz-hero h1 { font-size: clamp(2rem, 5vw, 3.4rem); font-weight: 800; line-height: 1.08; }
        .bz-hero .lead { font-size: 1.12rem; opacity: .9; }
        .bz-hero--img { background-size: cover; background-position: center; }
        .bz-hero--img::before { content:''; position:absolute; inset:0; background: linear-gradient(120deg, rgba(15,23,42,.86), rgba(15,23,42,.55)); }
        .bz-hero > .container { position: relative; z-index: 1; }

        /* Stats */
        .bz-stat { text-align:center; }
        .bz-stat__num { font-family: var(--bz-font-heading); font-weight: 800; font-size: clamp(1.9rem, 4vw, 2.8rem); color: var(--bz-primary); line-height: 1; }
        .bz-stat__label { color: var(--bz-muted); font-weight: 500; margin-top: .35rem; }

        /* Content (editor HTML) — shared contract across all themes */
        .bp-content { line-height: 1.75; }
        .bp-content img { max-width: 100%; height: auto; border-radius: 10px; }
        .bp-content h2, .bp-content h3 { margin-top: 1.6rem; }

        /* Footer */
        footer.bz-footer { background: var(--bz-secondary); color: #cbd5e1; }
        footer.bz-footer h6 { color:#fff; font-family: var(--bz-font-heading); }
        footer.bz-footer a { color: #cbd5e1; }
        footer.bz-footer a:hover { color: #fff; }
        .bz-social a { width:38px; height:38px; display:inline-flex; align-items:center; justify-content:center; border-radius:50%; background:rgba(255,255,255,.08); }
        .bz-social a:hover { background: var(--bz-primary); }

        @media (max-width: 768px) { .bz-section { padding: 3rem 0; } .bz-hero { padding: 3.5rem 0; } }
        @media (prefers-reduced-motion: reduce) { .bz-card, .btn { transition: none; } }
        :focus-visible { outline: 3px solid color-mix(in srgb, var(--bz-primary) 45%, transparent); outline-offset: 2px; }
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
