<?php

namespace App\Providers;

use App\Repositories\AccountGroupRepositoryInterface;
use App\Repositories\AccountRepositoryInterface;
use App\Repositories\BookRepositoryInterface;
use App\Repositories\BudgetRepositoryInterface;
use App\Repositories\Eloquent\AccountGroupRepository;
use App\Repositories\Eloquent\AccountRepository;
use App\Repositories\Eloquent\BookRepository;
use App\Repositories\Eloquent\BudgetRepository;
use App\Repositories\Eloquent\PermissionRepository;
use App\Repositories\Eloquent\SlipEntryRepository;
use App\Repositories\Eloquent\SlipRepository;
use App\Repositories\PermissionRepositoryInterface;
use App\Repositories\SlipEntryRepositoryInterface;
use App\Repositories\SlipRepositoryInterface;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            AccountGroupRepositoryInterface::class,
            AccountGroupRepository::class
        );
        $this->app->bind(
            AccountRepositoryInterface::class,
            AccountRepository::class
        );
        $this->app->bind(
            BookRepositoryInterface::class,
            BookRepository::class
        );
        $this->app->bind(
            BudgetRepositoryInterface::class,
            BudgetRepository::class
        );
        $this->app->bind(
            PermissionRepositoryInterface::class,
            PermissionRepository::class
        );
        $this->app->bind(
            SlipEntryRepositoryInterface::class,
            SlipEntryRepository::class
        );
        $this->app->bind(
            SlipRepositoryInterface::class,
            SlipRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
