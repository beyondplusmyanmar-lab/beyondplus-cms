<?php

/**
 * Logbook — a demo plugin with its own schema.
 *
 * Its migration (plugins/logbook/migrations) creates the bp_logbook table when
 * the plugin is activated. Here it just records a "page view" row on each
 * front-end footer render, to show the plugin using its own table.
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

bp_add_action('theme_footer', function () {
    if (Schema::hasTable('bp_logbook')) {
        try {
            DB::table('bp_logbook')->insert([
                'event'      => 'page_view',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // never let a plugin break the page
        }
    }
});
