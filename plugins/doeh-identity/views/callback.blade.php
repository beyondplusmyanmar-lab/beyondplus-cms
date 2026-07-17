{{-- DOEH Identity OAuth callback.
     Standalone, theme-agnostic page. Its ONLY job is to host the JS core, which
     reads ?code&state from the URL and finishes the PKCE exchange in the browser.
     PHP renders chrome and public config only — it never reads the code or a token
     (invariant P1). --}}
@php($cfg = function_exists('doeh_identity_config') ? doeh_identity_config() : [])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signing you in…</title>
    <style>
        body { font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
               display: flex; min-height: 100vh; margin: 0; align-items: center;
               justify-content: center; background: #f8fafc; color: #334155; }
        .doeh-cb { text-align: center; }
        .doeh-cb .spinner { width: 34px; height: 34px; margin: 0 auto 14px;
               border: 3px solid #e2e8f0; border-top-color: #6366f1; border-radius: 50%;
               animation: doeh-spin 0.8s linear infinite; }
        @keyframes doeh-spin { to { transform: rotate(360deg); } }
        .doeh-cb .err { color: #b91c1c; }
        .doeh-cb a { color: #6366f1; }
    </style>
    <script>window.__DOEH_IDENTITY__ = @json($cfg);</script>
</head>
<body>
    <div class="doeh-cb" data-doeh-widget="callback">
        <div class="spinner" aria-hidden="true"></div>
        <p>Signing you in…</p>
    </div>
    <script src="/doeh-identity/widget.js?v={{ $cfg['version'] ?? '0' }}" defer></script>
</body>
</html>
