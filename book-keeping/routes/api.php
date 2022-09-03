<?php

use App\Http\Controllers\api\v1\DeleteSlipEntriesActionApi;
use App\Http\Controllers\api\v1\GetAccountsActionApi;
use App\Http\Controllers\api\v1\GetSlipEntriesActionApi;
use App\Http\Controllers\api\v1\GetSlipEntriesSlipEntryIdActionApi;
use App\Http\Controllers\api\v1\GetSlipsSlipIdActionApi;
use App\Http\Controllers\api\v1\PatchSlipEntriesActionApi;
use App\Http\Controllers\api\v1\PatchSlipsActionApi;
use App\Http\Controllers\api\v1\PostSlipsActionApi;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::get('/accounts', GetAccountsActionApi::class);

    Route::post('/slips', PostSlipsActionApi::class);
    Route::get('/slips/{slipId}', GetSlipsSlipIdActionApi::class);
    Route::patch('/slips/{slipId}', PatchSlipsActionApi::class);

    Route::get('/slipentries', GetSlipEntriesActionApi::class);
    Route::get('/slipentries/{slipEntryId}', GetSlipEntriesSlipEntryIdActionApi::class);
    Route::patch('/slipentries/{slipEntryId}', PatchSlipEntriesActionApi::class);
    Route::delete('/slipentries/{slipEntryId}', DeleteSlipEntriesActionApi::class);
});
