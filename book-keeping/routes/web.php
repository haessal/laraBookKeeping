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

Route::get('/home', ShowDashboardActionHtml::class)->name('home');

Route::get('/settings', function () {
    return redirect()->route('settings_tokens');
})->name('settings');
Route::match(['get', 'post', 'delete'], '/settings/tokens', Settings\UpdateAccessTokenActionHTML::class)->name('settings_tokens');

Route::get('/page/v1/top', v1\ShowTopActionHTML::class)->name('v1_top');
Route::match(['get', 'post'], '/page/v1/findslips', v1\FindSlipsActionHTML::class)->name('v1_findslips');
Route::match(['get', 'post'], '/page/v1/slip', v1\CreateSlipActionHTML::class)->name('v1_slip');
Route::match(['get', 'post'], '/page/v1/statements', v1\ShowStatementsActionHTML::class)->name('v1_statements');
Route::get('/page/v1/accountslist', v1\ShowAccountsListActionHTML::class)->name('v1_accountslist');

Route::prefix('/page/v2/books/{bookId}')->group(function () {
    Route::get('', function ($bookId) {
        return redirect()->route('v2_home', ['bookId' => $bookId]);
    })->name('v2');
    Route::get('/home', v2\ShowHomeActionHtml::class)->name('v2_home');
    Route::get('/accounts', v2\ShowAccountsActionHtml::class)->name('v2_accounts');
    Route::get('/accounts/groups/new', v2\CreateAccountsGroupActionHtml::class)->name('v2_accounts_groups_new');
    Route::get('/accounts/settings', v2\ShowAccountsSettingsActionHtml::class)->name('v2_accounts_settings');
    Route::post('/accounts/settings', v2\RedirectAccountsSettingsActionHtml::class)->name('v2_accounts_settings_redirect');
    Route::match(['get', 'post'], '/accounts/settings/groups/{accountsGroupId}', v2\UpdateAccountsGroupActionHtml::class)->name('v2_accounts_groups');
    Route::match(['get', 'post'], '/accounts/settings/items/{accountsItemId}', v2\UpdateAccountsItemActionHtml::class)->name('v2_accounts_items');
    Route::get('/settings', function () {
        return view('welcome');
    })->name('v2_settings');
});
