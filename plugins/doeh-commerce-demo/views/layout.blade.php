{{-- Self-contained reference chrome. A production theme would use its own layout
     and its own styling — this exists only so the demo renders on any theme. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DOEH Commerce demo')</title>
    <style>
        :root { --ink:#0f172a; --muted:#64748b; --line:#e2e8f0; --brand:#6366f1; --ok:#16a34a; --bg:#f8fafc; }
        * { box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
               margin: 0; background: var(--bg); color: var(--ink); }
        .wrap { max-width: 720px; margin: 0 auto; padding: 24px 16px 64px; }
        .tag { display:inline-block; font-size:12px; color:var(--muted); border:1px solid var(--line);
               border-radius:999px; padding:2px 10px; margin-bottom:14px; background:#fff; }
        h1 { font-size: 22px; margin: 0 0 4px; }
        .sub { color: var(--muted); margin: 0 0 20px; }
        .card { background:#fff; border:1px solid var(--line); border-radius:14px; padding:16px; margin-bottom:12px; }
        .row { display:flex; align-items:center; justify-content:space-between; gap:12px; }
        .name { font-weight:600; }
        .hint { color:var(--muted); font-size:14px; }
        .btn { display:inline-block; border:0; border-radius:9px; padding:9px 16px; cursor:pointer;
               font:inherit; background:var(--brand); color:#fff; text-decoration:none; }
        .btn.sec { background:#fff; color:var(--ink); border:1px solid var(--line); }
        .btn.big { width:100%; text-align:center; padding:13px; font-size:16px; }
        .err { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; border-radius:10px; padding:10px 12px; margin-bottom:14px; }
        .muted { color: var(--muted); }
        a.plain { color: var(--brand); text-decoration: none; }
        .total { font-size:28px; font-weight:800; }
        .ok-badge { color:var(--ok); font-weight:700; }
        table { width:100%; border-collapse:collapse; }
        td { padding:8px 0; border-bottom:1px solid var(--line); vertical-align:top; }
        td.r { text-align:right; white-space:nowrap; }
    </style>
</head>
<body>
    <div class="wrap">
        <span class="tag">DOEH Commerce · reference checkout</span>
        @if ($errors->any())
            <div class="err">{{ $errors->first() }}</div>
        @endif
        @yield('content')
        <p class="muted" style="margin-top:28px; font-size:13px;">
            This is a reference flow. Your theme owns the real product, cart and checkout pages;
            it calls the same <code>doeh_commerce()</code> connector shown here.
        </p>
    </div>
</body>
</html>
