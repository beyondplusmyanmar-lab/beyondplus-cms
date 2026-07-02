<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


/*
 * Public, read-only CMS content API. Unauthenticated but rate limited by the
 * "api" middleware group (throttle:60,1 per IP). See CMSController.
 */
Route::group([
	'prefix' => 'm',
	'as' => 'api.',
	'namespace' => 'Api\V1\Customer'
], function () {
	Route::get('/home', 'CMSController@home');
	Route::get('/posts', 'CMSController@posts');
	Route::get('/posts/{slug}', 'CMSController@post');
	Route::get('/pages', 'CMSController@pages');
	Route::get('/pages/{slug}', 'CMSController@page');
	Route::get('/menus', 'CMSController@menus');
	Route::get('/categories', 'CMSController@categories');
	Route::get('/categories/{slug}/posts', 'CMSController@categoryPosts');
	Route::get('/sliders', 'CMSController@sliders');
	Route::get('/news', 'CMSController@news');
});

