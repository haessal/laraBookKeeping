<?php

use App\Http\Controllers\api\v1\DeleteBooksDefaultActionApi;
use App\Http\Controllers\api\v1\DeleteBooksPermissionsActionApi;
use App\Http\Controllers\api\v1\DeleteSlipEntriesActionApi;
use App\Http\Controllers\api\v1\GetAccountsActionApi;
use App\Http\Controllers\api\v1\GetBooksActionApi;
use App\Http\Controllers\api\v1\GetBooksBookIdActionApi;
use App\Http\Controllers\api\v1\GetBooksDefaultActionApi;
use App\Http\Controllers\api\v1\GetBooksPermissionsActionApi;
use App\Http\Controllers\api\v1\GetSlipEntriesActionApi;
use App\Http\Controllers\api\v1\GetSlipEntriesSlipEntryIdActionApi;
use App\Http\Controllers\api\v1\GetSlipsSlipIdActionApi;
use App\Http\Controllers\api\v1\PatchBooksActionApi;
use App\Http\Controllers\api\v1\PatchSlipEntriesActionApi;
use App\Http\Controllers\api\v1\PatchSlipsActionApi;
use App\Http\Controllers\api\v1\PostBooksActionApi;
use App\Http\Controllers\api\v1\PostBooksDefaultActionApi;
use App\Http\Controllers\api\v1\PostBooksPermissionsActionApi;
use App\Http\Controllers\api\v1\PostSlipsActionApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::get('/books', GetBooksActionApi::class);
    Route::get('/books/default', GetBooksDefaultActionApi::class);
    Route::get('/books/{bookId}', GetBooksBookIdActionApi::class);
    Route::post('/books', PostBooksActionApi::class);
    Route::patch('/books/{bookId}', PatchBooksActionApi::class);
    Route::post('/books/{bookId}/default', PostBooksDefaultActionApi::class);
    Route::delete('/books/{bookId}/default', DeleteBooksDefaultActionApi::class);
    Route::get('/books/{bookId}/permissions', GetBooksPermissionsActionApi::class);
    Route::post('/books/{bookId}/permissions', PostBooksPermissionsActionApi::class);
    Route::delete('/books/{bookId}/permissions', DeleteBooksPermissionsActionApi::class);

    Route::get('/accounts', GetAccountsActionApi::class);

    Route::post('/slips', PostSlipsActionApi::class);
    Route::get('/slips/{slipId}', GetSlipsSlipIdActionApi::class);
    Route::patch('/slips/{slipId}', PatchSlipsActionApi::class);

    Route::get('/slipentries', GetSlipEntriesActionApi::class);
    Route::get('/slipentries/{slipEntryId}', GetSlipEntriesSlipEntryIdActionApi::class);
    Route::patch('/slipentries/{slipEntryId}', PatchSlipEntriesActionApi::class);
    Route::delete('/slipentries/{slipEntryId}', DeleteSlipEntriesActionApi::class);
});
