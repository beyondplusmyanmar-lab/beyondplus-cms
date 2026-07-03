<?php

/**
 * Routes shipped by the Logbook plugin. Loaded only while the plugin is active.
 * Adds an admin page at /bp-admin/logbook that renders the plugin's own view.
 */

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Route::middleware('admins')->prefix('bp-admin')->group(function () {
    Route::get('logbook', function () {
        $entries = Schema::hasTable('bp_logbook')
            ? DB::table('bp_logbook')->orderByDesc('id')->limit(50)->get()
            : collect();

        return view('logbook::report', ['entries' => $entries]);
    });
});
