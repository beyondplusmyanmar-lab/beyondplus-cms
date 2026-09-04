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
    @php $favOpt = trim((string) bp_option('sv_favicon')); @endphp
    @if ($favOpt !== '')
        <link rel="icon" href="{{ \Illuminate\Support\Str::startsWith($favOpt, ['http', '/']) ? $favOpt : bp_upload_url($favOpt) }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Spectral: a calm transitional serif — the "care / by appointment" voice. Body stays system. --}}
    <link href="https://fonts.googleapis.com/css2?family=Spectral:ital,wght@0,500;0,600;1,500&family=Noto+Serif+Myanmar:wght@500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand: {{ $brand }};
            --brand-deep: color-mix(in srgb, {{ $brand }} 72%, #10251a);
            --brand-tint: color-mix(in srgb, {{ $brand }} 12%, #fff);
            --brand-wash: color-mix(in srgb, {{ $brand }} 6%, #fff);
            --price: #bc6a45;
            --ink: #23302a;
            --ink-2: #46554d;
            --muted: #7c8378;
            --line: #e3e4db;
            --rule-soft: #edeee6;
            --bg: #f4f3ed;
            --surface: #ffffff;
            --danger: #a4503a;

            --lift-1: 0 1px 2px rgba(35,48,42,.05), 0 2px 8px -2px rgba(35,48,42,.06);
            --lift-2: 0 1px 2px rgba(35,48,42,.06), 0 14px 34px -12px rgba(35,48,42,.2);

            --wrap: 780px;
            --gutter: 22px;
        }
        * { box-sizing: border-box; }
        html { -webkit-text-size-adjust: 100%; }
        body { margin: 0; background: var(--bg); color: var(--ink);
               font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans Myanmar", sans-serif;
               font-size: 16.5px; line-height: 1.65; -webkit-font-smoothing: antialiased; }

        .sv-serif, h1, h2, h3 { font-family: "Spectral", "Noto Serif Myanmar", Georgia, serif; font-weight: 600; letter-spacing: 0; margin: 0; line-height: 1.2; }
        .sv-display { font-size: clamp(32px, 5vw, 46px); line-height: 1.1; letter-spacing: -0.012em; }
        .sv-h2 { font-size: clamp(21px, 2.4vw, 26px); }
        .sv-h3 { font-size: 18px; }
        .sv-lead { font-size: clamp(16px, 1.5vw, 18.5px); color: var(--ink-2); max-width: 44ch; }
        p { margin: 0 0 1em; max-width: 66ch; }
        .sv-muted { color: var(--muted); }
        .sv-small { font-size: 14px; }
        /* A quiet label. Deliberately not tracked-out caps — that reads as template chrome. */
        .sv-eyebrow { font-size: 13.5px; color: var(--brand-deep); font-weight: 600; letter-spacing: 0; text-transform: none; }
        .sv-price { font-family: "Spectral", Georgia, serif; font-variant-numeric: tabular-nums;
                    font-feature-settings: "tnum" 1; color: var(--price); font-weight: 600; }
        a { color: var(--brand-deep); text-decoration: none; }
        a:hover { text-decoration: underline; text-underline-offset: 3px; }
        :focus-visible { outline: 2.5px solid var(--brand); outline-offset: 2px; border-radius: 3px; }

        .sv-wrap { max-width: var(--wrap); margin: 0 auto; padding: 0 var(--gutter); }
        .sv-band { width: 100%; }
        main { display: block; padding-bottom: 76px; }
        .sv-section { margin-top: 52px; }
        .sv-rule { height: 1px; border: 0; margin: 0 0 6px;
                   background: linear-gradient(90deg, var(--line), var(--rule-soft) 60%, transparent); }
        .sv-head-row { display: flex; align-items: baseline; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 8px; }

        .sv-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: 0; border-radius: 12px;
                  padding: 12px 24px; font: inherit; font-weight: 600; cursor: pointer; background: var(--brand); color: #fff;
                  text-decoration: none; box-shadow: var(--lift-1);
                  transition: background .16s ease, box-shadow .16s ease, transform .08s ease; }
        .sv-btn:hover { background: var(--brand-deep); text-decoration: none; box-shadow: var(--lift-2); }
        .sv-btn:active { transform: translateY(1px); box-shadow: var(--lift-1); }
        .sv-btn.ghost { background: var(--surface); color: var(--ink); border: 1px solid var(--line); font-weight: 500; box-shadow: none; }
        .sv-btn.ghost:hover { background: #fff; border-color: var(--ink-2); box-shadow: var(--lift-1); }
        .sv-btn.sm { padding: 9px 17px; font-size: 14px; border-radius: 10px; }
        .sv-btn.block { width: 100%; padding: 14px; }
        .sv-btn[disabled] { opacity: .45; cursor: not-allowed; box-shadow: none; }
        .sv-btn[disabled]:hover { background: var(--brand); }

        .sv-card { background: var(--surface); border: 1px solid var(--rule-soft); border-radius: 18px; box-shadow: var(--lift-1); }
        .sv-chip { display: inline-flex; align-items: center; gap: 5px; font-size: 12.5px; font-weight: 600;
                   padding: 3px 10px; border-radius: 999px; background: var(--brand-tint); color: var(--brand-deep); }
        .sv-notice { border-radius: 12px; padding: 13px 16px; margin: 0 0 20px; }
        .sv-notice.err { background: color-mix(in srgb, var(--price) 9%, #fff); border: 1px solid color-mix(in srgb, var(--price) 28%, #fff); color: var(--danger); }

        /* Header */
        .sv-head { background: color-mix(in srgb, var(--bg) 80%, #fff); border-bottom: 1px solid var(--line);
                   position: sticky; top: 0; z-index: 30; backdrop-filter: saturate(1.25) blur(10px); }
        .sv-head .row { display: flex; align-items: center; gap: 22px; padding-block: 16px; }
        .sv-brand { font-family: "Spectral","Noto Serif Myanmar",Georgia,serif; font-weight: 600; font-size: 22px; color: var(--ink); }
        .sv-brand:hover { text-decoration: none; }
        .sv-nav { display: flex; gap: 18px; }
        .sv-nav a { color: var(--ink-2); font-weight: 500; }
        .sv-nav a:hover { color: var(--ink); }
        .sv-spacer { margin-left: auto; }
        .sv-req { color: var(--ink); font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }
        .sv-badge { background: var(--brand); color: #fff; border-radius: 999px; font-size: 12px; padding: 1px 8px; font-variant-numeric: tabular-nums; font-weight: 700; }
        #sv-account .drop { position: relative; }
        #sv-account .menu { position: absolute; right: 0; top: 148%; background: #fff; border: 1px solid var(--line);
                 border-radius: 15px; min-width: 208px; padding: 11px; box-shadow: var(--lift-2); display: none; }
        #sv-account .menu.open { display: block; animation: sv-drop .16s ease both; }
        #sv-account .menu .line { padding: 7px 10px; }
        #sv-account .menu a { display: block; padding: 8px 10px; color: var(--ink); border-radius: 9px; }
        #sv-account .menu a:hover { background: var(--bg); text-decoration: none; }
        #sv-account button.link { background: none; border: 0; font: inherit; font-weight: 600; color: var(--ink); cursor: pointer; padding: 0; }
        @keyframes sv-drop { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: none; } }

        /* Hero — calm and centred, the one composed moment. */
        .sv-hero { border-bottom: 1px solid var(--line);
                   background: radial-gradient(100% 120% at 50% -30%, var(--brand-wash), transparent 60%),
                               linear-gradient(180deg, #fff, var(--bg)); }
        .sv-hero .inner { padding: clamp(46px, 7vw, 84px) 0 clamp(36px, 5vw, 56px); text-align: center; }
        .sv-hero .cta-row { display: flex; justify-content: center; align-items: center; gap: 16px; flex-wrap: wrap; margin-top: 26px; }
        .sv-reveal > * { animation: sv-rise .7s cubic-bezier(.2,.7,.3,1) both; }
        .sv-reveal > *:nth-child(2) { animation-delay: .07s; }
        .sv-reveal > *:nth-child(3) { animation-delay: .14s; }
        .sv-reveal > *:nth-child(4) { animation-delay: .21s; }
        @keyframes sv-rise { from { opacity: 0; transform: translateY(13px); } to { opacity: 1; transform: none; } }

        /* The appointment row — the signature: unhurried, ruled, price in its own column. */
        .sv-svc { display: flex; align-items: center; gap: 20px; padding: 26px 0; border-bottom: 1px solid var(--rule-soft); }
        .sv-svc:last-child { border-bottom: 0; }
        .sv-svc .s-name { font-family: "Spectral","Noto Serif Myanmar",Georgia,serif; font-size: 21px; font-weight: 600; line-height: 1.25; }
        .sv-svc .s-note { color: var(--muted); font-size: 13.5px; margin-top: 5px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .sv-svc .s-mid { flex: 1 1 auto; min-width: 0; }
        .sv-svc .s-price { font-size: 20px; white-space: nowrap; }

        /* Booking is genuinely a sequence, so it is numbered. */
        .sv-steps { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 26px; counter-reset: step; }
        .sv-step { counter-increment: step; padding-top: 40px; position: relative; }
        .sv-step::before { content: counter(step); position: absolute; top: 0; left: 0; font-size: 15px; font-weight: 700;
                           color: var(--brand-deep); width: 29px; height: 29px; border-radius: 50%; display: grid;
                           place-items: center; border: 1.5px solid var(--brand-tint); background: var(--brand-wash); }
        .sv-step p { color: var(--ink-2); font-size: 15px; margin: 5px 0 0; max-width: 32ch; }

        .sv-rows .r { display: flex; justify-content: space-between; align-items: baseline; gap: 16px;
                      padding: 14px 0; border-bottom: 1px solid var(--rule-soft); }
        .sv-rows .r:last-child { border-bottom: 0; }
        .sv-rows .k { color: var(--muted); font-size: 14px; }
        .sv-rows .v { font-weight: 600; text-align: right; }

        .sv-foot { border-top: 1px solid var(--line); }
        .sv-foot .inner { padding-block: 30px; color: var(--muted); font-size: 14px; display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; }

        @media (max-width: 560px) {
            :root { --gutter: 18px; }
            .sv-head .row { gap: 12px; }
            .sv-nav { gap: 12px; }
            .sv-svc { flex-wrap: wrap; gap: 12px; }
            .sv-section { margin-top: 42px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>
</head>
<body>
    @include('theme.doeh-service.layouts.header')
    <main>
        @if ($errors->any())
            <div class="sv-wrap" style="padding-top:24px;"><div class="sv-notice err">{{ $errors->first() }}</div></div>
        @endif
        @yield('content')
    </main>
    @include('theme.doeh-service.layouts.footer')
</body>
</html>
