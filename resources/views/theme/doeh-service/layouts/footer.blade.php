@php
    $siteName = trim((string) bp_option('sv_name')) ?: (optional(site_information('blogname'))->option_value ?: config('app.name'));
    $mm = app()->getLocale() === 'mm';
@endphp
<footer class="sv-foot">
    <div class="sv-wrap inner">
        <span>&copy; {{ date('Y') }} {{ $siteName }}</span>
        <span>{{ $mm ? 'DOEH ဖြင့် တောင်းဆိုမှုတင်သည်' : 'Requests powered by DOEH' }}</span>
    </div>
</footer>

{{-- DOEH Identity injects its config + widget.js here. --}}
@php bp_do_action('theme_footer') @endphp

@if (function_exists('doeh_identity_enabled') && doeh_identity_enabled())
{{-- Account slot — theme-owned UI on window.DoehIdentity. Points come from
     getCustomer(); the theme never touches a token. --}}
<script>
(function () {
    var slot = document.getElementById('sv-account');
    if (!slot) return;
    var mm = {{ $mm ? 'true' : 'false' }};
    var t = {
        signIn:  mm ? 'ဝင်ရန်' : 'Sign in',
        account: mm ? 'အကောင့်' : 'Account',
        rewards: mm ? 'ဆုမှတ်များ' : 'Rewards',
        points:  mm ? 'အမှတ်' : 'points',
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
            '<span class="drop">' +
                '<button type="button" class="link" id="sv-acct-btn">' + t.account + ' ▾</button>' +
                '<span class="menu" id="sv-acct-menu">' +
                    '<div class="line sv-eyebrow">' + t.rewards + '</div>' +
                    '<div class="line"><span class="sv-price" id="sv-points" style="font-size:24px;">' + t.loading + '</span> <span class="sv-muted">' + t.points + '</span></div>' +
                    '<a href="#" id="sv-signout">' + t.signOut + '</a>' +
                '</span>' +
            '</span>';
        var menu = slot.querySelector('#sv-acct-menu');
        slot.querySelector('#sv-acct-btn').addEventListener('click', function (e) { e.stopPropagation(); menu.classList.toggle('open'); });
        document.addEventListener('click', function () { menu.classList.remove('open'); });
        slot.querySelector('#sv-signout').addEventListener('click', function (e) { e.preventDefault(); id.signOut(); });
        id.getCustomer().then(function (c) {
            var el = document.getElementById('sv-points');
            if (el) el.textContent = (c && c.state === 'ok' && c.pointsBalance != null) ? Number(c.pointsBalance).toLocaleString() : '—';
        });
    }
    document.addEventListener('doeh:identity', draw);
    if (window.DoehIdentity) draw();
})();
</script>
@endif
