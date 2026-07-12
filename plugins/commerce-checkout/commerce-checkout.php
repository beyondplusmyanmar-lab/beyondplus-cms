<?php

/**
 * Commerce Checkout — session cart + orders on top of the Commerce plugin.
 *
 * Loaded only while active. It hooks the `commerce_product_actions` slot that
 * the Commerce plugin's product cards expose, adding an "Add to cart" button —
 * so the two plugins connect without Commerce depending on this one. Orders are
 * inquiry / cash-on-delivery only; no payment data is ever collected.
 */

// Inject an "Add to cart" button into every Commerce product card.
bp_add_filter('commerce_product_actions', function ($html, $product) {
    if (bp_plugin_option('commerce-checkout', 'checkout_enabled', 'yes') !== 'yes') {
        return $html;
    }
    try {
        return $html.view('commerce-checkout::partials.add-to-cart', ['p' => $product])->render();
    } catch (\Throwable $e) {
        return $html;
    }
}, 10);
