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
 * Promotions — fills the theme's `business_promotions` slot with campaigns that
 * are active AND currently within their (optional) start/end window.
 */
bp_add_filter('business_promotions', function ($html) {
    if (! Schema::hasTable('commerce_promotions')) {
        return $html;
    }
    try {
        $now = now();
        $promos = DB::table('commerce_promotions')
            ->where('is_active', 1)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now))
            ->orderBy('sort_order')->orderByDesc('id')->get();

        if ($promos->isEmpty()) {
            return $html;
        }

        return $html.view('commerce::partials.promotions', ['promos' => $promos])->render();
    } catch (\Throwable $e) {
        return $html;
    }
});

/**
 * Store Locations — fills the theme's `business_store_locations` slot with the
 * active branches.
 */
bp_add_filter('business_store_locations', function ($html) {
    if (! Schema::hasTable('commerce_branches')) {
        return $html;
    }
    try {
        $branches = DB::table('commerce_branches')
            ->where('is_active', 1)
            ->orderBy('sort_order')->orderByDesc('id')->get();

        if ($branches->isEmpty()) {
            return $html;
        }

        return $html.view('commerce::partials.locations', ['branches' => $branches])->render();
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
