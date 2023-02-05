<?php

namespace App\DataProvider;

interface UserRepositoryInterface
{
    /**
     * Find user.
     *
     * @param  int  $userId
     * @return array | null
     */
    public function findById(int $userId): ?array;
}
