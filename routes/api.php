<?php

use Illuminate\Http\Request;

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

Route::group(['middleware' => 'auth:api'], function () {

    Route::resource('post', 'PostController')->except(['create', 'edit']);

    Route::resource('user', 'UserController')->only(['index']);

    Route::get('me', 'UserController@me')->name('me');
});
