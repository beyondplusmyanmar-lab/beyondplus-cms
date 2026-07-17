<?php

/**
 * DOEH Commerce Demo — a reference checkout over the DOEH Commerce connector.
 *
 * This is intentionally the ONLY place in the bridge that owns commerce UI, and
 * it is a REFERENCE: it shows a merchant (and a theme author) the exact shape of
 * the flow — fixture → cart → doeh_commerce()->createOrder() → confirmation —
 * and nothing more. A production theme would render its own cart and pages and
 * call the same connector; this plugin is what they copy from.
 *
 * It holds no secret and speaks to DOEH only through doeh_commerce(); the cart is
 * plain session state. Routes + views live here (a THEME cannot own routes in this
 * CMS); the pages are self-contained so the reference renders on any theme.
 *
 * This file registers the fixture helper only; the flow is in routes.php.
 */

if (! function_exists('doeh_demo_products')) {
    /**
     * The demo product fixture (manifest `products_json`), normalized to
     * [['sku'=>…, 'name'=>…, 'price_hint'=>…], …]. Rows without a SKU are dropped.
     *
     * @return array<int, array{sku:string, name:string, price_hint:string}>
     */
    function doeh_demo_products(): array
    {
        $raw = bp_plugin_option('doeh-commerce-demo', 'products_json');
        $rows = is_string($raw) ? json_decode($raw, true) : $raw;

        // Fresh install: the operator hasn't saved settings yet, so fall back to
        // the manifest's declared default (one source of truth) rather than show
        // an empty shop.
        if (! is_array($rows) || $rows === []) {
            foreach (\App\Support\Plugin::settingsSchema('doeh-commerce-demo') as $field) {
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
