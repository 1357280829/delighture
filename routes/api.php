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
    Route::delete('authorizations', 'AuthorizationsController@destroy');

    Route::middleware('token.refresh')->group(function () {
        //  这里加入需要授权认证的接口
    });
});
