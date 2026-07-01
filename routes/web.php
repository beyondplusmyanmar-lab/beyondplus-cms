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





Route::group(['prefix' => 'bp-admin','namespace'  =>  'BpAdmin', 'middleware' => 'admins'], function () {

	Route::get('department', 'DepartmentController@index');
    Route::get('department/create', 'DepartmentController@create');
    Route::post('department', 'DepartmentController@store');
    Route::get('department/{id}', 'DepartmentController@show');
    Route::get('department/{id}/edit', 'DepartmentController@edit');
    Route::put('department/{id}', 'DepartmentController@update');
    Route::delete('department/{id}', 'DepartmentController@destroy');

    Route::get('department/{id}/translate', 'DepartmentController@translate');
    Route::get('department/delete/{id}','DepartmentController@destroy');

});