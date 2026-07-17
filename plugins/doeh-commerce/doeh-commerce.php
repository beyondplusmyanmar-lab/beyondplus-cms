<?php

/**
 * DOEH Commerce — server-side connector to the DOEH Orders API for Beyond Plus CMS.
 *
 * ── MODEL A (the boundary this plugin is built around): the merchant secret key
 *    lives ONLY in this plugin's server-side settings and never reaches the
 *    browser; a customer's sign-in token is NEVER used as the order credential.
 *    The plugin owns API auth, request signing, payload mapping, error
 *    normalization and order retrieval — and NOTHING else. No cart, no catalog,
 *    no checkout UI: themes own all presentation and call this connector.
 *
 * How a theme uses it (server-side, from its own checkout controller/route):
 *
 *     $result = doeh_commerce()?->createOrder([
 *         'lines'    => [['sku' => 'COFFEE-250', 'qty' => 2]],
 *         'customer' => ['phone' => '+95912345678'],   // optional
 *     ]);
 *     if ($result && $result['ok']) { $order = $result['order']; ... }
 *
 * Or, without a hard class dependency, through the hook system:
 *
 *     $result = bp_apply_filters('doeh_create_order', null, $submission);
 *     $order  = bp_apply_filters('doeh_get_order',   null, $orderId);
 *     $report = bp_apply_filters('doeh_list_orders', null, ['from' => …, 'to' => …]);
 *
 * This file registers hooks only; it is loaded solely while the plugin is active.
 */

use Doeh\Commerce\DoehCommerceClient;

// The loader require_once's only the main file — no PSR-4 for plugins — so the
// connector class is pulled in explicitly here.
require_once __DIR__.'/src/DoehCommerceClient.php';

if (! function_exists('doeh_commerce_enabled')) {
    /** True when the operator switched submission on AND supplied a secret key. */
    function doeh_commerce_enabled(): bool
    {
        if ((bp_plugin_option('doeh-commerce', 'enabled') ?: 'yes') !== 'yes') {
            return false;
        }

        return (string) bp_plugin_option('doeh-commerce', 'secret_key') !== '';
    }
}

if (! function_exists('doeh_commerce')) {
    /**
     * The configured connector, or null when the plugin is off / unconfigured.
     * A fresh instance per call is fine — it holds only config, no connection.
     */
    function doeh_commerce(): ?DoehCommerceClient
    {
        if (! doeh_commerce_enabled()) {
            return null;
        }

        return new DoehCommerceClient(
            secretKey:           (string) bp_plugin_option('doeh-commerce', 'secret_key'),
            environment:         bp_plugin_option('doeh-commerce', 'environment') ?: 'sandbox',
            defaultFulfillment:  (string) bp_plugin_option('doeh-commerce', 'default_fulfillment'),
        );
    }
}

// ── Capabilities as hooks ─────────────────────────────────────────────────────
// Themes may depend on the class directly (doeh_commerce()) or stay decoupled and
// go through these filters, which return the connector's normalized array — or
// leave the passed-through default (null) untouched when the plugin is inactive,
// so a theme degrades gracefully exactly like it does with DOEH Identity.
bp_add_filter('doeh_create_order', function ($default, $submission = [], $idempotencyKey = null) {
    $client = doeh_commerce();

    return $client ? $client->createOrder((array) $submission, $idempotencyKey) : $default;
});

bp_add_filter('doeh_get_order', function ($default, $id = '') {
    $client = doeh_commerce();

    return $client ? $client->getOrder((string) $id) : $default;
});

bp_add_filter('doeh_list_orders', function ($default, $query = []) {
    $client = doeh_commerce();

    return $client ? $client->listOrders((array) $query) : $default;
});
