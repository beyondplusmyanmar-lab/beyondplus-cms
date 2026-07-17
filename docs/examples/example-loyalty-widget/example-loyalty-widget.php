<?php

/**
 * Example: Points-to-Reward Widget — the smallest useful identity extension.
 *
 * What it demonstrates:
 *  - a theme-consumable FILTER that returns a mount point (the same pattern the
 *    DOEH Identity plugin uses for doeh_loyalty_panel);
 *  - a script injected on the theme_footer ACTION that drives the mount using
 *    ONLY the public browser API: the doeh:identity event + getCustomer();
 *  - P1 in practice: this file never sees a token — there is nothing here that
 *    could log or leak one. All customer state lives in the browser.
 *
 * A theme renders it with:   {!! bp_apply_filters('example_points_progress', '') !!}
 * When this plugin (or identity) is off, the filter returns the default ('')
 * and the theme shows nothing — graceful degradation for free.
 */

bp_add_filter('example_points_progress', function ($default) {
    if (! function_exists('doeh_identity_enabled') || ! doeh_identity_enabled()) {
        return $default; // identity off → render nothing
    }

    return '<div data-example-widget="progress"></div>';
});

bp_add_action('theme_footer', function () {
    if (! function_exists('doeh_identity_enabled') || ! doeh_identity_enabled()) {
        return;
    }

    // Settings are plain server-side config — safe to print. (Never print a
    // secret here; this plugin has none, by design.)
    $target = max(1, (int) bp_plugin_option('example-loyalty-widget', 'reward_target', '5000'));
    $label = e(bp_plugin_option('example-loyalty-widget', 'reward_label', 'Free coffee'));
    ?>
    <script>
    (function () {
        // doeh:identity fires on boot and again on sign-out — one listener
        // keeps every mount current. Never poll; never touch tokens.
        document.addEventListener('doeh:identity', render);
        function render() {
            var id = window.DoehIdentity;
            var mounts = document.querySelectorAll('[data-example-widget="progress"]');
            if (!id || !mounts.length) return;
            if (!id.isSignedIn()) {
                mounts.forEach(function (m) { m.innerHTML = ''; }); // guests see nothing
                return;
            }
            id.getCustomer().then(function (c) {
                var pts = (c && c.pointsBalance) || 0;
                var pct = Math.min(100, Math.round(pts / <?php echo $target; ?> * 100));
                mounts.forEach(function (m) {
                    m.innerHTML =
                        '<div style="border:1px solid #ddd; border-radius:10px; padding:12px 14px; max-width:340px;">' +
                        '<div style="font-size:13px; color:#666;">' + pts + ' / <?php echo $target; ?> points toward <strong><?php echo $label; ?></strong></div>' +
                        '<div style="background:#eee; border-radius:6px; height:8px; margin-top:8px; overflow:hidden;">' +
                        '<div style="background:#2f855a; height:100%; width:' + pct + '%;"></div></div>' +
                        (pct >= 100 ? '<div style="color:#2f855a; font-size:13px; margin-top:6px;">Reward unlocked — redeem in store!</div>' : '') +
                        '</div>';
                });
            });
        }
    })();
    </script>
    <?php
});
