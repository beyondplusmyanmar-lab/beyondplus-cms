<?php

/**
 * DOEH Identity — browser OAuth 2.1 + PKCE sign-in for Beyond Plus CMS.
 *
 * ── INVARIANT P1 (the whole reason this plugin is safe to run on an untrusted
 *    merchant origin): NO code path in this PHP file — or anywhere in the PHP
 *    shell — reads, stores, logs, or forwards an access or refresh token. The
 *    PHP layer does presentation and public configuration only. All identity
 *    (PKCE, code exchange, token lifecycle) lives in the browser in
 *    assets/doeh-identity.js. Adding a server endpoint that accepts or relays a
 *    token is re-opening the architecture decision, not adding a feature.
 *
 * This file registers hooks only; it is loaded solely while the plugin is active.
 */

use Illuminate\Support\Facades\Route;

if (! function_exists('doeh_identity_config')) {
    /**
     * Public, non-secret configuration handed to the browser. Every value here
     * is safe to print in page source: client_id and publishable_key are public
     * identifiers by design, and the issuer/api base are fixed per environment.
     *
     * @return array<string,mixed>
     */
    function doeh_identity_config(): array
    {
        $env = bp_plugin_option('doeh-identity', 'environment') ?: 'sandbox';

        $planes = [
            'sandbox' => [
                'issuer'  => 'https://auth-sandbox.doehpos.com',
                'apiBase' => 'https://sandbox-api.doehpos.com/v1',
            ],
            'production' => [
                'issuer'  => 'https://auth.doehpos.com',
                'apiBase' => 'https://api.doehpos.com/v1',
            ],
        ];
        $plane = $planes[$env] ?? $planes['sandbox'];

        // Redirect URI is derived from the site's own host so it always matches
        // what the operator registered as https://<site>/doeh/callback.
        $redirectUri = rtrim(request()->getSchemeAndHttpHost(), '/').'/doeh/callback';

        return [
            'environment'    => $env,
            'issuer'         => $plane['issuer'],
            'apiBase'        => $plane['apiBase'],
            'clientId'       => (string) bp_plugin_option('doeh-identity', 'client_id'),
            'publishableKey' => (string) bp_plugin_option('doeh-identity', 'publishable_key'),
            'redirectUri'    => $redirectUri,
            'scope'          => 'loyalty:read',
            'version'        => '0.1.0',
        ];
    }
}

if (! function_exists('doeh_identity_enabled')) {
    /** True when the operator has switched sign-in on AND supplied the required public ids. */
    function doeh_identity_enabled(): bool
    {
        if ((bp_plugin_option('doeh-identity', 'enabled') ?: 'yes') !== 'yes') {
            return false;
        }
        $cfg = doeh_identity_config();

        return $cfg['clientId'] !== '' && $cfg['publishableKey'] !== '';
    }
}

// ── Widget mount points ───────────────────────────────────────────────────────
// Two ways to place a widget, both handled in the browser by the JS core (this
// CMS has no server-side content-filter hook, so mounting is client-side):
//   1. Page/post authors type a shortcode token in content: [doeh_signin] /
//      [doeh_loyalty]. The JS finds the literal token and replaces it in place.
//   2. Theme authors drop an explicit element: <div data-doeh-widget="signin">
//      or <div data-doeh-widget="loyalty">.
// Themes may also render a mount point through a filter without editing markup:
//   {!! bp_apply_filters('doeh_signin_button', '') !!}
//   {!! bp_apply_filters('doeh_loyalty_panel', '') !!}
bp_add_filter('doeh_signin_button', fn ($v) => doeh_identity_enabled() ? '<div data-doeh-widget="signin"></div>' : $v);
bp_add_filter('doeh_loyalty_panel', fn ($v) => doeh_identity_enabled() ? '<div data-doeh-widget="loyalty"></div>' : $v);

// ── Inject the public config + the self-contained JS core, once per page ──────
bp_add_action('theme_footer', function () {
    if (! doeh_identity_enabled()) {
        return;
    }
    $cfg = doeh_identity_config();
    $json = htmlspecialchars(json_encode($cfg, JSON_UNESCAPED_SLASHES), ENT_NOQUOTES, 'UTF-8');
    $v = rawurlencode($cfg['version']);
    echo "\n<script>window.__DOEH_IDENTITY__=JSON.parse('".str_replace("'", "\\'", $json)."');</script>\n";
    echo '<script src="/doeh-identity/widget.js?v='.$v.'" defer></script>'."\n";
});

// The callback page + the JS asset are declared in this plugin's routes.php,
// which the CMS loads separately during routing (App\Support\Plugin::bootRoutes).
// Configuration status is surfaced by the CMS-generated settings form (Plugins →
// DOEH Identity → Settings) from the manifest "settings" block — no custom admin
// page needed, and no admin_notices hook exists in this CMS to hook into.
