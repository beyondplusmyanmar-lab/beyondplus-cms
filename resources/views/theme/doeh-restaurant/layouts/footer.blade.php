@php
    $siteName = trim((string) bp_option('r_name')) ?: (optional(site_information('blogname'))->option_value ?: config('app.name'));
    $mm = app()->getLocale() === 'mm';
@endphp
<footer class="r-foot">
    <div class="r-wrap inner">
        <span>&copy; {{ date('Y') }} {{ $siteName }}</span>
        <span>{{ $mm ? 'DOEH ဖြင့် အော်ဒါတင်သည်' : 'Orders powered by DOEH' }}</span>
    </div>
</footer>

{{-- DOEH Identity injects its config + widget.js here. --}}
@php bp_do_action('theme_footer') @endphp

@if (function_exists('doeh_identity_enabled') && doeh_identity_enabled())
{{-- Header account slot — theme-owned UI on window.DoehIdentity. Signed out: a
     sign-in link. Signed in: a rewards dropdown with the live points balance and
     sign-out. The theme never touches a customer token. --}}
<script>
(function () {
    var slot = document.getElementById('r-account');
    if (!slot) return;
    var mm = {{ $mm ? 'true' : 'false' }};
    var t = {
        signIn:  mm ? 'ဝင်ရန်' : 'Sign in',
        hello:   mm ? 'မင်္ဂလာပါ' : 'Account',
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
                '<button type="button" class="link" id="r-acct-btn">' + t.hello + ' ▾</button>' +
                '<span class="menu" id="r-acct-menu">' +
                    '<div class="line r-eyebrow">' + t.rewards + '</div>' +
                    '<div class="line"><span class="r-jade" id="r-points" style="font-size:26px;font-family:Fraunces,Georgia,serif;">' + t.loading + '</span> <span class="r-muted">' + t.points + '</span></div>' +
                    '<a href="#" id="r-signout">' + t.signOut + '</a>' +
                '</span>' +
            '</span>';
        var menu = slot.querySelector('#r-acct-menu');
        slot.querySelector('#r-acct-btn').addEventListener('click', function (e) { e.stopPropagation(); menu.classList.toggle('open'); });
        document.addEventListener('click', function () { menu.classList.remove('open'); });
        slot.querySelector('#r-signout').addEventListener('click', function (e) { e.preventDefault(); id.signOut(); });
        id.getCustomer().then(function (c) {
            var el = document.getElementById('r-points');
            if (el) el.textContent = (c && c.state === 'ok' && c.pointsBalance != null) ? Number(c.pointsBalance).toLocaleString() : '—';
        });
    }
    document.addEventListener('doeh:identity', draw);
    if (window.DoehIdentity) draw();
})();
</script>
@endif
