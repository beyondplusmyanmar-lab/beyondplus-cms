<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') — @yield('title')</title>
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
    </style>
</head>
<body>
    <main class="bp-err">
        <div class="bp-err__badge"><i class="bi @yield('icon', 'bi-exclamation-triangle')"></i></div>
        <div class="bp-err__code">@yield('code')</div>
        <h1 class="bp-err__title">@yield('title')</h1>
        <p class="bp-err__msg">@yield('message')</p>
        <div class="d-flex gap-2 justify-content-center flex-wrap">
            <a href="{{ url('/') }}" class="btn btn-bp px-4"><i class="bi bi-house-door"></i> Back to home</a>
            <a href="javascript:history.back()" class="btn btn-outline-secondary px-4"><i class="bi bi-arrow-left"></i> Go back</a>
        </div>
    </main>
</body>
</html>
