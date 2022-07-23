<?php

use App\Http\Controllers\Settings\UpdateAccessTokenActionHtml;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['verified'])->name('dashboard');

Route::match(['get', 'post', 'delete'], '/settings/tokens', UpdateAccessTokenActionHtml::class)->middleware(['verified'])->name('settings_tokens');

require __DIR__.'/auth.php';
