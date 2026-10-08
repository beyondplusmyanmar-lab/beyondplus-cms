@php
    $siteName = trim((string) bp_option('mt_name')) ?: (optional(site_information('blogname'))->option_value ?: config('app.name'));
    $mm = app()->getLocale() === 'mm';
    $phone = trim((string) bp_option('mt_phone'));
    $email = trim((string) bp_option('mt_email'));
    $address = trim((string) bp_option('mt_address'));
    $socials = ['mt_social_facebook' => 'bi-facebook', 'mt_social_instagram' => 'bi-instagram', 'mt_social_tiktok' => 'bi-tiktok'];
@endphp
<footer class="mt-footer mt-4">
    <div class="container py-4">
        <div class="row gy-4">
            <div class="col-lg-5">
                <h6 class="mb-2"><i class="bi bi-shop" style="color:var(--mt-primary);"></i> {{ $siteName }}</h6>
                <p class="small mb-3">{{ optional(site_information('blogdescription'))->option_value ?: ($mm ? 'ကျွန်ုပ်တို့ ဆိုင်၏ အွန်လိုင်း ဈေးဆိုင်။' : 'Our shop, online.') }}</p>
                <div class="mt-social d-flex gap-2">
                    @foreach ($socials as $key => $icon)
                        @if (bp_option($key))<a href="{{ bp_option($key) }}" target="_blank" rel="noopener" aria-label="{{ str_replace(['mt_social_'], '', $key) }}"><i class="bi {{ $icon }}"></i></a>@endif
                    @endforeach
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <h6 class="mb-3">{{ $mm ? 'ဈေးဝယ်ရန်' : 'Shopping' }}</h6>
                <ul class="list-unstyled small d-grid gap-2 mb-0">
                    <li><a href="{{ url('/store') }}">{{ $mm ? 'ဈေးဆိုင်' : 'Shop' }}</a></li>
                    <li><a href="{{ url('/store/cart') }}">{{ $mm ? 'ခြင်း' : 'Cart' }}</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-4">
                <h6 class="mb-3">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Contact' }}</h6>
                <ul class="list-unstyled small d-grid gap-2 mb-0">
                    @if ($phone !== '')<li><i class="bi bi-telephone me-1" style="color:var(--mt-primary);"></i> <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{{ $phone }}</a></li>@endif
                    @if ($email !== '')<li><i class="bi bi-envelope me-1" style="color:var(--mt-primary);"></i> <a href="mailto:{{ $email }}">{{ $email }}</a></li>@endif
                    @if ($address !== '')<li><i class="bi bi-geo-alt me-1" style="color:var(--mt-primary);"></i> {{ $address }}</li>@endif
                    @if ($phone === '' && $email === '' && $address === '')<li>{{ $mm ? 'ဆိုင်သို့ တိုက်ရိုက် ဆက်သွယ်ပါ။' : 'Ask us in the shop.' }}</li>@endif
                </ul>
            </div>
        </div>
    </div>
    <div class="border-top">
        <div class="container py-3 d-flex flex-wrap justify-content-between small" style="gap:.5rem;">
            <span>&copy; {{ date('Y') }} {{ $siteName }}</span>
            <span>{{ $mm ? 'DOEH ဖြင့် အော်ဒါတင်သည်' : 'Orders powered by DOEH' }}</span>
        </div>
    </div>
</footer>

{{-- DOEH Identity injects its config + widget.js here. --}}
@php bp_do_action('theme_footer') @endphp

@if (function_exists('doeh_identity_enabled') && doeh_identity_enabled())
{{-- Header account slot — theme-owned UI on window.DoehIdentity. The theme never
     touches a token; points come from getCustomer(). --}}
<script>
(function () {
    var slot = document.getElementById('mt-doeh-account');
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
            slot.innerHTML = '<a class="mt-navlink" href="#"><i class="bi bi-person"></i> ' + t.signIn + '</a>';
            slot.firstChild.addEventListener('click', function (e) { e.preventDefault(); id.signIn(); });
            return;
        }
        slot.innerHTML =
            '<a class="mt-navlink dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">' +
                '<i class="bi bi-person-circle"></i> ' + t.account + '</a>' +
            '<ul class="dropdown-menu dropdown-menu-end">' +
                '<li><span class="dropdown-item-text small text-muted">' + t.points + '</span></li>' +
                '<li><span class="dropdown-item-text fw-bold" id="mt-doeh-points">' + t.loading + '</span></li>' +
                '<li><hr class="dropdown-divider"></li>' +
                '<li><a class="dropdown-item" href="#" id="mt-doeh-signout">' + t.signOut + '</a></li>' +
            '</ul>';
        slot.querySelector('#mt-doeh-signout').addEventListener('click', function (e) { e.preventDefault(); id.signOut(); });
        id.getCustomer().then(function (c) {
            var el = document.getElementById('mt-doeh-points');
            if (el) el.textContent = (c && c.state === 'ok' && c.pointsBalance != null) ? Number(c.pointsBalance).toLocaleString() : '—';
        });
    }
    document.addEventListener('doeh:identity', draw);
    if (window.DoehIdentity) draw();
})();
</script>
@endif
