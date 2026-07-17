<?php

/**
 * DOEH Setup Wizard — the merchant onboarding flow (Merchant Activation v1,
 * Phase 3). Everything it does rides EXISTING surfaces: Plugin::activate /
 * Theme::activate (dependency-enforced), the theme settings schema (Brand
 * group), and the doeh-commerce / doeh-identity plugin options.
 *
 * Boundaries:
 *  - Keys are COLLECTED and VALIDATED, never minted — issuance is the DOEH
 *    developer portal's job. The commerce key is proven with a live Orders API
 *    call before it is saved; a key that fails is never stored.
 *  - Identity is OPTIONAL — commerce works without it (guest checkout). The
 *    step can be skipped and revisited later.
 *  - The merchant secret key is never echoed back to the browser once saved.
 */

if (! function_exists('doeh_setup_themes')) {
    /**
     * The installable DOEH storefront themes: every theme whose manifest
     * requires doeh-commerce-storefront. The theme represents the business
     * model (D2) — its declared fulfillment_types are shown, not chosen.
     *
     * @return array<int, array{slug:string, name:string, description:string, fulfillment:array<int,string>, active:bool}>
     */
    function doeh_setup_themes(): array
    {
        $active = \App\Support\Theme::active();
        $out = [];
        foreach (\App\Support\Theme::all() as $t) {
            $slug = $t['slug'] ?? '';
            $meta = \App\Support\Theme::meta($slug);
            $requires = (array) ($meta['requires'] ?? []);
            if (! in_array('doeh-commerce-storefront', $requires, true)) {
                continue;
            }
            $out[] = [
                'slug'        => $slug,
                'name'        => $meta['name'] ?? $slug,
                'description' => $meta['description'] ?? '',
                'fulfillment' => array_values((array) ($meta['fulfillment_types'] ?? ['pickup'])),
                'active'      => $slug === $active,
            ];
        }

        return $out;
    }
}

if (! function_exists('doeh_setup_state')) {
    /**
     * Where the merchant is: each step's done-ness computed from ACTUAL config,
     * so the wizard is re-entrant and survives partial runs.
     *
     * @return array{plugins:bool, missing_plugins:array<int,string>, theme:bool, theme_slug:string,
     *               brand:bool, commerce:bool, commerce_env:string, identity:bool, identity_skipped:bool}
     */
    function doeh_setup_state(): array
    {
        $required = ['doeh-identity', 'doeh-commerce', 'doeh-commerce-storefront'];
        $activePlugins = json_decode((string) bp_option('active_plugins', '[]'), true) ?: [];
        $missing = array_values(array_diff($required, $activePlugins));

        $themeSlug = \App\Support\Theme::active();
        $themeMeta = \App\Support\Theme::meta($themeSlug);
        $isDoehTheme = in_array('doeh-commerce-storefront', (array) ($themeMeta['requires'] ?? []), true);

        // Brand counts as done when the theme's Brand-group has ANY saved value.
        $brand = false;
        foreach (\App\Support\Theme::settingsSchema($themeSlug) as $f) {
            if (($f['group'] ?? '') === 'Brand' && trim((string) bp_option($f['name'] ?? '')) !== '') {
                $brand = true;
                break;
            }
        }

        return [
            'plugins'          => $missing === [],
            'missing_plugins'  => $missing,
            'theme'            => $isDoehTheme,
            'theme_slug'       => $themeSlug,
            'brand'            => $brand,
            'commerce'         => (string) bp_plugin_option('doeh-commerce', 'secret_key') !== '',
            'commerce_env'     => bp_plugin_option('doeh-commerce', 'environment') ?: 'sandbox',
            'identity'         => (string) bp_plugin_option('doeh-identity', 'client_id') !== ''
                                  && (string) bp_plugin_option('doeh-identity', 'publishable_key') !== '',
            'identity_skipped' => bp_plugin_option('doeh-setup', 'identity_skipped') === 'yes',
        ];
    }
}

if (! function_exists('doeh_setup_option_set')) {
    /** Save one option the same way the theme customize page does. */
    function doeh_setup_option_set(string $name, string $value): void
    {
        \App\Models\Bp_options::updateOrCreate(
            ['option_name' => $name],
            ['option_value' => $value, 'autoload' => 'yes']
        );
    }
}
