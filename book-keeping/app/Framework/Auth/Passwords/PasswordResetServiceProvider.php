<?php

namespace App\Framework\Auth\Passwords;

use Illuminate\Auth\Passwords\PasswordResetServiceProvider as  FrameworkPasswordResetServiceProvider;

class PasswordResetServiceProvider extends FrameworkPasswordResetServiceProvider
{
    /**
     * Register the password broker instance.
     *
     * @return void
     */
    protected function registerPasswordBroker()
    {
        $this->app->singleton('auth.password', function ($app) {
            return new PasswordBrokerManager($app);
        });

        $this->app->bind('auth.password.broker', function ($app) {
            return $app->make('auth.password')->broker();
        });
    }
}
