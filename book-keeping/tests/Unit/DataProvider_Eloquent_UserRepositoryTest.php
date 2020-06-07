<?php

namespace Tests\Unit;

use App\DataProvider\Eloquent\UserRepository;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

class DataProvider_Eloquent_UserRepositoryTest extends DataProvider_UserRepositoryInterfaceTest
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = new UserRepository();
    }

    public function tearDown(): void
    {
        Artisan::call('migrate:refresh');
        parent::tearDown();
    }

    /**
     * @test
     */
    public function findById_ReturnUser()
    {
        $name = 'userName';
        $email = 'a@b.com';
        $password = 'hash';
        $userId = factory(User::class)->create([
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
        ])->id;
        $user_expected = [
            'id'       => $userId,
            'name'     => $name,
            'email'    => $email,
        ];

        $user_actual = $this->user->findById($userId);

        $this->assertArraySubset($user_expected, $user_actual);
    }
}
