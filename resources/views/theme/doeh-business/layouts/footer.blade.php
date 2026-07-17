@php
    $siteName = trim((string) bp_option('biz_shop_name')) ?: (optional(site_information('blogname'))->option_value ?: config('app.name'));
    $mm = app()->getLocale() === 'mm';
@endphp
<footer class="site">
    <div class="wrap inner">
        <span>&copy; {{ date('Y') }} {{ $siteName }}</span>
        <span>{{ $mm ? 'DOEH ဖြင့် ချိတ်ဆက်ထားသည်' : 'Connected to DOEH' }}</span>
    </div>
</footer>

{{-- DOEH Identity plugin injects its config + widget.js here. --}}
@php bp_do_action('theme_footer') @endphp

@if (function_exists('doeh_identity_enabled') && doeh_identity_enabled())
{{-- Header account slot behaviour: THEME-owned UI on top of window.DoehIdentity.
     Signed out → a sign-in link; signed in → a dropdown with live points, a link
     to the DOEH-hosted profile, and sign-out. The theme never touches a token. --}}
<script>
(function () {
    var slot = document.getElementById('biz-account');
    if (!slot) return;
    var mm = {{ $mm ? 'true' : 'false' }};
    var t = {
        signIn:  mm ? 'ဝင်ရန်' : 'Sign in',
        account: mm ? 'အကောင့်' : 'Account',
        points:  mm ? 'အမှတ်များ' : 'Points',
        signOut: mm ? 'ထွက်ရန်' : 'Sign out',
        loading: mm ? 'ဖွင့်နေသည်…' : 'Loading…'
    };
    function draw() {
        var id = window.DoehIdentity;
        if (!id) return;
        if (!id.isSignedIn()) {
            slot.innerHTML = '<button type="button" class="link">' + t.signIn + '</button>';
            slot.querySelector('button').addEventListener('click', function () { id.signIn(); });
            return;
        }
        slot.innerHTML =
            '<span class="dropdown">' +
                '<button type="button" class="link" id="biz-acct-btn">' + t.account + ' ▾</button>' +
                '<span class="menu" id="biz-acct-menu">' +
                    '<span class="row2 muted" style="font-size:13px;">' + t.points + ': <b id="biz-points">' + t.loading + '</b></span>' +
                    '<a href="#" id="biz-signout">' + t.signOut + '</a>' +
                '</span>' +
            '</span>';
        var menu = slot.querySelector('#biz-acct-menu');
        slot.querySelector('#biz-acct-btn').addEventListener('click', function (e) { e.stopPropagation(); menu.classList.toggle('open'); });
        document.addEventListener('click', function () { menu.classList.remove('open'); });
        slot.querySelector('#biz-signout').addEventListener('click', function (e) { e.preventDefault(); id.signOut(); });
        id.getCustomer().then(function (c) {
            var el = document.getElementById('biz-points');
            if (el) el.textContent = (c && c.state === 'ok' && c.pointsBalance != null) ? Number(c.pointsBalance).toLocaleString() : '—';
        });
    }
    document.addEventListener('doeh:identity', draw);
    if (window.DoehIdentity) draw();
})();
</script>
@endif
