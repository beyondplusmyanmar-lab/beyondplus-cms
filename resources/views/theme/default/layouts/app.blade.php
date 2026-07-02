<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    @php $siteName = optional(site_information('blogname'))->option_value ?: config('app.name'); @endphp
    <title>@hasSection('title')@yield('title') — {{ $siteName }}@else{{ $siteName }}@endif</title>

    <!-- Bootstrap 5 + icons (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root { --bp-accent: #2563eb; }
        body { font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; color: #1f2937; }
        a { text-decoration: none; }
        .navbar-brand { font-weight: 700; letter-spacing: .3px; }
        .bp-hero {
            background: linear-gradient(135deg, var(--bp-accent), #1e40af);
            color: #fff; padding: 5rem 0;
        }
        .bp-hero h1 { font-weight: 800; }
        .bp-slider .bp-slide-img { height: 480px; object-fit: cover; filter: brightness(0.45); }
        .bp-slider .carousel-caption { bottom: 20%; text-shadow: 0 2px 6px rgba(0,0,0,.5); }
        @media (max-width: 768px) { .bp-slider .bp-slide-img { height: 300px; } }
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
