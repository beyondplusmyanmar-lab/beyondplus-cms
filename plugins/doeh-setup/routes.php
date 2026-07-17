<?php

/**
 * DOEH Setup Wizard routes — /bp-admin/doeh-setup (admins only).
 *
 * Steps: 1 plugins → 2 theme → 3 brand → 4 commerce key → 5 identity (optional)
 * → 6 done. Each step's done-ness is computed from live config
 * (doeh_setup_state), so the wizard is re-entrant; ?step=N revisits any step.
 */

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Support\Plugin;
use App\Support\Theme;

Route::middleware('admins')->prefix('bp-admin')->group(function () {

    $firstOpenStep = function (array $s): int {
        if (! $s['plugins']) {
            return 1;
        }
        if (! $s['theme']) {
            return 2;
        }
        if (! $s['brand']) {
            return 3;
        }
        if (! $s['commerce']) {
            return 4;
        }
        if (! $s['identity'] && ! $s['identity_skipped']) {
            return 5;
        }

        return 6;
    };

    Route::get('doeh-setup', function (Request $request) use ($firstOpenStep) {
        $state = doeh_setup_state();
        $step = (int) $request->query('step', 0);
        if ($step < 1 || $step > 6) {
            $step = $firstOpenStep($state);
        }

        return view('doeh-setup::wizard', [
            'step'   => $step,
            'state'  => $state,
            'themes' => doeh_setup_themes(),
        ]);
    });

    // Step 1 — activate the bridge plugins (in dependency order).
    Route::post('doeh-setup/plugins', function () {
        foreach (['doeh-identity', 'doeh-commerce', 'doeh-commerce-storefront'] as $slug) {
            $activePlugins = json_decode((string) bp_option('active_plugins', '[]'), true) ?: [];
            if (in_array($slug, $activePlugins, true)) {
                continue;
            }
            $r = Plugin::activate($slug);
            if (! empty($r['blocked'])) {
                return redirect(url('bp-admin/doeh-setup?step=1'))
                    ->withErrors("Could not activate {$slug} — check the Plugins page for details.");
            }
        }

        return redirect(url('bp-admin/doeh-setup?step=2'))->with('success', 'DOEH plugins are active.');
    });

    // Step 2 — activate the chosen vertical theme (deps enforced by Theme::activate).
    Route::post('doeh-setup/theme', function (Request $request) {
        $slug = basename((string) $request->input('theme'));
        $known = array_column(doeh_setup_themes(), 'slug');
        if (! in_array($slug, $known, true)) {
            return redirect(url('bp-admin/doeh-setup?step=2'))->withErrors('Choose one of the DOEH themes.');
        }

        $r = Theme::activate($slug);
        if (! empty($r['blocked'])) {
            $why = implode('; ', (array) ($r['requirements'] ?? [])) ?: ($r['error'] ?? 'blocked');
            return redirect(url('bp-admin/doeh-setup?step=2'))->withErrors("Theme activation blocked: {$why}");
        }

        return redirect(url('bp-admin/doeh-setup?step=3'))->with('success', 'Theme activated.');
    });

    // Step 3 — brand: save the ACTIVE theme's Brand-group fields (same storage
    // as the Theme Customize page; the wizard is a guided subset of it).
    Route::post('doeh-setup/brand', function (Request $request) {
        $slug = Theme::active();
        foreach (Theme::settingsSchema($slug) as $field) {
            if (($field['group'] ?? '') !== 'Brand') {
                continue;
            }
            $name = $field['name'] ?? null;
            if (! $name) {
                continue;
            }
            doeh_setup_option_set($name, (string) $request->input($name, ''));
        }

        return redirect(url('bp-admin/doeh-setup?step=4'))->with('success', 'Branding saved.');
    });

    // Step 4 — commerce key: PROVE it against the live Orders API, then save.
    // A key that fails validation is never stored.
    Route::post('doeh-setup/commerce', function (Request $request) {
        $env = $request->input('environment') === 'production' ? 'production' : 'sandbox';
        $key = trim((string) $request->input('secret_key'));

        if ($key === '') {
            return redirect(url('bp-admin/doeh-setup?step=4'))->withErrors('Paste the merchant secret key (sk_…).');
        }
        // Cheap prefix/environment cross-check before burning a live call.
        $isTest = str_starts_with($key, 'sk_test_');
        $isLive = str_starts_with($key, 'sk_live_');
        if (! $isTest && ! $isLive) {
            return redirect(url('bp-admin/doeh-setup?step=4'))->withErrors('That does not look like a merchant secret key (sk_test_… / sk_live_…).');
        }
        if (($env === 'sandbox') !== $isTest) {
            return redirect(url('bp-admin/doeh-setup?step=4'))->withErrors('Key and environment do not match: use sk_test_ with Sandbox, sk_live_ with Production.');
        }

        // The connector class ships with the doeh-commerce plugin — step 1
        // activates it; guard against jumping straight here.
        if (! class_exists(\Doeh\Commerce\DoehCommerceClient::class)) {
            return redirect(url('bp-admin/doeh-setup?step=1'))->withErrors('Activate the DOEH plugins first (step 1).');
        }

        // Live proof: a tiny bounded window — success proves auth + scope + host
        // regardless of how many orders exist.
        $client = new \Doeh\Commerce\DoehCommerceClient($key, $env);
        $now = time();
        $probe = $client->listOrders([
            'from'  => gmdate('Y-m-d\TH:i:s\Z', $now - 60),
            'to'    => gmdate('Y-m-d\TH:i:s\Z', $now),
            'limit' => 50,
        ]);
        if (! ($probe['ok'] ?? false)) {
            $code = $probe['code'] ?? 'EDGE_TRANSPORT';
            $friendly = function_exists('doeh_storefront_message') ? doeh_storefront_message($code) : 'The key could not be verified.';
            return redirect(url('bp-admin/doeh-setup?step=4'))
                ->withErrors($friendly." [{$code}]");
        }

        doeh_setup_option_set(Plugin::settingKey('doeh-commerce', 'environment'), $env);
        doeh_setup_option_set(Plugin::settingKey('doeh-commerce', 'secret_key'), $key);
        doeh_setup_option_set(Plugin::settingKey('doeh-commerce', 'enabled'), 'yes');

        return redirect(url('bp-admin/doeh-setup?step=5'))
            ->with('success', 'Key verified against the DOEH Orders API and saved.');
    });

    // Step 5 — identity (OPTIONAL): format-validate and save, or skip. A live
    // proof needs a real browser sign-in, so the wizard points at the
    // storefront for that instead of pretending to verify.
    Route::post('doeh-setup/identity', function (Request $request) {
        if ($request->input('action') === 'skip') {
            doeh_setup_option_set(Plugin::settingKey('doeh-setup', 'identity_skipped'), 'yes');

            return redirect(url('bp-admin/doeh-setup?step=6'))->with('success', 'Skipped — you can add customer sign-in later.');
        }

        $env = $request->input('environment') === 'production' ? 'production' : 'sandbox';
        $clientId = trim((string) $request->input('client_id'));
        $pk = trim((string) $request->input('publishable_key'));

        if (! preg_match('/^app_[A-Za-z0-9]+$/', $clientId)) {
            return redirect(url('bp-admin/doeh-setup?step=5'))->withErrors('That does not look like a DOEH client id (app_…).');
        }
        $pkPrefix = $env === 'sandbox' ? 'pk_test_' : 'pk_live_';
        if (! str_starts_with($pk, $pkPrefix)) {
            return redirect(url('bp-admin/doeh-setup?step=5'))->withErrors("The publishable key must start with {$pkPrefix} for this environment.");
        }

        doeh_setup_option_set(Plugin::settingKey('doeh-identity', 'environment'), $env);
        doeh_setup_option_set(Plugin::settingKey('doeh-identity', 'client_id'), $clientId);
        doeh_setup_option_set(Plugin::settingKey('doeh-identity', 'publishable_key'), $pk);
        doeh_setup_option_set(Plugin::settingKey('doeh-identity', 'enabled'), 'yes');
        doeh_setup_option_set(Plugin::settingKey('doeh-setup', 'identity_skipped'), '');

        return redirect(url('bp-admin/doeh-setup?step=6'))
            ->with('success', 'Customer sign-in configured — verify it with a real sign-in on your storefront.');
    });
});
