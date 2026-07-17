<?php

/**
 * DOEH Commerce Storefront — the storefront flow over the DOEH Commerce connector.
 *
 * This is the ONLY place in the bridge that owns commerce UI, and it owns it as a
 * pluggable FLOW: fixture → cart → doeh_commerce()->createOrder() → confirmation.
 * It ships default templates so it works standalone, but a theme may override
 * theme.<active>.commerce.{shop,cart,order} to render the same flow in its own
 * chrome (see doeh_commerce_view()) — the WooCommerce template-override model.
 *
 * It holds no secret and speaks to DOEH only through doeh_commerce(); the cart is
 * plain session state. Routes + views live here because a THEME cannot own routes
 * in this CMS; the default pages are self-contained so the flow works on any theme.
 *
 * This file registers the fixture + view helpers; the flow is in routes.php.
 */

if (! function_exists('doeh_storefront_products')) {
    /**
     * The product fixture (manifest `products_json`), normalized to
     * [['sku'=>…, 'name'=>…, 'price_hint'=>…], …]. Rows without a SKU are dropped.
     *
     * @return array<int, array{sku:string, name:string, price_hint:string}>
     */
    function doeh_storefront_products(): array
    {
        $raw = bp_plugin_option('doeh-commerce-storefront', 'products_json');
        $rows = is_string($raw) ? json_decode($raw, true) : $raw;

        // Fresh install: the operator hasn't saved settings yet, so fall back to
        // the manifest's declared default (one source of truth) rather than show
        // an empty shop.
        if (! is_array($rows) || $rows === []) {
            foreach (\App\Support\Plugin::settingsSchema('doeh-commerce-storefront') as $field) {
                if (($field['name'] ?? '') === 'products_json') {
                    $rows = $field['default'] ?? [];
                    break;
                }
            }
        }
        if (! is_array($rows)) {
            return [];
        }

        $out = [];
        foreach ($rows as $r) {
            $sku = trim((string) ($r['sku'] ?? ''));
            if ($sku === '') {
                continue;
            }
            $out[$sku] = [
                'sku'        => $sku,
                'name'       => trim((string) ($r['name'] ?? $sku)) ?: $sku,
                'price_hint' => trim((string) ($r['price_hint'] ?? '')),
            ];
        }

        return array_values($out); // de-duped by SKU
    }
}

if (! function_exists('doeh_commerce_view')) {
    /**
     * Render a commerce-flow page, letting the ACTIVE THEME own the presentation.
     *
     * If the theme provides `theme.<active>.commerce.<name>` it is used (so a
     * business theme skins the cart/checkout/confirmation in its own chrome);
     * otherwise the plugin's self-contained default view is the fallback. This is
     * the WooCommerce template-override model: the plugin owns the flow + default
     * templates, the theme owns the look.
     *
     * @param array<string,mixed> $data
     */
    function doeh_commerce_view(string $name, array $data)
    {
        $themeView = 'theme.'.\App\Support\Theme::active().'.commerce.'.$name;
        $view = view()->exists($themeView) ? $themeView : 'doeh-commerce-storefront::'.$name;

        return response()->view($view, $data);
    }
}
