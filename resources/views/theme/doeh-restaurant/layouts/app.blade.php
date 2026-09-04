<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $siteName = trim((string) bp_option('r_name')) ?: (optional(site_information('blogname'))->option_value ?: config('app.name'));
        $clay = bp_option('r_clay', '#be5f37');
    @endphp
    <title>@hasSection('title')@yield('title') · {{ $siteName }}@else{{ $siteName }}@endif</title>
    @php $favOpt = trim((string) bp_option('r_favicon')); @endphp
    @if ($favOpt !== '')
        <link rel="icon" href="{{ \Illuminate\Support\Str::startsWith($favOpt, ['http', '/']) ? $favOpt : bp_upload_url($favOpt) }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Fraunces: a soft, characterful display serif for the menu-card voice. Body stays system. --}}
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;1,9..144,500&family=Noto+Serif+Myanmar:wght@500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --clay: {{ $clay }};
            --clay-deep: #9c4a29;
            --teal: #1e6e68;
            --saffron: #e1a02b;
            --jade: #2e7d5b;
            --ink: #2a2622;
            --muted: #8c8577;
            --paper: #fbf6ee;
            --card: #ffffff;
            --line: #e8dfce;
            --danger: #b0402f;
            --ink-2: #4a443c;
            --rule-soft: #f0e8d9;
            --lift-1: 0 1px 2px rgba(42,38,34,.05), 0 2px 8px -2px rgba(42,38,34,.06);
            --lift-2: 0 1px 2px rgba(42,38,34,.06), 0 14px 34px -12px rgba(42,38,34,.2);
        }
        * { box-sizing: border-box; }
        html { -webkit-text-size-adjust: 100%; }
        body {
            margin: 0; background: var(--paper); color: var(--ink);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans Myanmar", sans-serif;
            line-height: 1.55;
        }
        .r-serif { font-family: "Fraunces", "Noto Serif Myanmar", Georgia, serif; }
        h1, h2, h3 { font-family: "Fraunces", "Noto Serif Myanmar", Georgia, serif; font-weight: 600; letter-spacing: -0.01em; margin: 0; }
        a { color: var(--teal); text-decoration: none; }
        a:hover { text-decoration: underline; }
        .r-eyebrow { font-size: 13.5px; letter-spacing: 0; text-transform: none; color: var(--teal); font-weight: 600; }
        .r-wrap { max-width: 860px; margin: 0 auto; padding: 0 20px; }
        .r-muted { color: var(--muted); }
        .r-money { font-variant-numeric: tabular-nums; }
        .r-jade { color: var(--jade); font-variant-numeric: tabular-nums; }

        /* Buttons */
        .r-btn { display: inline-flex; align-items: center; gap: 8px; border: 0; border-radius: 999px;
                 padding: 11px 22px; font: inherit; font-weight: 600; cursor: pointer;
                 background: var(--clay); color: #fff; text-decoration: none; box-shadow: var(--lift-1);
                 transition: background .15s ease, box-shadow .16s ease, transform .08s ease; }
        .r-btn:hover { background: var(--clay-deep); text-decoration: none; box-shadow: var(--lift-2); }
        .r-btn:active { transform: translateY(1px); }
        .r-btn.ghost { box-shadow: none; background: var(--card); color: var(--ink); border: 1px solid var(--line); font-weight: 500; }
        .r-btn.ghost:hover { background: #fff; }
        .r-btn.sm { padding: 7px 16px; font-size: 14px; }
        .r-btn.block { width: 100%; justify-content: center; padding: 14px; }
        .r-btn[disabled] { opacity: .5; cursor: not-allowed; }
        :focus-visible { outline: 3px solid color-mix(in srgb, var(--teal) 45%, transparent); outline-offset: 2px; }

        .r-card { background: var(--card); border: 1px solid var(--rule-soft); border-radius: 18px; box-shadow: var(--lift-1); }
        .r-notice { border-radius: 12px; padding: 12px 15px; margin: 0 0 20px; }
        .r-notice.err { background: #fbeeeb; border: 1px solid #f0cabf; color: var(--danger); }

        /* Header */
        .r-head { background: color-mix(in srgb, var(--paper) 85%, #fff); border-bottom: 1px solid var(--line);
                  position: sticky; top: 0; z-index: 30; backdrop-filter: saturate(1.1) blur(6px); }
        .r-head .row { display: flex; align-items: center; gap: 20px; padding-block: 15px; }
        .r-brand { font-family: "Fraunces","Noto Serif Myanmar",Georgia,serif; font-weight: 600; font-size: 22px; color: var(--ink); }
        .r-brand:hover { text-decoration: none; }
        .r-nav { display: flex; gap: 18px; }
        .r-nav a { color: var(--ink); font-weight: 500; }
        .r-spacer { margin-left: auto; }
        .r-cart { color: var(--ink); font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }
        .r-badge { background: var(--clay); color: #fff; border-radius: 999px; font-size: 12px; padding: 1px 8px; font-variant-numeric: tabular-nums; }
        #r-account .drop { position: relative; }
        #r-account .menu { position: absolute; right: 0; top: 132%; background: #fff; border: 1px solid var(--line);
                 border-radius: 14px; min-width: 200px; padding: 10px; box-shadow: var(--lift-2); display: none; }
        #r-account .menu.open { display: block; animation: r-drop .16s ease both; }
        #r-account .menu .line { padding: 8px 10px; }
        #r-account .menu a { display: block; padding: 8px 10px; color: var(--ink); border-radius: 9px; }
        #r-account .menu a:hover { background: var(--paper); text-decoration: none; }
        #r-account button.link { background: none; border: 0; font: inherit; font-weight: 600; color: var(--ink); cursor: pointer; padding: 0; }

        /* Menu leader-dot row — the signature device */
        .r-menu-row { display: flex; align-items: baseline; gap: 12px; padding: 19px 0; border-bottom: 1px solid var(--rule-soft); }
        .r-menu-row:last-child { border-bottom: 0; }
        .r-menu-row .mr-name { font-family: "Fraunces","Noto Serif Myanmar",Georgia,serif; font-size: 19px; font-weight: 600; }
        .r-menu-row .mr-sku { color: var(--muted); font-size: 12.5px; letter-spacing: 0; font-variant-numeric: tabular-nums; }
        .r-menu-row .mr-dots { flex: 1 1 auto; align-self: baseline; position: relative; top: -5px;
                 border-bottom: 2px dotted color-mix(in srgb, var(--muted) 42%, transparent); min-width: 24px; }
        .r-menu-row .mr-price { font-family: "Fraunces",Georgia,serif; font-size: 19px; font-weight: 600; color: var(--clay-deep);
                 font-variant-numeric: tabular-nums; font-feature-settings: "tnum" 1; white-space: nowrap; }

        /* ---- Shared system (type scale, elevation, rhythm) ------------------ */
        .r-display { font-size: clamp(34px, 5.4vw, 52px); line-height: 1.04; letter-spacing: -0.022em; }
        .r-h2 { font-size: clamp(21px, 2.5vw, 27px); }
        .r-h3 { font-size: 18px; }
        .r-lead { font-size: clamp(16px, 1.6vw, 19px); color: var(--ink-2); max-width: 40ch; }
        .r-small { font-size: 14px; }
        .r-band { width: 100%; }
        .r-section { margin-top: 52px; }
        .r-rule { height: 1px; border: 0; margin: 0 0 8px;
                  background: linear-gradient(90deg, var(--line), var(--rule-soft) 60%, transparent); }
        .r-head-row { display: flex; align-items: baseline; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 8px; }

        /* Hero: the restaurant's own name, set as a printed menu card would set it. */
        .r-hero { border-bottom: 1px solid var(--line);
                  background: radial-gradient(105% 125% at 50% -28%, color-mix(in srgb, var(--clay) 7%, #fff), transparent 60%),
                              linear-gradient(180deg, #fff, var(--paper)); }
        .r-hero .inner { padding: clamp(44px, 7vw, 82px) 0 clamp(34px, 5vw, 54px); text-align: center; }
        .r-hero .cta-row { display: flex; justify-content: center; align-items: center; gap: 16px; flex-wrap: wrap; margin-top: 24px; }
        .r-reveal > * { animation: r-rise .7s cubic-bezier(.2,.7,.3,1) both; }
        .r-reveal > *:nth-child(2) { animation-delay: .07s; }
        .r-reveal > *:nth-child(3) { animation-delay: .14s; }
        .r-reveal > *:nth-child(4) { animation-delay: .21s; }
        @keyframes r-rise { from { opacity: 0; transform: translateY(13px); } to { opacity: 1; transform: none; } }
        @keyframes r-drop { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: none; } }

        /* Ordering is a sequence, so it is numbered. */
        .r-steps { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 26px; counter-reset: step; }
        .r-step { counter-increment: step; padding-top: 40px; position: relative; }
        .r-step::before { content: counter(step); position: absolute; top: 0; left: 0; font-size: 15px; font-weight: 700;
                          color: var(--clay-deep); width: 29px; height: 29px; border-radius: 50%; display: grid; place-items: center;
                          border: 1.5px solid color-mix(in srgb, var(--clay) 24%, #fff);
                          background: color-mix(in srgb, var(--clay) 7%, #fff); }
        .r-step p { color: var(--ink-2); font-size: 15px; margin: 5px 0 0; max-width: 32ch; }

        .r-rows .r { display: flex; justify-content: space-between; align-items: baseline; gap: 16px;
                     padding: 14px 0; border-bottom: 1px solid var(--rule-soft); }
        .r-rows .r:last-child { border-bottom: 0; }
        .r-rows .k { color: var(--muted); font-size: 14px; }
        .r-rows .v { font-weight: 600; text-align: right; }

        main { display: block; padding: 0 0 76px; }
        .r-foot { border-top: 1px solid var(--line); }
        .r-foot .inner { padding-block: 26px; color: var(--muted); font-size: 14px; display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; }

        @media (max-width: 560px) {
            .r-head .row { gap: 12px; }
            .r-nav { gap: 12px; }
            .r-menu-row .mr-price { font-size: 16px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>
</head>
<body>
    @include('theme.doeh-restaurant.layouts.header')
    <main>
        @if ($errors->any())
            <div class="r-wrap" style="padding-top:24px;"><div class="r-notice err">{{ $errors->first() }}</div></div>
        @endif
        @yield('content')
    </main>
    @include('theme.doeh-restaurant.layouts.footer')
</body>
</html>
