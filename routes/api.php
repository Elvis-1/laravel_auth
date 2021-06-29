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

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth',
    'namespace' => 'App\Http\Controllers',

], function ($router) {
     
    Route::post('login','AuthController@login')->name('login');
    // Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::get('profile', 'AuthController@profile');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('update', 'AuthController@update');
    Route::post('logout', 'AuthController@logout');

});
