<?php

use App\Http\Controllers\Auth\PersonalAccessTokenController;
use App\Http\Controllers\page\CreateBookActionHtml;
use App\Http\Controllers\page\ShowDashboardActionHtml;
use App\Http\Controllers\page\v1\CreateSlipActionHTML;
use App\Http\Controllers\page\v1\FindSlipsActionHTML;
use App\Http\Controllers\page\v1\ShowAccountsListActionHTML;
use App\Http\Controllers\page\v1\ShowStatementsActionHTML;
use App\Http\Controllers\page\v1\ShowTopActionHTML;
use App\Http\Controllers\page\v2\ShowBookHomeActionHtml;
use App\Http\Controllers\page\v2\ShowBookSettingsActionHtml;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', ShowDashboardActionHtml::class)->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/books/new', CreateBookActionHtml::class)->middleware(['auth', 'verified'])->name('books.new');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/token', [PersonalAccessTokenController::class, 'get_creation_date']);
    Route::post('/profile/token', [PersonalAccessTokenController::class, 'store']);
    Route::delete('/profile/token', [PersonalAccessTokenController::class, 'destroy']);
});

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
    Route::get('/home', ShowBookHomeActionHtml::class)->name('v2_home');
    Route::get('/settings', ShowBookSettingsActionHtml::class)->name('v2_settings');
});

require __DIR__.'/auth.php';
