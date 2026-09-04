<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $siteName = trim((string) bp_option('biz_shop_name')) ?: (optional(site_information('blogname'))->option_value ?: config('app.name'));
        $accent = bp_option('biz_accent', '#b0803f');
    @endphp
    <title>@hasSection('title')@yield('title') — {{ $siteName }}@else{{ $siteName }}@endif</title>
    @php $favOpt = trim((string) bp_option('biz_favicon')); @endphp
    @if ($favOpt !== '')
        <link rel="icon" href="{{ \Illuminate\Support\Str::startsWith($favOpt, ['http', '/']) ? $favOpt : bp_upload_url($favOpt) }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Source Serif 4: broad and institutional, where Georgia was only ever a fallback.
         One family, so the page costs a single webfont on a slow connection. --}}
    <link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:opsz,wght@8..60,400;8..60,600;8..60,700&family=Noto+Serif+Myanmar:wght@500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --accent: {{ $accent }};
            --accent-deep: color-mix(in srgb, {{ $accent }} 74%, #2b1d08);
            --accent-wash: color-mix(in srgb, {{ $accent }} 8%, #fff);
            --accent-edge: color-mix(in srgb, {{ $accent }} 26%, #fff);

            --ink: #241f19;
            --ink-2: #4a4239;
            --muted: #8a8175;
            --line: #ece5da;
            --rule-soft: #f2ece2;
            --bg: #faf8f4;
            --surface: #ffffff;
            --jade: #2f7d5b;
            --danger: #b0402f;

            --lift-1: 0 1px 2px rgba(43,38,32,.05), 0 2px 8px -2px rgba(43,38,32,.06);
            --lift-2: 0 1px 2px rgba(43,38,32,.06), 0 14px 32px -12px rgba(43,38,32,.22);

            --wrap: 1040px;
            --gutter: 20px;
        }
        * { box-sizing: border-box; }
        html { -webkit-text-size-adjust: 100%; }
        body { margin: 0; background: var(--bg); color: var(--ink);
               font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans Myanmar", sans-serif;
               font-size: 16px; line-height: 1.6; -webkit-font-smoothing: antialiased; }

        h1, h2, h3, .serif { font-family: "Source Serif 4", "Noto Serif Myanmar", Georgia, serif; }
        h1, h2, h3 { margin: 0; font-weight: 700; line-height: 1.14; letter-spacing: -0.012em; }
        .display { font-size: clamp(33px, 5.2vw, 51px); line-height: 1.05; letter-spacing: -0.02em; }
        .h2 { font-size: clamp(21px, 2.5vw, 27px); }
        .h3 { font-size: 19px; }
        .lead { font-size: clamp(16px, 1.5vw, 19px); color: var(--ink-2); max-width: 48ch; }
        p { margin: 0 0 1em; max-width: 68ch; }
        .muted { color: var(--muted); }
        .small { font-size: 14px; }
        .jade { color: var(--jade); }
        .money { font-variant-numeric: tabular-nums; font-feature-settings: "tnum" 1; font-weight: 700; }
        a { color: var(--accent-deep); text-decoration: none; }
        a:hover { text-decoration: underline; text-underline-offset: 3px; }
        :focus-visible { outline: 2.5px solid var(--accent); outline-offset: 2px; border-radius: 3px; }

        .wrap { max-width: var(--wrap); margin: 0 auto; padding: 0 var(--gutter); }
        .band { width: 100%; }
        main { display: block; padding-bottom: 68px; }
        .section { margin-top: 54px; }
        .head-row { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 6px; }
        .rule { height: 1px; border: 0; margin: 0 0 20px;
                background: linear-gradient(90deg, var(--line), var(--rule-soft) 55%, transparent); }

        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: 0;
               border-radius: 11px; padding: 12px 22px; cursor: pointer; font: inherit; font-weight: 600;
               background: var(--accent); color: #fff; text-decoration: none; box-shadow: var(--lift-1);
               transition: background .16s ease, box-shadow .16s ease, transform .08s ease; }
        .btn:hover { background: var(--accent-deep); text-decoration: none; box-shadow: var(--lift-2); }
        .btn:active { transform: translateY(1px); box-shadow: var(--lift-1); }
        .btn.sec { background: var(--surface); color: var(--ink); border: 1px solid var(--line); font-weight: 500; box-shadow: none; }
        .btn.sec:hover { background: #fff; border-color: var(--ink-2); box-shadow: var(--lift-1); }
        .btn.big { width: 100%; padding: 14px; }
        .btn[disabled] { opacity: .45; cursor: not-allowed; box-shadow: none; }
        .btn[disabled]:hover { background: var(--accent); }

        .card { background: var(--surface); border: 1px solid var(--rule-soft); border-radius: 16px; box-shadow: var(--lift-1); }
        .notice { border-radius: 12px; padding: 13px 16px; margin: 0 0 20px; }
        .notice.err { background: #fbeeeb; border: 1px solid #f0cabf; color: var(--danger); }
        .mark { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; }
        .mark .dot { width: 7px; height: 7px; border-radius: 50%; background: var(--accent); flex: 0 0 auto; }
        .mark.is-jade .dot { background: var(--jade); }

        /* Header */
        header.site { background: color-mix(in srgb, var(--bg) 76%, #fff); border-bottom: 1px solid var(--line);
                      position: sticky; top: 0; z-index: 20; backdrop-filter: saturate(1.3) blur(10px); }
        header.site .bar { display: flex; align-items: center; gap: 24px; padding: 15px 0; }
        .brand { font-size: 23px; font-weight: 700; color: var(--ink); letter-spacing: -0.02em; }
        .brand:hover { text-decoration: none; }
        header.site nav { display: flex; gap: 20px; margin-left: 2px; }
        header.site nav a { color: var(--ink-2); font-weight: 500; }
        header.site nav a:hover { color: var(--ink); }
        .spacer { margin-left: auto; }
        .cart-link { color: var(--ink); font-weight: 600; font-variant-numeric: tabular-nums; }
        #biz-account .dropdown { position: relative; display: inline-block; }
        #biz-account .menu { position: absolute; right: 0; top: 148%; background: var(--surface);
                border: 1px solid var(--line); border-radius: 14px; min-width: 200px; padding: 10px;
                box-shadow: var(--lift-2); display: none; }
        #biz-account .menu.open { display: block; animation: biz-drop .16s ease both; }
        #biz-account .menu a, #biz-account .menu .row2 { display: block; padding: 8px 10px; color: var(--ink); border-radius: 9px; }
        #biz-account .menu a:hover { background: var(--bg); text-decoration: none; }
        #biz-account button.link { background: none; border: 0; font: inherit; color: var(--ink); cursor: pointer; font-weight: 600; padding: 0; }
        @keyframes biz-drop { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: none; } }

        /* Hero — the one loud moment, full-bleed so it reads as the shopfront. */
        .hero { position: relative; overflow: hidden; border-bottom: 1px solid var(--line);
                background: radial-gradient(115% 130% at 85% -20%, var(--accent-wash), transparent 56%),
                            linear-gradient(180deg, #fff, var(--bg)); }
        .hero .inner { padding: clamp(50px, 7.5vw, 92px) 0 clamp(38px, 5vw, 58px); }
        .hero .cta-row { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; margin-top: 28px; }
        .reveal > * { animation: biz-rise .7s cubic-bezier(.2,.7,.3,1) both; }
        .reveal > *:nth-child(2) { animation-delay: .07s; }
        .reveal > *:nth-child(3) { animation-delay: .14s; }
        @keyframes biz-rise { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }

        /* Two-material product card: paper body, a solid tinted foot carrying price and action. */
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(236px, 1fr)); gap: 20px; }
        .product { display: flex; flex-direction: column; overflow: hidden; }
        .product .body { padding: 20px 20px 16px; display: flex; flex-direction: column; gap: 4px; flex: 1; }
        .product .pname { font-family: "Source Serif 4", "Noto Serif Myanmar", Georgia, serif;
                          font-size: 19px; font-weight: 600; line-height: 1.25; }
        .product .psku { font-size: 12.5px; color: var(--muted); font-variant-numeric: tabular-nums; }
        .product .foot { background: var(--accent-wash); border-top: 1px solid var(--accent-edge);
                         padding: 14px 20px 16px; display: flex; flex-direction: column; gap: 11px; }
        .product .price { font-size: 19px; color: var(--ink); }

        /* Ledger rows — used for order lines and the shop details. */
        .rows { display: grid; }
        .rows .r { display: flex; justify-content: space-between; align-items: baseline; gap: 16px;
                   padding: 13px 0; border-bottom: 1px solid var(--rule-soft); }
        .rows .r:last-child { border-bottom: 0; }
        .rows .k { color: var(--muted); font-size: 14px; }
        .rows .v { font-weight: 600; text-align: right; }

        .steps { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 26px; counter-reset: step; }
        .step { counter-increment: step; padding-top: 42px; position: relative; }
        .step::before { content: counter(step); position: absolute; top: 0; left: 0; font-size: 15px; font-weight: 700;
                        color: var(--accent-deep); width: 30px; height: 30px; border-radius: 50%; display: grid;
                        place-items: center; border: 1.5px solid var(--accent-edge); background: var(--accent-wash); }
        .step p { color: var(--ink-2); font-size: 15px; margin: 5px 0 0; max-width: 34ch; }

        .two-col { display: grid; grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr); gap: 24px; align-items: start; }

        main { padding-top: 0; }
        footer.site { border-top: 1px solid var(--line); background: var(--surface); }
        footer.site .inner { padding: 30px 0; color: var(--muted); font-size: 14px;
                             display: flex; justify-content: space-between; gap: 14px; flex-wrap: wrap; }

        @media (max-width: 780px) { .two-col { grid-template-columns: 1fr; } }
        @media (max-width: 520px) {
            :root { --gutter: 16px; }
            header.site .bar { gap: 14px; }
            header.site nav { gap: 14px; }
            .section { margin-top: 42px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>
</head>
<body>
    @include('theme.doeh-business.layouts.header')
    <main>
        @if ($errors->any())
            <div class="wrap" style="padding-top:22px;"><div class="notice err">{{ $errors->first() }}</div></div>
        @endif
        @yield('content')
    </main>
    @include('theme.doeh-business.layouts.footer')
</body>
</html>
