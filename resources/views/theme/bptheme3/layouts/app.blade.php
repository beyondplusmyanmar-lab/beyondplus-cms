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

    {{-- Terra — a minimal, editorial theme. Sans-only type, generous air, a single
         sage accent. Bootstrap 5 is used only for the grid + a few utilities. --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Noto+Sans+Myanmar:wght@400;500;600&display=swap">
    <style>
        :root {
            --tr-paper: #f6f6f1;        /* cool stone, not cream */
            --tr-ink: #23261f;
            --tr-sage: #6f7d5a;         /* the single accent */
            --tr-sage-dk: #566047;
            --tr-muted: #7d7f74;
            --tr-line: #e3e2d8;
            --bs-primary: #6f7d5a; --bs-primary-rgb: 111,125,90;
            --bs-link-color: #566047; --bs-link-hover-color: #6f7d5a;
        }
        body { background: var(--tr-paper); color: var(--tr-ink);
            font-family: "Inter", system-ui, -apple-system, "Noto Sans Myanmar", sans-serif;
            line-height: 1.65; }
        h1,h2,h3,h4,h5,.tr-display { font-family:"Sora","Noto Sans Myanmar",sans-serif; letter-spacing:-.02em; font-weight:600; }
        a { text-decoration:none; color:var(--tr-ink); }
        a:hover { color:var(--tr-sage-dk); }
        .text-primary { color: var(--tr-sage) !important; }
        .tr-muted { color: var(--tr-muted) !important; }

        /* Oversized lowercase section label with a sage tick */
        .tr-label { font-family:"Sora",sans-serif; font-size:.82rem; font-weight:600; letter-spacing:.02em;
            text-transform:lowercase; color:var(--tr-muted); display:inline-flex; align-items:center; gap:.5rem; }
        .tr-label::before { content:""; width:1.4rem; height:2px; background:var(--tr-sage); display:inline-block; }

        /* Animated underline on links that matter */
        .tr-ul { background-image:linear-gradient(var(--tr-sage),var(--tr-sage)); background-size:0% 2px;
            background-position:0 100%; background-repeat:no-repeat; transition:background-size .3s ease; padding-bottom:2px; }
        .tr-ul:hover { background-size:100% 2px; }

        /* Navbar — quiet, rule under it */
        .tr-nav { background: rgba(246,246,241,.9); backdrop-filter: blur(8px); border-bottom:1px solid var(--tr-line); }
        .tr-nav .navbar-brand { font-family:"Sora",sans-serif; font-weight:700; letter-spacing:-.02em; color:var(--tr-ink); }
        .tr-nav .nav-link { color:var(--tr-ink); font-weight:500; font-size:.95rem; }
        .tr-nav .nav-link:hover { color:var(--tr-sage-dk); }

        /* Post index rows — hairline separated, date on the left */
        .tr-row { border-top:1px solid var(--tr-line); }
        .tr-row:last-child { border-bottom:1px solid var(--tr-line); }
        .tr-row a.tr-row-link { display:block; padding:1.6rem 0; transition:padding-left .25s ease; }
        .tr-row a.tr-row-link:hover { padding-left:.5rem; }
        .tr-date { font-family:"Sora",sans-serif; font-size:.8rem; color:var(--tr-muted); letter-spacing:.02em; }
        .tr-row-title { font-size:clamp(1.25rem,2.4vw,1.7rem); font-weight:600; line-height:1.2; }
        .tr-thumb { width:100%; aspect-ratio:16/10; object-fit:cover; border-radius:4px; }

        .tr-cat { font-family:"Sora",sans-serif; font-size:.72rem; font-weight:600; letter-spacing:.06em;
            text-transform:uppercase; color:var(--tr-sage-dk); }

        .btn-tr { background:var(--tr-ink); color:var(--tr-paper); border:0; border-radius:2px; font-weight:500;
            padding:.6rem 1.4rem; transition:background .2s ease; }
        .btn-tr:hover { background:var(--tr-sage-dk); color:#fff; }
        .btn-tr-ghost { background:transparent; color:var(--tr-ink); border:1px solid var(--tr-ink); border-radius:2px; font-weight:500; padding:.55rem 1.3rem; }
        .btn-tr-ghost:hover { background:var(--tr-ink); color:var(--tr-paper); }
        .btn-primary { --bs-btn-bg:var(--tr-sage); --bs-btn-border-color:var(--tr-sage); --bs-btn-hover-bg:var(--tr-sage-dk); --bs-btn-hover-border-color:var(--tr-sage-dk); }

        .form-control { background:#fff; border-color:var(--tr-line); border-radius:2px; }
        .form-control:focus { border-color:var(--tr-sage); box-shadow:0 0 0 .2rem rgba(111,125,90,.15); }

        .bp-content { font-size:1.1rem; line-height:1.85; max-width:42rem; }
        .bp-content img { max-width:100%; height:auto; border-radius:4px; }
        .bp-content h2,.bp-content h3 { margin-top:1.8rem; }
        hr { border-color:var(--tr-line); opacity:1; }

        footer.tr-footer { border-top:1px solid var(--tr-line); color:var(--tr-muted); }
        footer.tr-footer a { color:var(--tr-ink); } footer.tr-footer a:hover { color:var(--tr-sage-dk); }

        :focus-visible { outline:2px solid var(--tr-sage); outline-offset:3px; }
        @media (prefers-reduced-motion: reduce) { .tr-row a.tr-row-link:hover { padding-left:0; } }
    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    @include('theme.bptheme3.layouts.header')

    <main class="flex-grow-1">
        @yield('content')
    </main>

    @include('theme.bptheme3.layouts.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
