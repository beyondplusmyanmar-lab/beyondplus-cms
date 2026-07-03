<!DOCTYPE html>
@php
    // Error pages can render before the locale middleware runs (unmatched routes),
    // so pick up an en/mm URL prefix here; otherwise the app default (mm) applies.
    $seg = request()->segment(1);
    if (in_array($seg, (array) config('app.locales', ['en', 'mm']), true)) {
        app()->setLocale($seg);
    }
    $brand = optional(site_information('blogname'))->option_value ?: config('app.name');
    // Resolve the code here (after the locale is set) so translations are correct.
    $code = trim($__env->yieldContent('code')) ?: '500';
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} — {{ __("errors.$code.title") }} · {{ $brand }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --bp-accent:#0d9488; --bp-accent-dark:#0f766e; }
        body {
            min-height:100vh; margin:0; padding:2rem;
            display:flex; align-items:center; justify-content:center;
            background:radial-gradient(1200px 600px at 50% -10%, #ccfbf1 0%, #f8fafc 55%);
            font-family:system-ui,-apple-system,"Segoe UI",Roboto,"Noto Sans Myanmar",sans-serif;
            color:#0f172a;
        }
        .bp-err { max-width:540px; width:100%; text-align:center; }
        .bp-err__badge {
            display:inline-flex; align-items:center; justify-content:center;
            width:76px; height:76px; border-radius:50%;
            background:#fff; box-shadow:0 10px 30px rgba(13,148,136,.18);
            color:var(--bp-accent); font-size:2.1rem; margin-bottom:1rem;
        }
        .bp-err__code {
            font-size:clamp(4.5rem,16vw,8rem); font-weight:800; line-height:.95;
            letter-spacing:-.04em;
            background:linear-gradient(135deg,var(--bp-accent),#0f172a);
            -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent;
        }
        .bp-err__title { font-size:1.5rem; font-weight:700; margin:.25rem 0 0; }
        .bp-err__msg { color:#64748b; margin:.75rem auto 1.75rem; max-width:26rem; }
        .btn-bp { background:var(--bp-accent); border-color:var(--bp-accent); color:#fff; }
        .btn-bp:hover, .btn-bp:focus { background:var(--bp-accent-dark); border-color:var(--bp-accent-dark); color:#fff; }
        .bp-err__brand { position:fixed; top:1.5rem; left:50%; transform:translateX(-50%); font-weight:800; font-size:1.15rem; color:var(--bp-accent); text-decoration:none; letter-spacing:-.01em; }
        .bp-err__brand:hover { color:var(--bp-accent-dark); }
        .bp-err--wide { max-width:900px; }
        .bp-log { margin-top:2rem; text-align:left; border:1px solid #e2e8f0; border-radius:.75rem; overflow:hidden; box-shadow:0 10px 30px rgba(15,23,42,.06); }
        .bp-log__head { display:flex; align-items:center; gap:.5rem; background:#0f172a; color:#e2e8f0; font-weight:600; font-size:.85rem; padding:.6rem .9rem; }
        .bp-log__head .badge { background:var(--bp-accent); }
        .bp-log__body { background:#fff; padding:.9rem; }
        .bp-log__msg { color:#b91c1c; font-weight:600; font-family:ui-monospace,SFMono-Regular,Menlo,monospace; font-size:.85rem; word-break:break-word; }
        .bp-log__loc { color:#64748b; font-size:.78rem; margin:.35rem 0 .6rem; font-family:ui-monospace,SFMono-Regular,Menlo,monospace; }
        .bp-log__trace { background:#0b1220; color:#94a3b8; font-size:.72rem; line-height:1.5; padding:.8rem; border-radius:.5rem; max-height:340px; overflow:auto; margin:0; white-space:pre-wrap; word-break:break-word; }
    </style>
</head>
<body>
    <a href="{{ url('/') }}" class="bp-err__brand">{{ $brand }}</a>
    <main class="bp-err @hasSection('log') bp-err--wide @endif">
        <div class="bp-err__badge"><i class="bi @yield('icon', 'bi-exclamation-triangle')"></i></div>
        <div class="bp-err__code">{{ $code }}</div>
        <h1 class="bp-err__title">{{ __("errors.$code.title") }}</h1>
        <p class="bp-err__msg">{{ __("errors.$code.message") }}</p>
        <div class="d-flex gap-2 justify-content-center flex-wrap">
            <a href="{{ url('/') }}" class="btn btn-bp px-4"><i class="bi bi-house-door"></i> {{ __('errors.home') }}</a>
            <a href="javascript:history.back()" class="btn btn-outline-secondary px-4"><i class="bi bi-arrow-left"></i> {{ __('errors.back') }}</a>
        </div>
        @yield('log')
    </main>
</body>
</html>
