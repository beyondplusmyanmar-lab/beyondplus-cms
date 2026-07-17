<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $siteName = trim((string) bp_option('rt_name')) ?: (optional(site_information('blogname'))->option_value ?: config('app.name'));
        $brand = bp_option('rt_brand', '#2e4ce0');
    @endphp
    <title>@hasSection('title')@yield('title') — {{ $siteName }}@else{{ $siteName }}@endif</title>
    @php $favOpt = trim((string) bp_option('rt_favicon')); @endphp
    @if ($favOpt !== '')
        <link rel="icon" href="{{ \Illuminate\Support\Str::startsWith($favOpt, ['http', '/']) ? $favOpt : bp_upload_url($favOpt) }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Space Grotesk: a modern grotesk with distinctive tabular figures — retail/price voice. --}}
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Noto+Sans+Myanmar:wght@500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand: {{ $brand }};
            --brand-deep: #2038b8;
            --brand-tint: color-mix(in srgb, {{ $brand }} 10%, #fff);
            --money: #0e9f6e;
            --deal: #f59e0b;
            --ink: #171a1f;
            --muted: #6b7280;
            --line: #e6e8ec;
            --bg: #f6f7f9;
            --surface: #ffffff;
            --danger: #dc2626;
        }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg); color: var(--ink);
               font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans Myanmar", sans-serif;
               line-height: 1.5; }
        .rt-display, h1, h2, h3 { font-family: "Space Grotesk", "Noto Sans Myanmar", system-ui, sans-serif; font-weight: 700; letter-spacing: -0.02em; margin: 0; }
        .rt-price { font-family: "Space Grotesk", system-ui, sans-serif; font-variant-numeric: tabular-nums; font-weight: 700; }
        a { color: var(--brand-deep); text-decoration: none; }
        a:hover { text-decoration: underline; }
        .rt-wrap { max-width: 1080px; margin: 0 auto; padding: 0 20px; }
        .rt-muted { color: var(--muted); }

        .rt-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: 0; border-radius: 10px;
                  padding: 11px 20px; font: inherit; font-weight: 600; cursor: pointer; background: var(--brand); color: #fff;
                  text-decoration: none; transition: background .14s, transform .05s; }
        .rt-btn:hover { background: var(--brand-deep); text-decoration: none; }
        .rt-btn:active { transform: translateY(1px); }
        .rt-btn.ghost { background: var(--surface); color: var(--ink); border: 1px solid var(--line); font-weight: 500; }
        .rt-btn.ghost:hover { background: #fff; border-color: #d7dbe2; }
        .rt-btn.sm { padding: 8px 14px; font-size: 14px; }
        .rt-btn.block { width: 100%; padding: 14px; }
        .rt-btn[disabled] { opacity: .5; cursor: not-allowed; }
        :focus-visible { outline: 3px solid color-mix(in srgb, var(--brand) 40%, transparent); outline-offset: 2px; }

        .rt-card { background: var(--surface); border: 1px solid var(--line); border-radius: 16px; }
        .rt-chip { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 600;
                   padding: 3px 9px; border-radius: 999px; }
        .rt-chip.pickup { background: color-mix(in srgb, var(--deal) 14%, #fff); color: #92600a; }
        .rt-chip.stock { background: color-mix(in srgb, var(--money) 12%, #fff); color: #0b7a54; }
        .rt-notice { border-radius: 12px; padding: 12px 15px; margin: 0 0 20px; }
        .rt-notice.err { background: #fdecec; border: 1px solid #f5c2c2; color: var(--danger); }

        /* Header */
        .rt-head { background: rgba(255,255,255,.85); border-bottom: 1px solid var(--line); position: sticky; top: 0; z-index: 30; backdrop-filter: saturate(1.1) blur(8px); }
        .rt-head .row { display: flex; align-items: center; gap: 22px; padding-block: 14px; }
        .rt-brand { font-family: "Space Grotesk", system-ui, sans-serif; font-weight: 700; font-size: 21px; color: var(--ink); letter-spacing: -0.02em; }
        .rt-brand:hover { text-decoration: none; }
        .rt-nav { display: flex; gap: 18px; }
        .rt-nav a { color: var(--ink); font-weight: 500; }
        .rt-spacer { margin-left: auto; }
        .rt-cart { color: var(--ink); font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }
        .rt-badge { background: var(--brand); color: #fff; border-radius: 999px; font-size: 12px; padding: 1px 8px; font-variant-numeric: tabular-nums; }
        #rt-account .drop { position: relative; }
        #rt-account .menu { position: absolute; right: 0; top: 134%; background: #fff; border: 1px solid var(--line);
                 border-radius: 14px; min-width: 200px; padding: 10px; box-shadow: 0 16px 44px rgba(23,26,31,.14); display: none; }
        #rt-account .menu.open { display: block; }
        #rt-account .menu .line { padding: 8px 10px; }
        #rt-account .menu a { display: block; padding: 8px 10px; color: var(--ink); border-radius: 9px; }
        #rt-account .menu a:hover { background: var(--bg); text-decoration: none; }
        #rt-account button.link { background: none; border: 0; font: inherit; font-weight: 600; color: var(--ink); cursor: pointer; padding: 0; }

        /* Product grid + card — the signature */
        .rt-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 18px; }
        .rt-grid.rt-compact { grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 14px; }
        .rt-prod { display: flex; flex-direction: column; overflow: hidden; }
        .rt-prod .swatch { aspect-ratio: 4 / 3; display: grid; place-items: center;
                 background: linear-gradient(135deg, var(--brand-tint), #fff);
                 font-family: "Space Grotesk", serif; font-weight: 700; font-size: 44px; color: var(--brand); }
        .rt-prod .body { padding: 14px 16px 16px; display: flex; flex-direction: column; gap: 8px; flex: 1; }
        .rt-prod .pname { font-weight: 600; font-family: "Space Grotesk", system-ui, sans-serif; }
        .rt-prod .psku { font-size: 12px; color: var(--muted); }
        .rt-prod .prow { display: flex; align-items: baseline; justify-content: space-between; gap: 10px; margin-top: 2px; }
        .rt-prod .price { font-size: 20px; color: var(--ink); }
        .rt-prod form { margin-top: auto; }

        main { padding: 32px 0 64px; }
        .rt-foot { border-top: 1px solid var(--line); background: var(--surface); }
        .rt-foot .inner { padding-block: 26px; color: var(--muted); font-size: 14px; display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; }

        @media (max-width: 520px) { .rt-head .row { gap: 12px; } .rt-nav { gap: 12px; } }
        @media (prefers-reduced-motion: reduce) { * { transition: none !important; } }
    </style>
</head>
<body>
    @include('theme.doeh-retail.layouts.header')
    <main>
        <div class="rt-wrap">
            @if ($errors->any())
                <div class="rt-notice err">{{ $errors->first() }}</div>
            @endif
            @yield('content')
        </div>
    </main>
    @include('theme.doeh-retail.layouts.footer')
</body>
</html>
