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

if (! function_exists('doeh_storefront_fulfillment_types')) {
    /**
     * The fulfilment choices this storefront OFFERS, declared by the active theme
     * (manifest `fulfillment_types`, e.g. ["pickup","dine_in"]). The theme knows
     * its vertical: a restaurant offers dine-in, a service business offers none
     * ([]). A theme that declares nothing gets ['pickup'] — the pre-v1.1 shape.
     *
     * This is a PREFERENCE the customer states, nothing more. The storefront never
     * computes fees, routes, ETAs or rider state; the Orders API stays the
     * authority on which types it accepts (today it refuses `delivery` with
     * EDGE_FULFILLMENT_NOT_AVAILABLE until the platform's delivery slice lands —
     * offering it here is a manifest flip, not a code change, when that day comes).
     *
     * @return array<int, string> subset of pickup|delivery|dine_in
     */
    function doeh_storefront_fulfillment_types(): array
    {
        $meta = \App\Support\Theme::meta(\App\Support\Theme::active());
        $declared = $meta['fulfillment_types'] ?? null;
        if (! is_array($declared)) {
            return ['pickup'];
        }

        // Whitelist mirrors the connector's FULFILLMENT constant (the wire values).
        $known = ['pickup', 'delivery', 'dine_in'];

        return array_values(array_unique(array_filter(
            array_map('strval', $declared),
            fn (string $t) => in_array($t, $known, true)
        )));
    }
}

if (! function_exists('doeh_storefront_fulfillment_label')) {
    /** Display copy for a fulfilment type in the DEFAULT templates (a theme owns its own words). */
    function doeh_storefront_fulfillment_label(string $type): array
    {
        return [
            'pickup'   => ['Pickup', 'Collect from the store'],
            'delivery' => ['Delivery', 'Delivered to your address'],
            'dine_in'  => ['Dine in', 'Enjoy at the store'],
        ][$type] ?? [ucfirst($type), ''];
    }
}

if (! function_exists('doeh_storefront_format_money')) {
    /**
     * Minor units → display, currency-aware. MMK (and other zero-decimal
     * currencies) are stored as whole units — dividing by 100 would show
     * 1,500 MMK as "15"; only 2-decimal currencies get the /100.
     */
    function doeh_storefront_format_money($minor, string $currency): string
    {
        $zeroDecimal = ['MMK', 'JPY', 'KRW', 'VND', 'IDR', 'LAK', 'KHR'];
        $exp = in_array(strtoupper($currency), $zeroDecimal, true) ? 0 : 2;
        $n = number_format($exp === 0 ? (int) $minor : $minor / (10 ** $exp), $exp);

        return $currency === '' ? $n : $n.' '.$currency;
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
