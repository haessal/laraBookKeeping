<?php

namespace App\Framework\Auth\Passwords;

use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Support\Str;

class ExPasswordBrokerManager extends PasswordBrokerManager
{
    /**
     * Create a token repository instance based on the given configuration.
     *
     * @param array $config
     *
     * @return \Illuminate\Auth\Passwords\TokenRepositoryInterface
     */
    protected function createTokenRepository(array $config)
    {
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        $connection = $config['connection'] ?? null;

        return new ExDatabaseTokenRepository(
            $this->app['db']->connection($connection),
            $this->app['hash'],
            $config['table'],
            $key,
            $config['index_name'],
            $config['expire']
        );
    }
}
