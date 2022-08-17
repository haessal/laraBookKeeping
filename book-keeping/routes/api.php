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

Route::get('/v1/accounts', GetAccountsActionApi::class);

Route::post('/v1/slips', PostSlipsActionApi::class);
Route::get('/v1/slips/{slipId}', GetSlipsSlipIdActionApi::class);
Route::patch('/v1/slips/{slipId}', PatchSlipsActionApi::class);

Route::get('/v1/slipentries', GetSlipEntriesActionApi::class);
Route::get('/v1/slipentries/{slipEntryId}', GetSlipEntriesSlipEntryIdActionApi::class);
Route::patch('/v1/slipentries/{slipEntryId}', PatchSlipEntriesActionApi::class);
Route::delete('/v1/slipentries/{slipEntryId}', DeleteSlipEntriesActionApi::class);
