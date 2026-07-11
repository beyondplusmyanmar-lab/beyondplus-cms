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

    {{-- Meridian — an editorial theme. Bootstrap 5 for the grid + utilities only;
         the personality lives in the type system and the masthead below. --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700;9..144,900&family=Inter:wght@400;500;600;700&family=Noto+Sans+Myanmar:wght@400;500;600&display=swap">
    <style>
        :root {
            --md-ink: #17130f;          /* near-black warm ink */
            --md-paper: #fbf8f3;        /* warm off-white */
            --md-burgundy: #7a1f2b;     /* masthead + accent */
            --md-burgundy-dk: #5c141d;
            --md-gold: #b8893b;         /* secondary accent, used sparingly */
            --md-muted: #6b6259;
            --md-rule: #e2dccf;
            --bs-primary: #7a1f2b; --bs-primary-rgb: 122,31,43;
            --bs-link-color: #7a1f2b; --bs-link-hover-color: #5c141d;
        }
        body { background: var(--md-paper); color: var(--md-ink);
            font-family: "Inter", system-ui, -apple-system, "Noto Sans Myanmar", sans-serif;
            font-size: 1.02rem; line-height: 1.6; }
        h1,h2,h3,h4,h5,.md-serif { font-family: "Fraunces","Noto Sans Myanmar",Georgia,serif; }
        a { text-decoration: none; color: var(--md-ink); }
        a:hover { color: var(--md-burgundy); }
        .text-primary { color: var(--md-burgundy) !important; }

        /* ── Kicker: the category eyebrow that heads every story ── */
        .md-kicker { font-family:"Inter",sans-serif; font-size:.72rem; font-weight:700;
            letter-spacing:.16em; text-transform:uppercase; color: var(--md-burgundy); }
        .md-kicker--muted { color: var(--md-muted); }

        /* ── Masthead ── */
        .md-topbar { background: var(--md-ink); color:#e9e2d6; font-size:.78rem; letter-spacing:.03em; }
        .md-topbar a { color:#e9e2d6; } .md-topbar a:hover { color:#fff; }
        .md-masthead { border-bottom: 3px double var(--md-ink); }
        .md-wordmark { font-family:"Fraunces",serif; font-weight:900; font-optical-sizing:auto;
            letter-spacing:-.01em; color: var(--md-ink); line-height:1; }
        .md-nav { border-bottom: 1px solid var(--md-rule); background: rgba(251,248,243,.92);
            backdrop-filter: saturate(1.2) blur(6px); }
        .md-nav .nav-link { font-family:"Inter",sans-serif; font-weight:600; font-size:.86rem;
            letter-spacing:.04em; text-transform:uppercase; color:var(--md-ink); padding:.85rem .9rem; position:relative; }
        .md-nav .nav-link:hover { color:var(--md-burgundy); }
        .md-nav .nav-link.active::after, .md-nav .nav-link:hover::after { content:""; position:absolute;
            left:.9rem; right:.9rem; bottom:.55rem; height:2px; background:var(--md-burgundy); }

        /* ── Lead story (the signature split) ── */
        .md-lead { border-top: 1px solid var(--md-rule); border-bottom: 1px solid var(--md-rule); }
        .md-lead-title { font-weight:600; line-height:1.05; letter-spacing:-.015em;
            font-size: clamp(2rem, 4.5vw, 3.4rem); }
        .md-lead-title a:hover { color: var(--md-burgundy); }
        .md-lead-img { aspect-ratio: 3/2; object-fit: cover; width:100%; }
        .md-hairline { border:0; border-top:1px solid var(--md-rule); }

        /* ── Story cards in the grid ── */
        .md-story-title { font-weight:600; line-height:1.12; letter-spacing:-.01em; }
        .md-story-title a:hover { color: var(--md-burgundy); }
        .md-story-img { aspect-ratio: 16/10; object-fit: cover; width:100%; }
        .md-story { padding-bottom:1.4rem; border-bottom:1px solid var(--md-rule); }
        .md-dek { color: var(--md-muted); }
        .md-byline { font-family:"Inter",sans-serif; font-size:.78rem; color:var(--md-muted);
            letter-spacing:.02em; }

        /* ── Article body / drop cap ── */
        .md-article { max-width: 42rem; }
        .md-article-title { font-weight:600; line-height:1.08; letter-spacing:-.015em; }
        .bp-content { font-size:1.12rem; line-height:1.8; }
        .bp-content > p:first-of-type::first-letter {
            font-family:"Fraunces",serif; font-weight:900; float:left; font-size:3.6rem;
            line-height:.82; padding:.1rem .5rem .1rem 0; color:var(--md-burgundy); }
        .bp-content img { max-width:100%; height:auto; border-radius:2px; }
        .bp-content h2,.bp-content h3 { margin-top:1.6rem; }

        /* ── Buttons / badges ── */
        .btn-primary { --bs-btn-bg:var(--md-burgundy); --bs-btn-border-color:var(--md-burgundy);
            --bs-btn-hover-bg:var(--md-burgundy-dk); --bs-btn-hover-border-color:var(--md-burgundy-dk); }
        .btn-outline-primary { --bs-btn-color:var(--md-burgundy); --bs-btn-border-color:var(--md-burgundy);
            --bs-btn-hover-bg:var(--md-burgundy); --bs-btn-hover-border-color:var(--md-burgundy); }
        .btn-md-ghost { border:1px solid var(--md-ink); border-radius:0; font-family:"Inter",sans-serif;
            font-weight:600; font-size:.78rem; letter-spacing:.08em; text-transform:uppercase;
            padding:.5rem 1.1rem; color:var(--md-ink); }
        .btn-md-ghost:hover { background:var(--md-ink); color:var(--md-paper); }
        .md-tag { display:inline-block; font-family:"Inter",sans-serif; font-size:.7rem; font-weight:700;
            letter-spacing:.12em; text-transform:uppercase; color:var(--md-burgundy);
            border:1px solid var(--md-burgundy); padding:.15rem .5rem; border-radius:999px; }

        /* language toggle (sits in the dark top bar) */
        .md-lang a { font-size:.76rem; font-weight:600; color:#cabfad; padding:0 .25rem; }
        .md-lang a.active { color:#fff; text-decoration:underline; text-underline-offset:3px; }

        /* sidebar */
        .md-aside-title { font-family:"Inter",sans-serif; font-size:.74rem; font-weight:700;
            letter-spacing:.14em; text-transform:uppercase; color:var(--md-muted);
            border-bottom:2px solid var(--md-ink); padding-bottom:.4rem; margin-bottom:.9rem; }

        footer.md-footer { background: var(--md-ink); color:#c9c0b3; }
        footer.md-footer a { color:#e9e2d6; } footer.md-footer a:hover { color:#fff; }
        footer.md-footer .md-wordmark { color:#fff; }

        @media (max-width:768px){
            .bp-content > p:first-of-type::first-letter { font-size:2.8rem; }
        }
        :focus-visible { outline:2px solid var(--md-burgundy); outline-offset:2px; }
    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    @include('theme.bptheme1.layouts.header')

    <main class="flex-grow-1">
        @yield('content')
    </main>

    @include('theme.bptheme1.layouts.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
