<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in — {{ optional(site_information('blogname'))->option_value ?: 'Beyond Plus CMS' }}</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <style>
        :root { --bp-brand:#0d9488; --bp-brand-dark:#0f766e; --bp-accent:#14b8a6; }
        * { font-family:'Inter',-apple-system,"Segoe UI",Roboto,sans-serif; }
        body {
            min-height:100vh; margin:0; display:flex; align-items:center; justify-content:center;
            background: radial-gradient(1200px 600px at 20% -10%, #14b8a6 0%, transparent 55%),
                        linear-gradient(135deg, #0f766e 0%, #0f172a 100%);
            padding: 24px;
        }
        .login-card {
            width:100%; max-width:420px; background:#fff; border-radius:16px;
            box-shadow: 0 24px 60px rgba(2,6,23,.35); overflow:hidden;
        }
        .login-head { padding: 34px 34px 8px; text-align:center; }
        .login-badge {
            width:56px; height:56px; margin:0 auto 14px; border-radius:14px; color:#fff;
            display:flex; align-items:center; justify-content:center; font-size:1.5rem;
            background:linear-gradient(135deg,var(--bp-brand),var(--bp-accent));
            box-shadow:0 8px 20px rgba(13,148,136,.4);
        }
        .login-head h1 { font-size:1.35rem; font-weight:700; color:#0f172a; margin:0; }
        .login-head p { color:#64748b; margin:.35rem 0 0; font-size:.9rem; }
        .login-body { padding: 20px 34px 34px; }
        .form-label { font-weight:500; color:#1e293b; font-size:.85rem; }
        .form-control { border-radius:10px; padding:.6rem .85rem; border:1px solid #e2e8f0; }
        .form-control:focus { border-color:var(--bp-accent); box-shadow:0 0 0 3px rgba(20,184,166,.18); }
        .input-group-text { background:#f8fafc; border:1px solid #e2e8f0; border-right:0; border-radius:10px 0 0 10px; color:#94a3b8; }
        .input-group .form-control { border-left:0; border-radius:0 10px 10px 0; }
        .btn-brand {
            background:linear-gradient(135deg,var(--bp-brand),var(--bp-brand-dark)); color:#fff;
            border:0; border-radius:10px; padding:.65rem; font-weight:600; width:100%;
            box-shadow:0 8px 18px rgba(13,148,136,.3); transition:filter .15s ease;
        }
        .btn-brand:hover { filter:brightness(1.07); color:#fff; }
        .login-foot { text-align:center; color:#94a3b8; font-size:.82rem; padding-bottom:26px; }
        .login-foot a { color:var(--bp-brand); text-decoration:none; font-weight:600; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-head">
            <div class="login-badge"><i class="fa-solid fa-cube"></i></div>
            <h1>{{ optional(site_information('blogname'))->option_value ?: 'Beyond Plus CMS' }}</h1>
            <p>Sign in to your dashboard</p>
        </div>
        <div class="login-body">
            @if (session('status'))
                <div class="alert alert-success py-2">{{ session('status') }}</div>
            @endif
            @if (session('warning'))
                <div class="alert alert-warning py-2">{!! session('warning') !!}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ url('/login') }}">
                {!! csrf_field() !!}
                <div class="mb-3">
                    <label class="form-label">Email address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="you@example.com" autofocus required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="••••••••" required>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember" style="font-size:.85rem;color:#64748b;">Remember me</label>
                    </div>
                </div>
                <button type="submit" class="btn-brand"><i class="fa-solid fa-right-to-bracket me-1"></i> Sign in</button>
            </form>
        </div>
        <div class="login-foot">
            &copy; {{ date('Y') }} {{ optional(site_information('blogname'))->option_value ?: 'Beyond Plus CMS' }}
        </div>
    </div>
</body>
</html>
