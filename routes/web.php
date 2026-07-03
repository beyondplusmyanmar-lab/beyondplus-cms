<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Auth::routes();



// Main GET routes with locale
// Route::prefix('{lang?}')->middleware('locale')->group(function() {

//     Route::get('/home', 'HomeController@index')->name('home');
    
// });





// SPA example demo. Served from resources/ (not public/) and gated by the
// api_enabled toggle, so it returns 503 when the API is disabled — matching the
// API and its docs. Registered here (before the front catch-all) to keep the
// /spa-example.html URL.
Route::get('/spa-example.html', function () {
    return response(file_get_contents(resource_path('spa-example.html')))
        ->header('Content-Type', 'text/html; charset=UTF-8');
})->middleware('api.enabled');

// Admin routes are registered in routes/beyondplus-cms.php (bp-admin group).