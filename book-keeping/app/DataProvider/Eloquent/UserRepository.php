<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\UserRepositoryInterface;
use App\User;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Find user.
     *
     * @param  int  $userId
     * @return array | null
     */
    public function findById(int $userId): ?array
    {
        $user = User::find($userId);

        return is_null($user) ? null : $user->toArray();
    }
}
