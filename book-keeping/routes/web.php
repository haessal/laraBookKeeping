<?php

use App\Http\Controllers\page\v1\CreateSlipActionHTML;
use App\Http\Controllers\page\v1\FindSlipsActionHTML;
use App\Http\Controllers\page\v1\ShowAccountsListActionHTML;
use App\Http\Controllers\page\v1\ShowStatementsActionHTML;
use App\Http\Controllers\page\v1\ShowTopActionHTML;
use App\Http\Controllers\page\v2\CreateAccountsActionHtml;
use App\Http\Controllers\page\v2\RedirectAccountsSettingsActionHtml;
use App\Http\Controllers\page\v2\ShowAccountsActionHtml;
use App\Http\Controllers\page\v2\ShowAccountsSettingsActionHtml;
use App\Http\Controllers\page\v2\ShowHomeActionHtml;
use App\Http\Controllers\page\v2\UpdateAccountsGroupActionHtml;
use App\Http\Controllers\page\v2\UpdateAccountsItemActionHtml;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Settings\UpdateAccessTokenActionHtml;
use App\Http\Controllers\ShowDashboardActionHtml;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', ShowDashboardActionHtml::class)->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::match(['get', 'post', 'delete'], '/settings/tokens', UpdateAccessTokenActionHtml::class)->middleware(['verified'])->name('settings_tokens');

Route::prefix('/page/v1')->group(function () {
    Route::get('/top', ShowTopActionHTML::class)->name('v1_top');
    Route::match(['get', 'post'], '/findslips', FindSlipsActionHTML::class)->name('v1_findslips');
    Route::match(['get', 'post'], '/slip', CreateSlipActionHTML::class)->name('v1_slip');
    Route::match(['get', 'post'], '/statements', ShowStatementsActionHTML::class)->name('v1_statements');
    Route::get('/accountslist', ShowAccountsListActionHTML::class)->name('v1_accountslist');
});

Route::prefix('/page/v2/books/{bookId}')->group(function () {
    Route::get('', function ($bookId) {
        return redirect()->route('v2_home', ['bookId' => $bookId]);
    })->name('v2');
    Route::get('/home', ShowHomeActionHtml::class)->name('v2_home');
    Route::get('/accounts', ShowAccountsActionHtml::class)->name('v2_accounts');
    Route::match(['get', 'post'], '/accounts/new', CreateAccountsActionHtml::class)->name('v2_accounts_new');
    Route::get('/accounts/settings', ShowAccountsSettingsActionHtml::class)->name('v2_accounts_settings');
    Route::post('/accounts/settings', RedirectAccountsSettingsActionHtml::class)->name('v2_accounts_settings_redirect');
    Route::match(['get', 'post'], '/accounts/settings/groups/{accountsGroupId}', UpdateAccountsGroupActionHtml::class)->name('v2_accounts_groups');
    Route::match(['get', 'post'], '/accounts/settings/items/{accountsItemId}', UpdateAccountsItemActionHtml::class)->name('v2_accounts_items');
    Route::get('/settings', function () {
        return view('welcome');
    })->name('v2_settings');
});

require __DIR__.'/auth.php';
