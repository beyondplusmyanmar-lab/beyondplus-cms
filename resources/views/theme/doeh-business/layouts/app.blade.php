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
    <style>
        :root {
            --accent: {{ $accent }};
            --accent-dark: #8f6631;
            --ink: #2b2620;
            --muted: #8a8175;
            --line: #ece5da;
            --bg: #faf8f4;
            --surface: #ffffff;
            --jade: #2f7d5b;
            --danger: #b0402f;
        }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg); color: var(--ink);
               font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans Myanmar", sans-serif;
               line-height: 1.55; }
        h1, h2, h3, .serif { font-family: Georgia, "Times New Roman", "Noto Serif Myanmar", serif; }
        a { color: var(--accent-dark); text-decoration: none; }
        a:hover { text-decoration: underline; }
        .wrap { max-width: 1040px; margin: 0 auto; padding: 0 20px; }
        .btn { display: inline-block; border: 0; border-radius: 10px; padding: 11px 20px; cursor: pointer;
               font: inherit; font-weight: 600; background: var(--accent); color: #fff; text-decoration: none;
               transition: background .15s ease; }
        .btn:hover { background: var(--accent-dark); text-decoration: none; }
        .btn.sec { background: var(--surface); color: var(--ink); border: 1px solid var(--line); font-weight: 500; }
        .btn.big { width: 100%; text-align: center; padding: 14px; font-size: 16px; }
        .btn[disabled] { opacity: .5; cursor: not-allowed; }
        .card { background: var(--surface); border: 1px solid var(--line); border-radius: 16px;
                box-shadow: 0 1px 2px rgba(43,38,32,.04); }
        .muted { color: var(--muted); }
        .jade { color: var(--jade); }
        .notice { border-radius: 12px; padding: 12px 14px; margin: 0 0 18px; }
        .notice.err { background: #fbeeeb; border: 1px solid #f0cabf; color: var(--danger); }

        /* Header */
        header.site { background: var(--surface); border-bottom: 1px solid var(--line); position: sticky; top: 0; z-index: 20; }
        header.site .bar { display: flex; align-items: center; gap: 22px; padding: 16px 0; }
        .brand { font-size: 22px; font-weight: 700; color: var(--ink); letter-spacing: .2px; }
        .brand:hover { text-decoration: none; }
        header.site nav { display: flex; gap: 18px; margin-left: 4px; }
        header.site nav a { color: var(--ink); font-weight: 500; }
        .spacer { margin-left: auto; }
        .cart-link { color: var(--ink); font-weight: 600; }
        #biz-account .dropdown { position: relative; display: inline-block; }
        #biz-account .menu { position: absolute; right: 0; top: 130%; background: var(--surface);
                border: 1px solid var(--line); border-radius: 12px; min-width: 180px; padding: 8px;
                box-shadow: 0 8px 28px rgba(43,38,32,.12); display: none; }
        #biz-account .menu.open { display: block; }
        #biz-account .menu a, #biz-account .menu .row2 { display: block; padding: 8px 10px; color: var(--ink); border-radius: 8px; }
        #biz-account .menu a:hover { background: var(--bg); text-decoration: none; }
        #biz-account button.link { background: none; border: 0; font: inherit; color: var(--ink); cursor: pointer; font-weight: 600; padding: 0; }

        main { padding: 34px 0 60px; }
        footer.site { border-top: 1px solid var(--line); background: var(--surface); }
        footer.site .inner { padding: 26px 0; color: var(--muted); font-size: 14px; display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    </style>
</head>
<body>
    @include('theme.doeh-business.layouts.header')
    <main>
        <div class="wrap">
            @if ($errors->any())
                <div class="notice err">{{ $errors->first() }}</div>
            @endif
            @yield('content')
        </div>
    </main>
    @include('theme.doeh-business.layouts.footer')
</body>
</html>
