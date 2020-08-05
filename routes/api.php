<?php

use CloudCreativity\LaravelJsonApi\Facades\JsonApi;
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

Route::namespace('Api')->prefix('api/v1')->group(function () {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@signup');
    Route::post('/password-forgot', 'AuthController@forgot');
    Route::post('/password-reset', 'AuthController@reset');

    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::post('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});


JsonApi::register('v1')->middleware('auth:api')->withNamespace('Api')->routes(function ($api) {
    $api->resource('products');
    $api->resource('users');
});
