<?php

/**
 * DOEH Identity routes — loaded only while the plugin is active.
 *
 *   GET /doeh/callback        the OAuth redirect target. Renders a themed page
 *                             whose ONLY job is to host the JS that finishes the
 *                             PKCE code exchange in the browser (invariant P1 —
 *                             the code and tokens never reach PHP).
 *   GET /doeh-identity/widget.js   the single self-contained JS core, served from
 *                             this plugin folder with a long cache and a version
 *                             query for busting (no CDN, no external assets).
 *
 * Both are 'web' middleware (front-end pages). Neither accepts a token.
 */

use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    // The callback page. It carries ?code&state in the URL; the browser JS reads
    // and exchanges them. PHP renders chrome only and reads nothing from the query.
    Route::get('/doeh/callback', function () {
        return view('doeh-identity::callback');
    })->name('doeh-identity.callback');

    // The JS core. Served with the plugin's own version for cache-busting; the
    // file is static and self-contained.
    Route::get('/doeh-identity/widget.js', function () {
        $path = __DIR__.'/assets/doeh-identity.js';
        abort_unless(is_file($path), 404);

        return response(file_get_contents($path), 200, [
            'Content-Type'  => 'application/javascript; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    })->name('doeh-identity.widget');
});
