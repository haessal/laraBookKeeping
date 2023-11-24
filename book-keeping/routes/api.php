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

Route::get('/v1/accounts', api\v1\GetAccountsActionApi::class);

Route::post('/v1/slips', api\v1\PostSlipsActionApi::class);
Route::get('/v1/slips/{slipId}', api\v1\GetSlipsSlipIdActionApi::class);
Route::patch('/v1/slips/{slipId}', api\v1\PatchSlipsActionApi::class);

Route::get('/v1/slipentries', api\v1\GetSlipEntriesActionApi::class);
Route::get('/v1/slipentries/{slipEntryId}', api\v1\GetSlipEntriesSlipEntryIdActionApi::class);
Route::patch('/v1/slipentries/{slipEntryId}', api\v1\PatchSlipEntriesActionApi::class);
Route::delete('/v1/slipentries/{slipEntryId}', api\v1\DeleteSlipEntriesActionApi::class);

Route::get('/v1/export/books', api\v1\ExportBooksActionApi::class);
