<?php

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

Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/settings', function () {
    return redirect()->route('settings_tokens');
})->name('settings');
Route::match(['get', 'post', 'delete'], '/settings/tokens', Settings\UpdateAccessTokenActionHTML::class)->name('settings_tokens');

Route::get('/page/v1/top', v1\ShowTopActionHTML::class)->name('v1_top');
Route::match(['get', 'post'], '/page/v1/findslips', v1\FindSlipsActionHTML::class)->name('v1_findslips');
Route::match(['get', 'post'], '/page/v1/slip', v1\CreateSlipActionHTML::class)->name('v1_slip');
Route::match(['get', 'post'], '/page/v1/statements', v1\ShowStatementsActionHTML::class)->name('v1_statements');
Route::get('/page/v1/accountslist', v1\ShowAccountsListActionHTML::class)->name('v1_accountslist');
