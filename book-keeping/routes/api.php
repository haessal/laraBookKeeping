<?php

use App\Http\Controllers\api\v1\DeleteBooksPermissions;
use App\Http\Controllers\api\v1\DeleteSlipEntriesActionApi;
use App\Http\Controllers\api\v1\GetAccountsActionApi;
use App\Http\Controllers\api\v1\GetBooksActionApi;
use App\Http\Controllers\api\v1\GetBooksBookIdActionApi;
use App\Http\Controllers\api\v1\GetBooksPermissions;
use App\Http\Controllers\api\v1\GetSlipEntriesActionApi;
use App\Http\Controllers\api\v1\GetSlipEntriesSlipEntryIdActionApi;
use App\Http\Controllers\api\v1\GetSlipsSlipIdActionApi;
use App\Http\Controllers\api\v1\PatchBooksActionApi;
use App\Http\Controllers\api\v1\PatchSlipEntriesActionApi;
use App\Http\Controllers\api\v1\PatchSlipsActionApi;
use App\Http\Controllers\api\v1\PostBooksActionApi;
use App\Http\Controllers\api\v1\PostBooksPermissions;
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
    Route::get('/books', GetBooksActionApi::class);
    /*
        Route::post('/books', PostBooksActionApi::class);
        Route::get('/books/{bookId}', GetBooksBookIdActionApi::class);
        Route::patch('/books/{bookId}', PatchBooksActionApi::class);

        Route::get('/books/{bookId}/permissions', GetBooksPermissions::class);
        Route::post('/books/{bookId}/permissions', PostBooksPermissions::class);
        Route::delete('/books/{bookId}/permissions', DeleteBooksPermissions::class);
    */

    Route::get('/accounts', GetAccountsActionApi::class);

    Route::post('/slips', PostSlipsActionApi::class);
    Route::get('/slips/{slipId}', GetSlipsSlipIdActionApi::class);
    Route::patch('/slips/{slipId}', PatchSlipsActionApi::class);

    Route::get('/slipentries', GetSlipEntriesActionApi::class);
    Route::get('/slipentries/{slipEntryId}', GetSlipEntriesSlipEntryIdActionApi::class);
    Route::patch('/slipentries/{slipEntryId}', PatchSlipEntriesActionApi::class);
    Route::delete('/slipentries/{slipEntryId}', DeleteSlipEntriesActionApi::class);
});
