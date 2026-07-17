@php
    $siteName = optional(site_information('blogname'))->option_value ?: config('app.name');
    $mm = app()->getLocale() === 'mm';
    $phone = bp_option('sf_phone'); $email = bp_option('sf_email') ?: optional(site_information('admin_email'))->option_value; $address = bp_option('sf_address');
    $socials = ['sf_social_facebook' => 'bi-facebook', 'sf_social_instagram' => 'bi-instagram', 'sf_social_tiktok' => 'bi-tiktok'];
@endphp
<footer class="sf-footer mt-4">
    <div class="container py-4">
        <div class="row gy-4">
            <div class="col-lg-4">
                <h6 class="mb-2"><i class="bi bi-shop text-primary"></i> {{ $siteName }}</h6>
                <p class="small mb-3">{{ optional(site_information('blogdescription'))->option_value ?: ($mm ? 'အွန်လိုင်း ဈေးဆိုင်။' : 'Your online shop.') }}</p>
                <div class="sf-social d-flex gap-2">
                    @foreach($socials as $key => $icon)
                        @if(bp_option($key))<a href="{{ bp_option($key) }}" target="_blank" rel="noopener"><i class="bi {{ $icon }}"></i></a>@endif
                    @endforeach
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="mb-3">{{ $mm ? 'ဈေးဝယ်ရန်' : 'Shopping' }}</h6>
                <ul class="list-unstyled small d-grid gap-2 mb-0">
                    <li><a href="{{ url('/shop') }}">{{ $mm ? 'ဈေးဆိုင်' : 'Shop' }}</a></li>
                    <li><a href="{{ url('/cart') }}">{{ $mm ? 'ခြင်း' : 'Cart' }}</a></li>
                    <li><a href="{{ url('/faq') }}">{{ $mm ? 'အမေးအဖြေ' : 'FAQ' }}</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-3">
                <h6 class="mb-3">{{ $mm ? 'ကုမ္ပဏီ' : 'Company' }}</h6>
                <ul class="list-unstyled small d-grid gap-2 mb-0">
                    <li><a href="{{ url('/about') }}">{{ $mm ? 'အကြောင်း' : 'About' }}</a></li>
                    <li><a href="{{ url('/shipping') }}">{{ $mm ? 'ပို့ဆောင်မှု' : 'Shipping & Returns' }}</a></li>
                    <li><a href="{{ url('/contact') }}">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Contact' }}</a></li>
                </ul>
            </div>
            <div class="col-lg-3">
                <h6 class="mb-3">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Contact' }}</h6>
                <ul class="list-unstyled small d-grid gap-2 mb-0">
                    @if($phone)<li><i class="bi bi-telephone text-primary me-1"></i> {{ $phone }}</li>@endif
                    @if($email)<li><i class="bi bi-envelope text-primary me-1"></i> {{ $email }}</li>@endif
                    @if($address)<li><i class="bi bi-geo-alt text-primary me-1"></i> {{ $address }}</li>@endif
                </ul>
            </div>
        </div>
    </div>
    <div class="border-top">
        <div class="container py-3 text-center small">
            &copy; {{ date('Y') }} {{ $siteName }}. {{ $mm ? 'မူပိုင်ခွင့် အားလုံး ရယူထားသည်။' : 'All rights reserved.' }}
        </div>
        @php bp_do_action('theme_footer') @endphp
    </div>
</footer>

@if (function_exists('doeh_identity_enabled') && doeh_identity_enabled())
{{-- Header DOEH account slot. THEME-owned UI on top of the identity plugin's
     browser API (window.DoehIdentity) — the theme never sees a token and never
     calls the DOEH API itself. Wakes on the plugin's `doeh:identity` event
     (the widget JS is deferred, so it is not defined when this runs). --}}
<script>
(function () {
    var slot = document.getElementById('sf-doeh-account');
    if (!slot) return;
    var mm = {{ $mm ? 'true' : 'false' }};
    var t = {
        signIn:  mm ? 'ဝင်ရန်' : 'Sign in',
        account: mm ? 'ကျွန်ုပ်အကောင့်' : 'My account',
        points:  mm ? 'အမှတ်များ' : 'Points',
        signOut: mm ? 'ထွက်ရန်' : 'Sign out',
        loading: mm ? 'ဖွင့်နေသည်…' : 'Loading…'
    };
    function draw() {
        var id = window.DoehIdentity;
        if (!id) return;
        if (!id.isSignedIn()) {
            slot.innerHTML = '<a class="sf-navlink" href="#"><i class="bi bi-person"></i> ' + t.signIn + '</a>';
            slot.firstChild.addEventListener('click', function (e) { e.preventDefault(); id.signIn(); });
            return;
        }
        slot.innerHTML =
            '<a class="sf-navlink dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">' +
                '<i class="bi bi-person-circle"></i> ' + t.account + '</a>' +
            '<ul class="dropdown-menu dropdown-menu-end">' +
                '<li><span class="dropdown-item-text small text-muted">' + t.points + '</span></li>' +
                '<li><span class="dropdown-item-text fw-bold" id="sf-doeh-points">' + t.loading + '</span></li>' +
                '<li><hr class="dropdown-divider"></li>' +
                '<li><a class="dropdown-item" href="#" id="sf-doeh-signout">' + t.signOut + '</a></li>' +
            '</ul>';
        slot.querySelector('#sf-doeh-signout').addEventListener('click', function (e) { e.preventDefault(); id.signOut(); });
        id.getCustomer().then(function (c) {
            var el = document.getElementById('sf-doeh-points');
            if (el) el.textContent = (c && c.state === 'ok' && c.pointsBalance != null)
                ? Number(c.pointsBalance).toLocaleString() : '—';
        });
    }
    document.addEventListener('doeh:identity', draw);
    if (window.DoehIdentity) draw();
})();
</script>
@endif
