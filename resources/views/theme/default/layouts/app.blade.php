<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

    @php $siteName = optional(site_information('blogname'))->option_value ?: config('app.name'); @endphp
    <title>@hasSection('title')@yield('title') — {{ $siteName }}@else{{ $siteName }}@endif</title>

    <!-- Bootstrap 5 + icons (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Literata for anything editorial: it was drawn for long-form reading, where the
         previous system-only stack left every heading looking unset. --}}
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Literata:opsz,wght@7..72,400;7..72,600;7..72,700&family=Noto+Sans+Myanmar:wght@400;500;600&display=swap">
    <style>
        :root {
            /* Teal stays — it is deliberately aligned with the admin. Everything around it changed. */
            --bp-accent: #0d9488;
            --bp-accent-deep: #0b6f66;
            --bp-accent-wash: #eef6f4;

            /* Neutrals carry a slight teal bias, so they read as chosen rather than default grey. */
            --bp-ink: #17211f;
            --bp-ink-2: #41504c;
            --bp-muted: #6e7c78;
            --bp-paper: #f6f7f6;
            --bp-surface: #ffffff;
            --bp-rule: #e2e7e4;

            --bp-lift-1: 0 1px 2px rgba(23,33,31,.05), 0 2px 8px -2px rgba(23,33,31,.06);
            --bp-lift-2: 0 1px 2px rgba(23,33,31,.06), 0 16px 36px -14px rgba(23,33,31,.2);

            /* Bridge into Bootstrap's own tokens so all 15 views inherit the system. */
            --bs-primary: #0d9488;
            --bs-primary-rgb: 13,148,136;
            --bs-link-color: var(--bp-accent-deep);
            --bs-link-hover-color: var(--bp-accent);
            --bs-body-color: var(--bp-ink);
            --bs-body-bg: var(--bp-paper);
            --bs-border-color: var(--bp-rule);
            --bs-border-radius: .75rem;
            --bs-border-radius-lg: 1rem;
            --bs-border-radius-sm: .5rem;
        }

        body { font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans Myanmar", sans-serif;
               color: var(--bp-ink); background: var(--bp-paper); line-height: 1.65; }
        a { text-decoration: none; }
        a:hover { text-decoration: underline; text-underline-offset: 3px; }
        :focus-visible { outline: 2.5px solid var(--bp-accent); outline-offset: 2px; border-radius: 3px; }

        h1, h2, h3, h4, h5, h6, .navbar-brand, .card-title, .bp-serif {
            font-family: "Literata", "Noto Serif Myanmar", Georgia, serif;
            letter-spacing: -0.012em; font-weight: 600; }
        h1, .display-4, .display-5 { letter-spacing: -0.022em; line-height: 1.08; }
        .lead { color: var(--bp-ink-2); }
        p { max-width: 72ch; }
        .text-primary { color: var(--bp-accent) !important; }
        .text-muted { color: var(--bp-muted) !important; }

        .btn { border-radius: .7rem; font-weight: 600; }
        .btn-primary { --bs-btn-bg: var(--bp-accent); --bs-btn-border-color: var(--bp-accent);
                       --bs-btn-hover-bg: var(--bp-accent-deep); --bs-btn-hover-border-color: var(--bp-accent-deep); }
        .btn-outline-primary { --bs-btn-color: var(--bp-accent-deep); --bs-btn-border-color: var(--bp-rule);
                               --bs-btn-hover-bg: var(--bp-accent); --bs-btn-hover-border-color: var(--bp-accent); }

        /* Navbar: a quiet rule and a serif wordmark, not a solid white slab. */
        .navbar { background: color-mix(in srgb, var(--bp-paper) 76%, #fff) !important;
                  backdrop-filter: saturate(1.3) blur(10px); border-bottom: 1px solid var(--bp-rule) !important; }
        .navbar-brand { font-size: 1.32rem; font-weight: 600; color: var(--bp-ink) !important; letter-spacing: -0.025em; }
        .navbar .nav-link { color: var(--bp-ink-2) !important; font-weight: 500; }
        .navbar .nav-link:hover, .navbar .nav-link.active { color: var(--bp-ink) !important; }
        .navbar .form-control { border-color: var(--bp-rule); background: var(--bp-surface); }
        .dropdown-menu { border-color: var(--bp-rule); border-radius: .85rem; box-shadow: var(--bp-lift-2); padding: .4rem; }
        .dropdown-item { border-radius: .55rem; }

        /* Language toggle */
        .bp-lang { display:inline-flex; border:1px solid var(--bp-rule); border-radius:999px; overflow:hidden; background:var(--bp-surface); }
        .bp-lang__opt { padding:.24rem .7rem; font-size:.8rem; font-weight:600; color:var(--bp-ink-2); line-height:1.5; }
        .bp-lang__opt:hover { background:var(--bp-accent-wash); color:var(--bp-accent-deep); text-decoration:none; }
        .bp-lang__opt.active { background:var(--bp-accent); color:#fff; }

        /* Masthead. The old teal-to-slate gradient is the commonest generated-page tell;
           this is paper, a wash of the brand, and a rule. */
        .bp-hero { background: radial-gradient(88% 120% at 50% -30%, var(--bp-accent-wash), transparent 62%),
                               linear-gradient(180deg, #fff, var(--bp-paper));
                   color: var(--bp-ink); border-bottom: 1px solid var(--bp-rule);
                   padding: clamp(54px, 8vw, 100px) 0; }
        .bp-hero h1 { font-weight: 700; }

        /* Slider: darken only the lower band so captions stay legible without flattening the image. */
        .bp-slider .bp-slide-img { height: clamp(320px, 46vw, 520px); object-fit: cover; }
        .bp-slider .carousel-item { position: relative; }
        .bp-slider .carousel-item::after { content:''; position:absolute; inset:0;
            background: linear-gradient(180deg, rgba(12,20,18,0) 38%, rgba(12,20,18,.68) 100%); }
        .bp-slider .carousel-caption { bottom: 12%; z-index: 3; text-align: left; left: 8%; right: 8%; }
        .bp-slider .carousel-caption h2 { font-size: clamp(24px, 3.4vw, 40px); font-weight: 700; text-shadow: 0 2px 14px rgba(0,0,0,.4); }
        .bp-slider .carousel-caption p { text-shadow: 0 1px 10px rgba(0,0,0,.45); max-width: 52ch; }

        /* Section headers: left-aligned with a rule that fades, not a centred title plus subtitle. */
        .bp-sec-head { display:flex; align-items:baseline; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:8px; }
        .bp-rule { height:1px; border:0; margin:0 0 28px;
                   background: linear-gradient(90deg, var(--bp-rule), color-mix(in srgb, var(--bp-rule) 40%, transparent) 55%, transparent); }

        /* Cards do not lift on hover — that transform on every card is template chrome.
           Hover changes what the link does, and nothing else moves. */
        .bp-card { border: 1px solid var(--bp-rule); background: var(--bp-surface); box-shadow: none;
                   border-radius: var(--bs-border-radius-lg); overflow: hidden;
                   transition: box-shadow .18s ease, border-color .18s ease; }
        .bp-card:hover { box-shadow: var(--bp-lift-1); border-color: color-mix(in srgb, var(--bp-accent) 30%, var(--bp-rule)); }
        .bp-card:focus-within { box-shadow: var(--bp-lift-1); border-color: var(--bp-accent); }
        .bp-card .card-img-top { aspect-ratio: 16 / 10; object-fit: cover; }
        .bp-card .card-title { font-size: 1.06rem; line-height: 1.35; }
        .bp-card .card-title a { color: var(--bp-ink); }
        .bp-card:hover .card-title a { text-decoration: underline; text-underline-offset: 3px; }
        .bp-card .card-footer { background: transparent !important; border-top: 1px solid var(--bp-rule) !important; }
        .bp-cat-badge { background: var(--bp-accent); color: #fff; font-weight: 600; letter-spacing: .01em; }

        .bp-section-title { font-weight: 600; }

        footer.bp-footer { background: var(--bp-ink); color: #c2cec9; }
        footer.bp-footer h5, footer.bp-footer h6 { color: #fff; }
        footer.bp-footer a { color: #dbe4e0; }
        footer.bp-footer a:hover { color: #fff; }
        footer.bp-footer .text-secondary { color: #9aa8a3 !important; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>

    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    @include('theme.default.layouts.header')

    <main class="flex-grow-1">
        @yield('content')
    </main>

    @include('theme.default.layouts.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
