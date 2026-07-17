<footer style="border-top:1px solid var(--line);">
    <div class="wrap muted" style="padding-block:18px; font-size:14px;">
        Powered by DOEH.
    </div>
</footer>

{{-- REQUIRED: this action lets plugins inject their scripts (DOEH Identity
     ships its widget.js + config here). Without it, sign-in never boots. --}}
@php bp_do_action('theme_footer') @endphp

<script>
// The account slot, driven by the PUBLIC browser API only (P1: no tokens in
// PHP, ever). doeh:identity fires on boot and sign-out — one listener keeps
// the header current.
document.addEventListener('doeh:identity', function () {
    var el = document.getElementById('ex-account');
    var id = window.DoehIdentity;
    if (!el || !id) return;
    if (!id.isSignedIn()) {
        el.innerHTML = '<button class="btn" style="padding:6px 14px;" onclick="DoehIdentity.signIn()">Sign in</button>';
        return;
    }
    id.getCustomer().then(function (c) {
        el.innerHTML = '<span class="muted" style="font-size:14px;">★ ' + ((c && c.pointsBalance) || 0) + ' pts</span> ' +
            '<a href="#" onclick="DoehIdentity.signOut(); return false;" style="font-size:14px;">Sign out</a>';
    });
});
</script>
