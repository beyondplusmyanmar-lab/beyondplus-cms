<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $siteName = trim((string) bp_option('sv_name')) ?: (optional(site_information('blogname'))->option_value ?: config('app.name'));
        $brand = bp_option('sv_brand', '#5e8168');
    @endphp
    <title>@hasSection('title')@yield('title') · {{ $siteName }}@else{{ $siteName }}@endif</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Spectral: a calm transitional serif — the "care / by appointment" voice. Body stays system. --}}
    <link href="https://fonts.googleapis.com/css2?family=Spectral:ital,wght@0,500;0,600;1,500&family=Noto+Serif+Myanmar:wght@500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand: {{ $brand }};
            --brand-deep: #46664f;
            --brand-tint: color-mix(in srgb, {{ $brand }} 12%, #fff);
            --price: #bc6a45;
            --ink: #23302a;
            --muted: #7c8378;
            --line: #e3e4db;
            --bg: #f4f3ed;
            --surface: #ffffff;
            --danger: #a4503a;
        }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg); color: var(--ink);
               font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans Myanmar", sans-serif;
               line-height: 1.6; }
        .sv-serif, h1, h2, h3 { font-family: "Spectral", "Noto Serif Myanmar", Georgia, serif; font-weight: 600; letter-spacing: 0; margin: 0; }
        a { color: var(--brand-deep); text-decoration: none; }
        a:hover { text-decoration: underline; }
        .sv-eyebrow { font-size: 12px; letter-spacing: 0.16em; text-transform: uppercase; color: var(--brand-deep); font-weight: 600; }
        .sv-wrap { max-width: 780px; margin: 0 auto; padding: 0 22px; }
        .sv-muted { color: var(--muted); }
        .sv-price { font-family: "Spectral", Georgia, serif; font-variant-numeric: tabular-nums; color: var(--price); }

        .sv-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: 0; border-radius: 12px;
                  padding: 11px 22px; font: inherit; font-weight: 600; cursor: pointer; background: var(--brand); color: #fff;
                  text-decoration: none; transition: background .15s, transform .05s; }
        .sv-btn:hover { background: var(--brand-deep); text-decoration: none; }
        .sv-btn:active { transform: translateY(1px); }
        .sv-btn.ghost { background: transparent; color: var(--ink); border: 1px solid var(--line); font-weight: 500; }
        .sv-btn.ghost:hover { background: #fff; }
        .sv-btn.sm { padding: 8px 16px; font-size: 14px; }
        .sv-btn.block { width: 100%; padding: 14px; }
        .sv-btn[disabled] { opacity: .5; cursor: not-allowed; }
        :focus-visible { outline: 3px solid color-mix(in srgb, var(--brand) 45%, transparent); outline-offset: 2px; }

        .sv-card { background: var(--surface); border: 1px solid var(--line); border-radius: 18px; }
        .sv-chip { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 600;
                   padding: 3px 10px; border-radius: 999px; background: var(--brand-tint); color: var(--brand-deep); }
        .sv-notice { border-radius: 12px; padding: 13px 16px; margin: 0 0 20px; }
        .sv-notice.err { background: color-mix(in srgb, var(--price) 9%, #fff); border: 1px solid color-mix(in srgb, var(--price) 28%, #fff); color: var(--danger); }

        /* Header */
        .sv-head { background: color-mix(in srgb, var(--bg) 82%, #fff); border-bottom: 1px solid var(--line); position: sticky; top: 0; z-index: 30; backdrop-filter: saturate(1.05) blur(6px); }
        .sv-head .row { display: flex; align-items: center; gap: 22px; padding-block: 16px; }
        .sv-brand { font-family: "Spectral","Noto Serif Myanmar",Georgia,serif; font-weight: 600; font-size: 22px; color: var(--ink); }
        .sv-brand:hover { text-decoration: none; }
        .sv-nav { display: flex; gap: 18px; }
        .sv-nav a { color: var(--ink); font-weight: 500; }
        .sv-spacer { margin-left: auto; }
        .sv-req { color: var(--ink); font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }
        .sv-badge { background: var(--brand); color: #fff; border-radius: 999px; font-size: 12px; padding: 1px 8px; font-variant-numeric: tabular-nums; }
        #sv-account .drop { position: relative; }
        #sv-account .menu { position: absolute; right: 0; top: 134%; background: #fff; border: 1px solid var(--line);
                 border-radius: 14px; min-width: 200px; padding: 10px; box-shadow: 0 14px 42px rgba(35,48,42,.14); display: none; }
        #sv-account .menu.open { display: block; }
        #sv-account .menu .line { padding: 8px 10px; }
        #sv-account .menu a { display: block; padding: 8px 10px; color: var(--ink); border-radius: 9px; }
        #sv-account .menu a:hover { background: var(--bg); text-decoration: none; }
        #sv-account button.link { background: none; border: 0; font: inherit; font-weight: 600; color: var(--ink); cursor: pointer; padding: 0; }

        /* Service list — the signature: spacious, calm rows */
        .sv-svc { display: flex; align-items: center; gap: 18px; padding: 24px 0; border-bottom: 1px solid var(--line); }
        .sv-svc:last-child { border-bottom: 0; }
        .sv-svc .s-name { font-family: "Spectral","Noto Serif Myanmar",Georgia,serif; font-size: 21px; font-weight: 600; }
        .sv-svc .s-note { color: var(--muted); font-size: 13px; margin-top: 2px; }
        .sv-svc .s-mid { flex: 1 1 auto; }
        .sv-svc .s-price { font-size: 19px; white-space: nowrap; }

        main { padding: 40px 0 72px; }
        .sv-foot { border-top: 1px solid var(--line); }
        .sv-foot .inner { padding-block: 28px; color: var(--muted); font-size: 14px; display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; }

        @media (max-width: 560px) {
            .sv-head .row { gap: 12px; }
            .sv-nav { gap: 12px; }
            .sv-svc { flex-wrap: wrap; gap: 10px; }
        }
        @media (prefers-reduced-motion: reduce) { * { transition: none !important; } }
    </style>
</head>
<body>
    @include('theme.doeh-service.layouts.header')
    <main>
        <div class="sv-wrap">
            @if ($errors->any())
                <div class="sv-notice err">{{ $errors->first() }}</div>
            @endif
            @yield('content')
        </div>
    </main>
    @include('theme.doeh-service.layouts.footer')
</body>
</html>
