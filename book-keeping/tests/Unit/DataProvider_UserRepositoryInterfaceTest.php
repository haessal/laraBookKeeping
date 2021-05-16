<?php

namespace Tests\Unit;

use Tests\TestCase;

abstract class DataProvider_UserRepositoryInterfaceTest extends TestCase
{
    /**
     * @test
     */
    public function findById_ReturnValueTypeIsArrayOrNull()
    {
        $userId = 15;

        $user = $this->user->findById($userId);

        if (is_null($user)) {
            $this->assertTrue(is_null($user));
        } else {
            $this->assertTrue(is_array($user));
        }
    }
}
