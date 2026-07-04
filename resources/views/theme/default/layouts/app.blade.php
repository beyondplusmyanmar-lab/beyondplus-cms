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

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+Myanmar:wght@400;500;600&display=swap">
    <style>
        :root {
            --bp-accent: #0d9488;        /* teal brand — aligned with the admin */
            --bp-accent-dark: #0f766e;
            --bs-primary: #0d9488;
            --bs-primary-rgb: 13,148,136;
            --bs-link-color: #0d9488;
            --bs-link-hover-color: #0f766e;
        }
        body { font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans Myanmar", sans-serif; color: #1f2937; }
        a { text-decoration: none; }
        .text-primary { color: var(--bp-accent) !important; }
        .btn-primary { --bs-btn-bg: var(--bp-accent); --bs-btn-border-color: var(--bp-accent); --bs-btn-hover-bg: var(--bp-accent-dark); --bs-btn-hover-border-color: var(--bp-accent-dark); }
        .btn-outline-primary { --bs-btn-color: var(--bp-accent); --bs-btn-border-color: var(--bp-accent); --bs-btn-hover-bg: var(--bp-accent); --bs-btn-hover-border-color: var(--bp-accent); }
        .navbar-brand { font-weight: 700; letter-spacing: .3px; }
        .bp-hero {
            background: linear-gradient(135deg, var(--bp-accent), #0f172a);
            color: #fff; padding: 5rem 0;
        }
        /* Language toggle (matches the teal brand) */
        .bp-lang { display:inline-flex; border:1px solid #cbd5e1; border-radius:999px; overflow:hidden; }
        .bp-lang__opt { padding:.24rem .7rem; font-size:.8rem; font-weight:600; color:#475569; line-height:1.5; }
        .bp-lang__opt:hover { background:#f1f5f9; color:var(--bp-accent-dark); }
        .bp-lang__opt.active { background:var(--bp-accent); color:#fff; }
        .bp-hero h1 { font-weight: 800; }
        .bp-slider .bp-slide-img { height: 480px; object-fit: cover; }
        .bp-slider .carousel-item { position: relative; }
        .bp-slider .carousel-item::after { content: ''; position: absolute; inset: 0;
            background: linear-gradient(180deg, rgba(15,23,42,.10) 0%, rgba(15,23,42,.15) 45%, rgba(15,23,42,.72) 100%); }
        .bp-slider .carousel-caption { bottom: 22%; z-index: 3; text-shadow: 0 2px 10px rgba(0,0,0,.45); }
        @media (max-width: 768px) { .bp-slider .bp-slide-img { height: 320px; } }
        .bp-card { transition: transform .15s ease, box-shadow .15s ease; border: 1px solid #eef0f3; }
        .bp-card:hover { transform: translateY(-4px); box-shadow: 0 .75rem 1.5rem rgba(0,0,0,.08); }
        .bp-card .card-img-top { aspect-ratio: 16 / 10; object-fit: cover; }
        .bp-section-title { font-weight: 700; }
        footer.bp-footer { background: #0f172a; color: #cbd5e1; }
        footer.bp-footer a { color: #e2e8f0; }
        footer.bp-footer a:hover { color: #fff; }
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
