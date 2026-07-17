<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        // Settings are flat bp_options; every theme setting is read this way.
        $siteName = trim((string) bp_option('ex_name')) ?: (optional(site_information('blogname'))->option_value ?: config('app.name'));
        $brand = bp_option('ex_brand', '#2563eb');
    @endphp
    <title>@hasSection('title')@yield('title') — {{ $siteName }}@else{{ $siteName }}@endif</title>
    <style>
        /* Design tokens from theme settings — derive everything from these. */
        :root { --brand: {{ $brand }}; --ink: #1f2430; --muted: #6b7280; --line: #e5e7eb; --money: #2f855a; }
        * { box-sizing: border-box; margin: 0; }
        body { font-family: system-ui, sans-serif; color: var(--ink); line-height: 1.5; }
        a { color: var(--brand); text-decoration: none; }
        .wrap { max-width: 880px; margin: 0 auto; padding: 0 20px; }
        .card { border: 1px solid var(--line); border-radius: 12px; padding: 18px 20px; margin-bottom: 18px; }
        .btn { display: inline-block; background: var(--brand); color: #fff; border: 0; border-radius: 9px; padding: 10px 18px; font: inherit; cursor: pointer; }
        .muted { color: var(--muted); }
        .money { color: var(--money); font-variant-numeric: tabular-nums; }
        :focus-visible { outline: 2px solid var(--brand); outline-offset: 2px; }
    </style>
</head>
<body>
    @include('theme.example-theme.layouts.header')
    <main class="wrap" style="padding:28px 20px 60px;">
        @if ($errors->any())<div class="card" style="border-color:#dc2626; color:#dc2626;">{{ $errors->first() }}</div>@endif
        @yield('content')
    </main>
    @include('theme.example-theme.layouts.footer')
</body>
</html>
