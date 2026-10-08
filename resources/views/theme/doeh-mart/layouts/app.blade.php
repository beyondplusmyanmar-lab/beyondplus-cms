<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $siteName = trim((string) bp_option('mt_name')) ?: (optional(site_information('blogname'))->option_value ?: config('app.name'));
        $siteDesc = optional(site_information('blogdescription'))->option_value ?: '';
        $primary = bp_option('mt_color_primary', '#ee4d2d') ?: '#ee4d2d';
        $primaryDark = bp_option('mt_color_primary_dark', '#d73211') ?: '#d73211';
        $hero = bp_option('mt_color_hero', '#0b5a50') ?: '#0b5a50';
    @endphp
    <title>@hasSection('title')@yield('title') — {{ $siteName }}@else{{ $siteName }}@endif</title>
    @if ($siteDesc !== '')<meta name="description" content="{{ $siteDesc }}">@endif
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Noto+Sans+Myanmar:wght@400;500;600;700&display=swap">

    <style>
        :root {
            --mt-primary: {{ $primary }};
            --mt-primary-dark: {{ $primaryDark }};
            --mt-hero: {{ $hero }};
            --mt-wash: color-mix(in srgb, {{ $primary }} 10%, #fff);
            --mt-bg: #f5f5f5;
            --mt-surface: #fff;
            --mt-text: #222;
            --mt-muted: #757575;
            --mt-border: #ececec;
            --mt-ok: #0b7f5a;
            --mt-lift-1: 0 1px 2px rgba(20,20,20,.05), 0 2px 8px -2px rgba(20,20,20,.07);
            --mt-lift-2: 0 2px 6px rgba(20,20,20,.08), 0 10px 26px -12px rgba(20,20,20,.22);
            --bs-primary: var(--mt-primary);
            --bs-link-color: var(--mt-primary);
            --bs-link-hover-color: var(--mt-primary-dark);
        }
        body { font-family: 'Manrope', 'Noto Sans Myanmar', system-ui, sans-serif; line-height: 1.6;
               color: var(--mt-text); background: var(--mt-bg); }
        a { text-decoration: none; }
        /* Bootstrap 5.3 colours links from --bs-link-color-rgb, so the palette is set here. */
        main a:not(.btn):not(.mt-cat) { color: var(--mt-primary); }
        main a:not(.btn):not(.mt-cat):hover { color: var(--mt-primary-dark); }
        .mt-muted { color: var(--mt-muted) !important; }
        .btn { font-weight: 600; }
        .btn-primary { --bs-btn-bg: var(--mt-primary); --bs-btn-border-color: var(--mt-primary);
                       --bs-btn-hover-bg: var(--mt-primary-dark); --bs-btn-hover-border-color: var(--mt-primary-dark);
                       --bs-btn-active-bg: var(--mt-primary-dark); --bs-btn-active-border-color: var(--mt-primary-dark);
                       --bs-btn-disabled-bg: var(--mt-primary); --bs-btn-disabled-border-color: var(--mt-primary); }
        .btn-outline-primary { --bs-btn-color: var(--mt-primary); --bs-btn-border-color: var(--mt-primary);
                               --bs-btn-hover-bg: var(--mt-primary); --bs-btn-hover-border-color: var(--mt-primary);
                               --bs-btn-active-bg: var(--mt-primary-dark); }
        .mt-money { font-variant-numeric: tabular-nums; font-feature-settings: "tnum" 1; font-weight: 700; }

        /* ── Top strip + orange header ── */
        .mt-topbar { background: var(--mt-primary-dark); color: #fff; font-size: .8rem; }
        .mt-topbar a { color: #fff; opacity: .9; }
        .mt-topbar a:hover { opacity: 1; }
        .mt-header { background: var(--mt-primary); color: #fff; }
        .mt-brand { color: #fff !important; font-weight: 800; font-size: 1.4rem; letter-spacing: .2px; white-space: nowrap; }
        .mt-search .form-control { border: 0; border-radius: 3px 0 0 3px; padding: .6rem .9rem; }
        .mt-search .btn { background: var(--mt-primary-dark); color: #fff; border: 0; border-radius: 0 3px 3px 0; padding: 0 1.1rem; }
        .mt-cart { color: #fff; position: relative; font-size: 1.55rem; line-height: 1; }
        .mt-cart:hover { color: #fff; }
        .mt-cart .badge { position: absolute; top: -7px; right: -11px; background: #fff; color: var(--mt-primary);
                          font-size: .62rem; border-radius: 999px; }
        .mt-navlink { color: #fff; opacity: .95; font-weight: 500; font-size: .9rem; }
        .mt-navlink:hover, .mt-navlink.active { color: #fff; opacity: 1; text-decoration: underline; text-underline-offset: 4px; }

        /* ── Banner ── */
        .mt-hero { border-radius: 6px; overflow: hidden; position: relative; color: #fff; box-shadow: var(--mt-lift-1);
                   background: radial-gradient(120% 140% at 0% 0%, color-mix(in srgb, var(--mt-hero) 72%, #fff) 0%, var(--mt-hero) 42%, color-mix(in srgb, var(--mt-hero) 62%, #0d1b2a) 100%); }
        .mt-hero::before, .mt-hero::after { content: ""; position: absolute; border-radius: 50%; background: rgba(255,255,255,.07); }
        .mt-hero::before { width: 220px; height: 220px; right: 6%; top: -110px; }
        .mt-hero::after { width: 160px; height: 160px; left: 22%; bottom: -100px; }
        .mt-hero .inner { position: relative; z-index: 1; padding: clamp(2rem, 6vw, 4.5rem) clamp(1.25rem, 3vw, 3rem); }
        .mt-hero h1 { font-weight: 800; font-size: clamp(1.9rem, 4.6vw, 3.4rem); line-height: 1.08; margin: 0 0 .6rem; letter-spacing: -.01em; }
        .mt-hero p { font-size: clamp(1rem, 1.6vw, 1.15rem); opacity: .95; max-width: 48ch; margin: 0 0 1.4rem; }
        .mt-hero .btn-light { color: var(--mt-primary); font-weight: 800; padding: .7rem 1.5rem; border-radius: 6px; }
        .mt-hero .btn-ghost { background: rgba(255,255,255,.95); color: var(--mt-text); font-weight: 800; padding: .7rem 1.5rem; border-radius: 6px; }

        /* ── Panels, categories, services ── */
        .mt-section { padding: 1.1rem 0; }
        .mt-panel { background: var(--mt-surface); border-radius: 4px; padding: 1.1rem 1.2rem; box-shadow: var(--mt-lift-1); }
        .mt-panel-title { font-size: .95rem; color: var(--mt-muted); font-weight: 700; margin: 0; }
        .mt-panel-title.hot { color: var(--mt-primary); font-size: 1.05rem; }
        .mt-cat { display: flex; flex-direction: column; align-items: center; gap: .6rem; padding: 1.1rem .5rem;
                  background: #fff; border: 1px solid var(--mt-border); border-radius: 4px; color: var(--mt-text);
                  text-align: center; height: 100%; transition: border-color .14s ease, box-shadow .14s ease; }
        .mt-cat:hover { border-color: var(--mt-primary); color: var(--mt-primary); box-shadow: var(--mt-lift-2); }
        .mt-cat i { font-size: 1.6rem; color: var(--mt-primary); line-height: 1; }
        .mt-cat span { font-size: .85rem; }
        .mt-services { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .75rem; }
        @media (min-width: 768px) { .mt-services { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
        .mt-service { display: flex; align-items: center; gap: .6rem; font-size: .85rem; }
        .mt-service i { color: var(--mt-primary); font-size: 1.35rem; }

        /* ── Product cards: a dense grid responds with border + shadow, nothing moves ── */
        .mt-card { background: #fff; border: 1px solid var(--mt-border); border-radius: 3px; overflow: hidden; height: 100%;
                   display: flex; flex-direction: column; transition: border-color .14s ease, box-shadow .14s ease; }
        .mt-card:hover, .mt-card:focus-within { border-color: var(--mt-primary); box-shadow: var(--mt-lift-2); }
        .mt-card .thumb { aspect-ratio: 1 / 1; background: #f4f4f4; display: grid; place-items: center; color: #9a9a9a; }
        .mt-card .thumb i { font-size: 2.6rem; }
        .mt-card .body { padding: .6rem .7rem .75rem; display: flex; flex-direction: column; gap: .45rem; flex: 1; }
        .mt-card .pname { font-size: .88rem; line-height: 1.3; min-height: 2.3em; display: -webkit-box;
                          -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .mt-card .price { color: var(--mt-primary); font-size: 1.05rem; }
        .mt-card .meta { font-size: .72rem; color: var(--mt-muted); display: flex; justify-content: space-between; gap: .4rem; }
        .mt-card form { margin-top: auto; }

        /* ── Forms (cart) ── */
        .mt-choice { display: flex; gap: .6rem; align-items: baseline; padding: .6rem .8rem; border: 1px solid var(--mt-border);
                     border-radius: 4px; cursor: pointer; background: var(--mt-bg); }
        .mt-choice:has(input:checked) { border-color: var(--mt-primary); background: var(--mt-wash); }
        .mt-thumb-sm { width: 56px; height: 56px; flex: 0 0 auto; border-radius: 3px; background: #f4f4f4;
                       display: grid; place-items: center; color: #9a9a9a; font-size: 1.4rem; }

        footer.mt-footer { background: #fff; border-top: 3px solid var(--mt-primary); color: var(--mt-muted); }
        footer.mt-footer h6 { color: var(--mt-text); font-size: .85rem; text-transform: uppercase; letter-spacing: .04em; }
        footer.mt-footer a { color: var(--mt-muted); }
        footer.mt-footer a:hover { color: var(--mt-primary); }
        .mt-social a { width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;
                       border-radius: 50%; background: var(--mt-bg); color: var(--mt-primary); }

        :focus-visible { outline: 2.5px solid var(--mt-primary); outline-offset: 2px; border-radius: 3px; }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    @include('theme.doeh-mart.layouts.header')
    <main class="flex-grow-1 pb-4">
        @if ($errors->any())
            <div class="container pt-3"><div class="alert alert-danger mb-0">{{ $errors->first() }}</div></div>
        @endif
        @yield('content')
    </main>
    @include('theme.doeh-mart.layouts.footer')

    @php $mmLayout = app()->getLocale() === 'mm'; @endphp
    {{-- "Added to cart" toast for the in-page add below. --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:1080;">
        <div id="mt-cart-toast" class="toast align-items-center border-0 text-white" role="status" aria-live="polite" aria-atomic="true"
             style="background:var(--mt-text);">
            <div class="d-flex align-items-center">
                <div class="toast-body"><i class="bi bi-check-circle-fill me-1" style="color:#5fd39a;"></i> <span data-msg></span></div>
                <a href="{{ url('/store/cart') }}" class="btn btn-sm btn-light fw-bold me-2">{{ $mmLayout ? 'ခြင်း ကြည့်ရန်' : 'View cart' }}</a>
                <button type="button" class="btn-close btn-close-white me-2" data-bs-dismiss="toast" aria-label="{{ $mmLayout ? 'ပိတ်ရန်' : 'Close' }}"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
    // Add to cart without leaving the page: the form posts in the background (the storefront
    // answers JSON when asked), the header badge takes the new count and a toast confirms.
    // Any failure falls back to the plain form post, which lands on the cart as before.
    (function () {
        var added = @json($mmLayout ? 'ခြင်းထဲ ထည့်ပြီး' : 'Added to cart');
        var toastEl = document.getElementById('mt-cart-toast');
        function setBadge(count) {
            var cart = document.querySelector('.mt-cart');
            if (!cart) return;
            var badge = cart.querySelector('.badge');
            if (count > 0) {
                if (!badge) { badge = document.createElement('span'); badge.className = 'badge'; cart.appendChild(badge); }
                badge.textContent = count;
            } else if (badge) { badge.remove(); }
        }
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form.matches('form[action$="/store/cart/add"]') || !window.fetch || form.dataset.plain) return;
            e.preventDefault();
            var btn = form.querySelector('button[type=submit]');
            if (btn) btn.disabled = true;
            fetch(form.action, {
                method: 'POST', body: new FormData(form), credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function (r) {
                return r.json().then(function (d) { return { ok: r.ok && d && d.ok, d: d }; });
            }).then(function (res) {
                if (!res.ok) throw new Error('not added');
                setBadge(res.d.count);
                if (toastEl && window.bootstrap) {
                    var name = (form.closest('.mt-card') || document).querySelector('.pname');
                    toastEl.querySelector('[data-msg]').textContent = added + (name ? ' — ' + name.textContent.trim() : '');
                    bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 2600 }).show();
                }
                if (btn) btn.disabled = false;
            }).catch(function () {
                form.dataset.plain = '1';
                form.submit();
            });
        });
    })();
    </script>
    @stack('scripts')
</body>
</html>
