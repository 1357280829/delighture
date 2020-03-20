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

Route::namespace('Api')->group(function () {

    Route::post('authorizations', 'AuthorizationsController@store');

    Route::middleware('token.refresh')->group(function () {
        Route::delete('authorizations', 'AuthorizationsController@destroy');

        Route::post('users', 'UsersController@store');
        Route::get('users', 'UsersController@index');
    });
});
