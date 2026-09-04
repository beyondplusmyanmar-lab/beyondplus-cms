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
    {{-- Space Grotesk carries the whole Latin voice: display, UI and — the point of a
         retail theme — prices, whose tabular figures line up down a column. --}}
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Noto+Sans+Myanmar:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            /* Signage: the merchant's colour, and the tints derived from it. */
            --brand: {{ $brand }};
            --brand-ink: color-mix(in srgb, {{ $brand }} 74%, #0a1420);
            --brand-wash: color-mix(in srgb, {{ $brand }} 6%, #fff);
            --brand-edge: color-mix(in srgb, {{ $brand }} 20%, #fff);

            /* Paper and ink. The ground is a shop wall, not a grey dashboard. */
            --paper: #eef1ec;
            --card: #ffffff;
            --ink: #16211d;
            --ink-2: #3c4a44;
            --muted: #6c7873;
            --rule: #dbe1da;
            --rule-soft: #e7ebe5;

            /* Reserved meanings: green is rewards, never decoration. */
            --money: #0b7f5a;
            --alert: #b23b2e;

            /* Elevation is warm and layered — a paper shadow, not a grey blur. */
            --lift-1: 0 1px 2px rgba(22,33,29,.05), 0 2px 8px -2px rgba(22,33,29,.06);
            --lift-2: 0 1px 2px rgba(22,33,29,.06), 0 14px 32px -12px rgba(22,33,29,.22);
            --inset-niche: inset 0 1px 0 rgba(255,255,255,.9), inset 0 -20px 34px -26px rgba(22,33,29,.35);

            --wrap: 1120px;
            --gutter: 20px;
        }

        * { box-sizing: border-box; }
        html { -webkit-text-size-adjust: 100%; }
        body {
            margin: 0; background: var(--paper); color: var(--ink);
            font-family: "Space Grotesk", "Noto Sans Myanmar", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            font-size: 16px; line-height: 1.6;
            font-feature-settings: "kern" 1;
            -webkit-font-smoothing: antialiased;
        }

        /* ---- Type scale ------------------------------------------------- */
        h1, h2, h3 { margin: 0; font-weight: 700; letter-spacing: -0.022em; line-height: 1.1; }
        .rt-display { font-size: clamp(34px, 5.4vw, 54px); letter-spacing: -0.032em; line-height: 1.03; }
        .rt-h2 { font-size: clamp(21px, 2.6vw, 27px); }
        .rt-h3 { font-size: 18px; letter-spacing: -0.015em; }
        .rt-lead { font-size: clamp(16px, 1.6vw, 19px); color: var(--ink-2); max-width: 46ch; line-height: 1.55; }
        p { margin: 0 0 1em; max-width: 68ch; }
        .rt-muted { color: var(--muted); }
        .rt-small { font-size: 14px; }
        .rt-micro { font-size: 12.5px; letter-spacing: .01em; }
        /* Money always lines up: tabular figures, never proportional. */
        .rt-money { font-variant-numeric: tabular-nums; font-feature-settings: "tnum" 1; font-weight: 700; letter-spacing: -0.015em; }

        a { color: var(--brand-ink); text-decoration: none; }
        a:hover { text-decoration: underline; text-underline-offset: 3px; }
        :focus-visible { outline: 2.5px solid var(--brand); outline-offset: 2px; border-radius: 3px; }

        /* ---- Layout ------------------------------------------------------ */
        .rt-wrap { max-width: var(--wrap); margin: 0 auto; padding: 0 var(--gutter); }
        .rt-band { width: 100%; }
        main { display: block; padding-bottom: 72px; }
        .rt-section { margin-top: 56px; }
        .rt-section:first-child { margin-top: 0; }
        /* A shelf rule: heavier at the left where the eye lands, fading right. */
        .rt-shelf-rule { height: 1px; border: 0; margin: 0 0 20px;
                         background: linear-gradient(90deg, var(--rule), var(--rule-soft) 55%, transparent); }
        .rt-two-col { display: grid; grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr); gap: 24px; align-items: start; }
        .rt-head-row { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 6px; }

        /* ---- Buttons ------------------------------------------------------ */
        .rt-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px;
                  border: 0; border-radius: 12px; padding: 12px 22px; font: inherit; font-weight: 600;
                  cursor: pointer; background: var(--brand); color: #fff; text-decoration: none;
                  box-shadow: var(--lift-1); transition: background .16s ease, box-shadow .16s ease, transform .08s ease; }
        .rt-btn:hover { background: var(--brand-ink); text-decoration: none; box-shadow: var(--lift-2); }
        .rt-btn:active { transform: translateY(1px); box-shadow: var(--lift-1); }
        .rt-btn.ghost { background: var(--card); color: var(--ink); border: 1px solid var(--rule); font-weight: 500; box-shadow: none; }
        .rt-btn.ghost:hover { background: #fff; border-color: var(--ink-2); box-shadow: var(--lift-1); }
        .rt-btn.sm { padding: 8px 15px; font-size: 14px; border-radius: 10px; }
        .rt-btn.block { width: 100%; }
        .rt-btn[disabled] { opacity: .45; cursor: not-allowed; box-shadow: none; }
        .rt-btn[disabled]:hover { background: var(--brand); }

        /* ---- Surfaces ----------------------------------------------------- */
        .rt-panel { background: var(--card); border: 1px solid var(--rule-soft); border-radius: 18px; box-shadow: var(--lift-1); }
        .rt-notice { border-radius: 12px; padding: 13px 16px; margin: 0 0 22px; }
        .rt-notice.err { background: #fbeceb; border: 1px solid #efc9c4; color: var(--alert); }
        .rt-mark { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; }
        .rt-mark .dot { width: 7px; height: 7px; border-radius: 50%; background: var(--brand); flex: 0 0 auto; }
        .rt-mark.is-stock .dot { background: var(--money); }

        /* ---- Header -------------------------------------------------------- */
        .rt-head { background: color-mix(in srgb, var(--paper) 78%, #fff); border-bottom: 1px solid var(--rule);
                   position: sticky; top: 0; z-index: 30; backdrop-filter: saturate(1.4) blur(10px); }
        .rt-head .row { display: flex; align-items: center; gap: 24px; padding-block: 15px; }
        .rt-brand { font-weight: 700; font-size: 21px; color: var(--ink); letter-spacing: -0.03em; }
        .rt-brand:hover { text-decoration: none; }
        .rt-nav { display: flex; gap: 20px; }
        .rt-nav a { color: var(--ink-2); font-weight: 500; }
        .rt-nav a:hover { color: var(--ink); }
        .rt-spacer { margin-left: auto; }
        .rt-cart { color: var(--ink); font-weight: 600; display: inline-flex; align-items: center; gap: 7px; }
        .rt-badge { background: var(--brand); color: #fff; border-radius: 999px; font-size: 12px;
                    padding: 1px 8px; font-variant-numeric: tabular-nums; font-weight: 700; }
        #rt-account .drop { position: relative; }
        #rt-account .menu { position: absolute; right: 0; top: 150%; background: #fff; border: 1px solid var(--rule);
                 border-radius: 15px; min-width: 212px; padding: 11px; box-shadow: var(--lift-2); display: none; }
        #rt-account .menu.open { display: block; animation: rt-drop .16s ease both; }
        #rt-account .menu .line { padding: 7px 10px; }
        #rt-account .menu a { display: block; padding: 8px 10px; color: var(--ink); border-radius: 9px; }
        #rt-account .menu a:hover { background: var(--paper); text-decoration: none; }
        #rt-account button.link { background: none; border: 0; font: inherit; font-weight: 600; color: var(--ink); cursor: pointer; padding: 0; }
        @keyframes rt-drop { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: none; } }

        /* ---- Hero: the one loud moment ------------------------------------- */
        .rt-hero { position: relative; overflow: hidden; background:
                   radial-gradient(120% 140% at 88% -20%, var(--brand-wash), transparent 58%),
                   linear-gradient(180deg, #fff, var(--paper)); border-bottom: 1px solid var(--rule); }
        .rt-hero .inner { padding: clamp(52px, 8vw, 96px) 0 clamp(40px, 5vw, 60px); display: grid;
                          grid-template-columns: minmax(0, 1.35fr) minmax(0, .65fr); gap: 40px; align-items: end; }
        .rt-hero .cta-row { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; margin-top: 30px; }
        /* Counter facts sit on a rule like a receipt column, not in pill chips. */
        .rt-facts { display: grid; gap: 0; border-top: 1px solid var(--rule); }
        .rt-facts .fact { display: flex; justify-content: space-between; align-items: baseline; gap: 14px;
                          padding: 13px 0; border-bottom: 1px solid var(--rule-soft); }
        .rt-facts .k { color: var(--muted); font-size: 13.5px; }
        .rt-facts .v { font-weight: 600; font-size: 14.5px; text-align: right; }
        .rt-reveal > * { animation: rt-rise .7s cubic-bezier(.2,.7,.3,1) both; }
        .rt-reveal > *:nth-child(2) { animation-delay: .07s; }
        .rt-reveal > *:nth-child(3) { animation-delay: .14s; }
        @keyframes rt-rise { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }

        /* ---- The shelf: product grid --------------------------------------- */
        .rt-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(232px, 1fr)); gap: 20px; }
        .rt-grid.rt-compact { grid-template-columns: repeat(auto-fill, minmax(176px, 1fr)); gap: 15px; }

        /* A ticket is not a panel: it has a recessed niche and a printed label edge. */
        .rt-ticket { display: flex; flex-direction: column; background: var(--card);
                     border: 1px solid var(--rule-soft); border-radius: 16px; overflow: hidden;
                     box-shadow: var(--lift-1); }
        .rt-ticket:focus-within { border-color: var(--brand-edge); box-shadow: var(--lift-2); }
        .rt-ticket .niche { aspect-ratio: 4 / 3; display: grid; place-items: center; position: relative;
                            background: linear-gradient(168deg, var(--brand-wash), #fff 62%);
                            box-shadow: var(--inset-niche); }
        .rt-ticket .monogram { font-size: clamp(38px, 7vw, 52px); font-weight: 700; letter-spacing: -0.04em;
                               color: color-mix(in srgb, var(--brand) 28%, #fff); }
        /* The shelf-edge label — the signature device. */
        .rt-ticket .label { border-top: 1px solid var(--rule-soft); padding: 13px 15px 15px;
                            display: flex; flex-direction: column; gap: 11px; flex: 1;
                            background: linear-gradient(180deg, #fff, color-mix(in srgb, var(--paper) 42%, #fff)); }
        .rt-ticket .pname { font-weight: 600; line-height: 1.3; letter-spacing: -0.012em; }
        .rt-ticket .psku { font-size: 12.5px; color: var(--muted); font-variant-numeric: tabular-nums; margin-top: 2px; }
        .rt-ticket .price-row { display: flex; align-items: flex-end; justify-content: space-between; gap: 10px; }
        .rt-ticket .price { font-size: 21px; line-height: 1; }
        .rt-ticket form { margin-top: auto; }
        .rt-compact .rt-ticket .price { font-size: 18px; }
        .rt-compact .rt-ticket .label { padding: 11px 12px 13px; gap: 9px; }

        /* ---- Steps: only numbered because pickup genuinely is a sequence ----- */
        .rt-steps { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 26px; counter-reset: step; }
        .rt-step { counter-increment: step; padding-top: 42px; position: relative; }
        .rt-step::before { content: counter(step); position: absolute; top: 0; left: 0;
                           font-size: 15px; font-weight: 700; color: var(--brand);
                           width: 30px; height: 30px; border-radius: 50%; display: grid; place-items: center;
                           border: 1.5px solid var(--brand-edge); background: var(--brand-wash); }
        .rt-step h3 { margin-bottom: 5px; }
        .rt-step p { color: var(--ink-2); font-size: 15px; margin: 0; max-width: 34ch; }

        /* ---- Store card ------------------------------------------------------ */
        .rt-visit { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 2px;
                    background: var(--rule-soft); border-radius: 18px; overflow: hidden; border: 1px solid var(--rule-soft); }
        .rt-visit .cell { background: var(--card); padding: 22px 24px; }
        .rt-visit .k { font-size: 13px; color: var(--muted); margin-bottom: 5px; }
        .rt-visit .v { font-weight: 600; line-height: 1.45; }

        /* ---- Footer ---------------------------------------------------------- */
        .rt-foot { border-top: 1px solid var(--rule); background: var(--card); }
        .rt-foot .inner { padding-block: 30px; color: var(--muted); font-size: 14px;
                          display: flex; justify-content: space-between; gap: 14px; flex-wrap: wrap; }

        @media (max-width: 860px) {
            .rt-hero .inner { grid-template-columns: 1fr; gap: 32px; align-items: start; }
        }
        @media (max-width: 780px) { .rt-two-col { grid-template-columns: 1fr; } }
        @media (max-width: 520px) {
            :root { --gutter: 16px; }
            .rt-head .row { gap: 14px; }
            .rt-nav { gap: 14px; }
            .rt-section { margin-top: 44px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>
</head>
<body>
    @include('theme.doeh-retail.layouts.header')
    <main>
        @if ($errors->any())
            <div class="rt-wrap" style="padding-top:24px;"><div class="rt-notice err">{{ $errors->first() }}</div></div>
        @endif
        @yield('content')
    </main>
    @include('theme.doeh-retail.layouts.footer')
</body>
</html>
