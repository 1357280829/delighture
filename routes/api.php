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

    Route::get('es/demo1', 'ESExampleController@demo1');
    Route::get('es/demo2', 'ESExampleController@demo2');
    Route::get('es/demo3', 'ESExampleController@demo3');
    Route::get('es/demo4', 'ESExampleController@demo4');
    Route::get('es/demo5', 'ESExampleController@demo5');
    Route::get('es/demo6', 'ESExampleController@demo6');

    Route::post('authorizations', 'AuthorizationsController@store');

    Route::middleware('token.refresh')->group(function () {
        Route::delete('authorizations', 'AuthorizationsController@destroy');

        Route::get('users', 'UsersController@index');
        Route::post('users', 'UsersController@store');
    });
});
