<?php

namespace App\Framework\Auth\Passwords;

use Illuminate\Auth\Passwords\PasswordBrokerManager as FrameworkPasswordBrokerManager;

/**
 * @mixin \Illuminate\Contracts\Auth\PasswordBroker
 */
class PasswordBrokerManager extends FrameworkPasswordBrokerManager
{
    /**
     * Create a token repository instance based on the given configuration.
     *
     * @param  array<string, mixed>  $config
     * @return \Illuminate\Auth\Passwords\TokenRepositoryInterface
     */
    protected function createTokenRepository(array $config)
    {
        $key = $this->app['config']['app.key'];

        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        $connection = $config['connection'] ?? null;

        return new DatabaseTokenRepository(
            $this->app['db']->connection($connection),
            $this->app['hash'],
            $config['table'],
            $key,
            $config['expire'],
            $config['throttle'] ?? 0
        );
    }
}
