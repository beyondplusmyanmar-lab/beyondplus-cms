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

    {{-- Pulse — a bold, vibrant theme. Gradient mesh, rounded shapes, a Poppins
         display face. Bootstrap 5 for the grid; the energy is in the CSS below. --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&family=Noto+Sans+Myanmar:wght@400;500;600&display=swap">
    <style>
        :root {
            --pl-ink: #1c1130;
            --pl-coral: #ff5a5f;
            --pl-orange: #ff9a3c;
            --pl-pink: #ff4d9d;
            --pl-indigo: #6d5efc;
            --pl-muted: #6b6482;
            --pl-soft: #f6f4ff;         /* soft lilac surface */
            --pl-line: #ece9f7;
            --pl-grad: linear-gradient(100deg,#6d5efc 0%,#ff4d9d 55%,#ff9a3c 100%);
            --bs-primary: #6d5efc; --bs-primary-rgb: 109,94,252;
            --bs-link-color: #6d5efc; --bs-link-hover-color: #ff4d9d;
        }
        body { background:#fff; color:var(--pl-ink);
            font-family:"Inter", system-ui, -apple-system, "Noto Sans Myanmar", sans-serif; line-height:1.6; }
        h1,h2,h3,h4,h5,.pl-display { font-family:"Poppins","Noto Sans Myanmar",sans-serif; font-weight:700; letter-spacing:-.02em; }
        a { text-decoration:none; color:var(--pl-indigo); }
        a:hover { color:var(--pl-pink); }
        .text-primary { color:var(--pl-indigo) !important; }
        .pl-muted { color:var(--pl-muted) !important; }
        .pl-grad-text { background:var(--pl-grad); -webkit-background-clip:text; background-clip:text; color:transparent; }

        .pl-eyebrow { display:inline-block; font-family:"Poppins",sans-serif; font-size:.74rem; font-weight:600;
            letter-spacing:.08em; text-transform:uppercase; color:var(--pl-indigo);
            background:var(--pl-soft); border:1px solid var(--pl-line); padding:.3rem .8rem; border-radius:999px; }

        /* Gradient-mesh hero */
        .pl-hero { position:relative; overflow:hidden; border-radius:32px; background:var(--pl-soft); }
        .pl-hero::before, .pl-hero::after { content:""; position:absolute; border-radius:50%; filter:blur(60px); opacity:.55; }
        .pl-hero::before { width:26rem; height:26rem; top:-8rem; right:-6rem; background:radial-gradient(circle,var(--pl-pink),transparent 70%); }
        .pl-hero::after  { width:24rem; height:24rem; bottom:-9rem; left:-6rem; background:radial-gradient(circle,var(--pl-indigo),transparent 70%); }

        /* Navbar */
        .pl-nav { background:rgba(255,255,255,.85); backdrop-filter:blur(12px); border-bottom:1px solid var(--pl-line); }
        .pl-nav .navbar-brand { font-family:"Poppins",sans-serif; font-weight:800; }
        .pl-nav .nav-link { color:var(--pl-ink); font-weight:600; font-size:.95rem; border-radius:999px; padding:.4rem .9rem !important; }
        .pl-nav .nav-link:hover { color:var(--pl-indigo); background:var(--pl-soft); }

        /* Chunky rounded cards */
        .pl-card { background:#fff; border:1px solid var(--pl-line); border-radius:24px; overflow:hidden;
            transition:transform .2s ease, box-shadow .2s ease; height:100%; }
        .pl-card:hover { transform:translateY(-6px); box-shadow:0 22px 45px -20px rgba(109,94,252,.5); }
        .pl-card .pl-img { aspect-ratio:16/10; object-fit:cover; width:100%; }

        /* Colorful category pills — hashed by name so a category keeps its colour */
        .pl-pill { display:inline-block; font-family:"Poppins",sans-serif; font-size:.7rem; font-weight:600;
            letter-spacing:.03em; color:#fff; padding:.22rem .7rem; border-radius:999px; }
        .pl-pill.c0{background:var(--pl-coral);} .pl-pill.c1{background:var(--pl-indigo);}
        .pl-pill.c2{background:var(--pl-pink);}  .pl-pill.c3{background:var(--pl-orange);}
        .pl-pill.c4{background:#12b5a5;}

        /* Buttons */
        .btn-pl { background:var(--pl-grad); background-size:150% 100%; border:0; border-radius:999px; color:#fff;
            font-family:"Poppins",sans-serif; font-weight:600; padding:.7rem 1.7rem; transition:background-position .3s ease, box-shadow .3s ease, transform .15s ease; }
        .btn-pl:hover { color:#fff; background-position:100% 0; box-shadow:0 12px 28px -8px rgba(255,77,157,.55); transform:translateY(-1px); }
        .btn-pl-soft { background:var(--pl-soft); border:1px solid var(--pl-line); border-radius:999px; color:var(--pl-indigo);
            font-family:"Poppins",sans-serif; font-weight:600; padding:.65rem 1.5rem; }
        .btn-pl-soft:hover { color:var(--pl-pink); border-color:var(--pl-pink); }
        .btn-primary { --bs-btn-bg:var(--pl-indigo); --bs-btn-border-color:var(--pl-indigo); --bs-btn-hover-bg:#5b4de0; --bs-btn-hover-border-color:#5b4de0; --bs-btn-border-radius:999px; }

        .form-control { border-color:var(--pl-line); border-radius:14px; }
        .form-control:focus { border-color:var(--pl-indigo); box-shadow:0 0 0 .2rem rgba(109,94,252,.18); }

        .bp-content { font-size:1.08rem; line-height:1.8; }
        .bp-content img { max-width:100%; height:auto; border-radius:16px; }
        .bp-content h2,.bp-content h3 { margin-top:1.6rem; }
        hr { border-color:var(--pl-line); opacity:1; }

        .pagination { --bs-pagination-color:var(--pl-indigo); --bs-pagination-border-color:var(--pl-line);
            --bs-pagination-hover-bg:var(--pl-soft); --bs-pagination-active-bg:var(--pl-indigo); --bs-pagination-active-border-color:var(--pl-indigo);
            --bs-pagination-border-radius:999px; }

        footer.pl-footer { background:var(--pl-ink); color:#c9c2e0; border-radius:32px 32px 0 0; }
        footer.pl-footer a { color:#e7e2f7; } footer.pl-footer a:hover { color:var(--pl-orange); }

        :focus-visible { outline:2px solid var(--pl-indigo); outline-offset:2px; }
        @media (prefers-reduced-motion: reduce) { .pl-card:hover,.btn-pl:hover { transform:none; } }
    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    @include('theme.bptheme4.layouts.header')

    <main class="flex-grow-1">
        @yield('content')
    </main>

    @include('theme.bptheme4.layouts.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
