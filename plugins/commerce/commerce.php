<?php

/**
 * Commerce — product catalogue plugin.
 *
 * Loaded only while active. It registers hooks the Business theme exposes, so
 * the theme's Featured Products section and hero Shop button light up once
 * products exist — with zero changes to the theme or core. Every hook guards
 * its own table and fails silently so a plugin issue can never break the page.
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Featured Products — fills the theme's `business_featured_products` filter with
 * a grid of themed product cards. Returns the incoming markup unchanged when
 * there is nothing to show, so the theme hides the whole section.
 */
bp_add_filter('business_featured_products', function ($html) {
    if (! Schema::hasTable('commerce_products')) {
        return $html;
    }
    try {
        $limit = (int) bp_plugin_option('commerce', 'featured_count', '4');
        $limit = $limit > 0 ? $limit : 4;

        $products = DB::table('commerce_products')
            ->where('is_active', 1)->where('is_featured', 1)
            ->orderBy('sort_order')->orderByDesc('id')
            ->limit($limit)->get();

        if ($products->isEmpty()) {
            return $html;
        }

        return $html.view('commerce::partials.featured', [
            'products' => $products,
            'currency' => bp_plugin_option('commerce', 'currency', 'MMK'),
        ])->render();
    } catch (\Throwable $e) {
        return $html;
    }
});

/**
 * Hero — add a "Shop Now" button to the theme's `business_hero_actions` filter
 * when the shop is enabled and at least one product is live.
 */
bp_add_filter('business_hero_actions', function ($html) {
    if (bp_plugin_option('commerce', 'shop_enabled', 'yes') !== 'yes') {
        return $html;
    }
    if (! Schema::hasTable('commerce_products')) {
        return $html;
    }
    try {
        if (DB::table('commerce_products')->where('is_active', 1)->exists()) {
            $label = app()->getLocale() === 'mm' ? 'ဈေးဝယ်ရန်' : 'Shop Now';
            return $html.'<a href="'.e(url('/shop')).'" class="btn btn-light btn-lg">'.e($label).'</a>';
        }
    } catch (\Throwable $e) {
        // fall through
    }
    return $html;
});
