<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="dark">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

    @php $siteName = optional(site_information('blogname'))->option_value ?: config('app.name'); @endphp
    <title>@hasSection('title')@yield('title') — {{ $siteName }}@else{{ $siteName }}@endif</title>

    {{-- Nocturne — a dark, glassmorphic theme. Bootstrap 5 for layout;
         the identity is the aurora backdrop and frosted-glass panels below. --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Noto+Sans+Myanmar:wght@400;500;600&display=swap">
    <style>
        :root {
            --nc-bg: #0a0711;           /* near-black violet */
            --nc-bg2: #0f0a1c;
            --nc-panel: rgba(255,255,255,.045);
            --nc-panel-2: rgba(255,255,255,.07);
            --nc-border: rgba(168,85,247,.18);
            --nc-violet: #a855f7;
            --nc-cyan: #22d3ee;
            --nc-text: #ece9f6;
            --nc-muted: #9c93b8;
            --bs-primary: #a855f7; --bs-primary-rgb: 168,85,247;
            --bs-body-color: #ece9f6;
            --bs-link-color: #c4b5fd; --bs-link-hover-color: #22d3ee;
        }
        html { scroll-behavior: smooth; }
        body {
            background: var(--nc-bg); color: var(--nc-text);
            font-family: "Inter", system-ui, -apple-system, "Noto Sans Myanmar", sans-serif;
            position: relative; overflow-x: hidden;
            -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; text-rendering: optimizeLegibility;
        }
        /* Aurora backdrop — two soft orbs, fixed behind everything */
        body::before, body::after {
            content:""; position: fixed; z-index:-1; border-radius:50%; filter: blur(90px); opacity:.5; pointer-events:none;
        }
        body::before { width:44vw; height:44vw; top:-12vw; left:-8vw; background: radial-gradient(circle, #7c3aed, transparent 68%);
            animation: nc-drift1 26s ease-in-out infinite alternate; }
        body::after  { width:40vw; height:40vw; bottom:-14vw; right:-10vw; background: radial-gradient(circle, #0891b2, transparent 68%); opacity:.4;
            animation: nc-drift2 32s ease-in-out infinite alternate; }
        @keyframes nc-drift1 { from { transform: translate(0,0); } to { transform: translate(6vw,4vw); } }
        @keyframes nc-drift2 { from { transform: translate(0,0); } to { transform: translate(-5vw,-3vw); } }

        h1,h2,h3,h4,h5,.nc-display { font-family:"Space Grotesk","Noto Sans Myanmar",sans-serif; letter-spacing:-.01em; }
        a { text-decoration:none; color:#c4b5fd; }
        a:hover { color: var(--nc-cyan); }
        .text-primary { color: var(--nc-violet) !important; }
        .text-muted, .nc-muted { color: var(--nc-muted) !important; }

        .nc-eyebrow { font-size:.72rem; font-weight:600; letter-spacing:.18em; text-transform:uppercase;
            background:linear-gradient(90deg,var(--nc-violet),var(--nc-cyan)); -webkit-background-clip:text; background-clip:text; color:transparent; }
        .nc-gradient-text { background:linear-gradient(100deg,#fff 10%,#c4b5fd 45%,#22d3ee 90%);
            -webkit-background-clip:text; background-clip:text; color:transparent; }

        /* Frosted glass — the signature surface */
        .nc-glass { background: var(--nc-panel); border:1px solid var(--nc-border); border-radius:16px;
            backdrop-filter: blur(14px) saturate(1.2); -webkit-backdrop-filter: blur(14px) saturate(1.2); }
        .nc-card { background: var(--nc-panel); border:1px solid var(--nc-border); border-radius:16px; overflow:hidden;
            backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease; }
        .nc-card:hover { transform: translateY(-5px); border-color: rgba(168,85,247,.55);
            box-shadow: 0 12px 40px -12px rgba(124,58,237,.55); }
        .nc-card .nc-img { aspect-ratio:16/10; object-fit:cover; width:100%; }
        .nc-badge { display:inline-block; font-size:.68rem; font-weight:600; letter-spacing:.06em; text-transform:uppercase;
            color:#e9d5ff; background:rgba(168,85,247,.18); border:1px solid rgba(168,85,247,.4); padding:.16rem .55rem; border-radius:999px; }

        /* Hero spotlight — the lead post as a full-bleed glass feature */
        .nc-spotlight { position:relative; border-radius:20px; overflow:hidden; border:1px solid var(--nc-border);
            background:var(--nc-panel); backdrop-filter:blur(14px); transition:border-color .2s ease, box-shadow .2s ease; }
        .nc-spotlight:hover { border-color:rgba(168,85,247,.55); box-shadow:0 20px 60px -20px rgba(124,58,237,.6); }
        .nc-spotlight img { width:100%; aspect-ratio:16/11; object-fit:cover; display:block; }
        .nc-spotlight .nc-shade { position:absolute; inset:0;
            background:linear-gradient(0deg,rgba(6,4,12,.94) 6%,rgba(6,4,12,.45) 42%,transparent 72%); }
        .nc-spotlight .nc-shade-body { position:absolute; left:0; right:0; bottom:0; padding:1.6rem 1.75rem; }
        .nc-spotlight-empty { display:flex; align-items:center; justify-content:center; min-height:340px;
            background:radial-gradient(120% 120% at 20% 10%, rgba(124,58,237,.35), transparent 60%),
                       radial-gradient(120% 120% at 90% 90%, rgba(34,211,238,.22), transparent 55%), var(--nc-bg2); }

        /* Topic chips — real categories, shows the taxonomy */
        .nc-chip { display:inline-flex; align-items:center; gap:.4rem; font-size:.82rem; color:#d7cfe9;
            background:var(--nc-panel-2); border:1px solid var(--nc-border); border-radius:999px; padding:.32rem .85rem; transition:.2s ease; }
        .nc-chip:hover { color:#fff; border-color:var(--nc-violet); background:rgba(168,85,247,.16); }

        /* Closing call-to-action panel */
        .nc-cta { border:1px solid var(--nc-border); border-radius:24px; overflow:hidden; position:relative;
            background:linear-gradient(120deg, rgba(124,58,237,.20), rgba(34,211,238,.10)); backdrop-filter:blur(12px); }

        /* Navbar */
        .nc-nav { background: rgba(10,7,17,.72); backdrop-filter: blur(16px); border-bottom:1px solid var(--nc-border); }
        .nc-nav .navbar-brand { font-family:"Space Grotesk",sans-serif; font-weight:700; }
        .nc-nav .nav-link { color:#d7cfe9 !important; font-weight:500; }
        .nc-nav .nav-link:hover { color:#fff !important; }
        .nc-brand-dot { display:inline-block; width:.55rem; height:.55rem; border-radius:50%;
            background:linear-gradient(135deg,var(--nc-violet),var(--nc-cyan)); box-shadow:0 0 12px var(--nc-violet); margin-right:.5rem; vertical-align:middle; }

        /* Buttons */
        .btn-nc { border:0; border-radius:999px; font-weight:600; color:#fff; padding:.6rem 1.5rem;
            background:linear-gradient(100deg,#7c3aed,#a855f7 55%,#22d3ee); background-size:180% 100%; transition: background-position .3s ease, box-shadow .3s ease; }
        .btn-nc:hover { color:#fff; background-position:100% 0; box-shadow:0 8px 30px -8px rgba(168,85,247,.7); }
        .btn-nc-ghost { border:1px solid var(--nc-border); border-radius:999px; color:#d7cfe9; padding:.55rem 1.35rem; background:transparent; }
        .btn-nc-ghost:hover { color:#fff; border-color:var(--nc-violet); background:rgba(168,85,247,.12); }

        .form-control, .accordion-button, .input-group-text { background: var(--nc-panel-2); border-color: var(--nc-border); color: var(--nc-text); }
        .form-control::placeholder { color:#8b82a6; }
        .form-control:focus { background: var(--nc-panel-2); border-color: var(--nc-violet); color:var(--nc-text); box-shadow:0 0 0 .2rem rgba(168,85,247,.2); }

        .bp-content { color:#d9d4e8; line-height:1.85; font-size:1.06rem; }
        .bp-content a { color:var(--nc-cyan); }
        .bp-content img { max-width:100%; height:auto; border-radius:12px; }
        .bp-content h2,.bp-content h3 { color:#fff; margin-top:1.6rem; }

        hr { border-color: var(--nc-border); opacity:1; }
        .pagination { --bs-pagination-bg:transparent; --bs-pagination-color:#c4b5fd; --bs-pagination-border-color:var(--nc-border);
            --bs-pagination-hover-bg:rgba(168,85,247,.18); --bs-pagination-hover-color:#fff; --bs-pagination-hover-border-color:var(--nc-violet);
            --bs-pagination-active-bg:var(--nc-violet); --bs-pagination-active-border-color:var(--nc-violet);
            --bs-pagination-disabled-bg:transparent; --bs-pagination-disabled-color:#6b6484; }

        footer.nc-footer { border-top:1px solid var(--nc-border); background:rgba(15,10,28,.6); backdrop-filter:blur(10px); color:#b7aecd; }
        footer.nc-footer a { color:#d7cfe9; } footer.nc-footer a:hover { color:var(--nc-cyan); }

        :focus-visible { outline:2px solid var(--nc-cyan); outline-offset:2px; }
        @media (prefers-reduced-motion: reduce) { html { scroll-behavior:auto; } .nc-card { transition:none; }
            body::before, body::after { animation:none; } }

        /* ── Burmese typography fixes ──
           Wide tracking + all-caps break Myanmar glyph clusters (visible on the
           eyebrow). Neutralise them for the Myanmar locale; keep the gradient. */
        html[lang="mm"] .nc-eyebrow, html[lang="mm"] .nc-badge {
            letter-spacing: normal; text-transform: none; }
        html[lang="mm"] h1, html[lang="mm"] h2, html[lang="mm"] h3,
        html[lang="mm"] h4, html[lang="mm"] h5, html[lang="mm"] .nc-display {
            line-height: 1.4; letter-spacing: normal; }
        html[lang="mm"] .bp-content { line-height: 1.95; }
    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    @include('theme.nocturne.layouts.header')

    <main class="flex-grow-1">
        @yield('content')
    </main>

    @include('theme.nocturne.layouts.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
