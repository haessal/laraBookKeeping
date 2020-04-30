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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/v1/accounts', api\v1\GetAccountsActionAPI::class);
//Route::post('/v1/slips', api\v1\PostSlipsActionAPI::class);
//Route::post('/v1/{bookID}/slips', api\v1\POSTSlipsActionAPI::class);