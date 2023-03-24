<?php

namespace Tests\Unit\DataProvider\PermissionRepositoryInterface;

use App\DataProvider\Eloquent\PermissionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchForAccessibleBooksTest extends TestCase
{
    use RefreshDatabase;

    protected $permission;

    public function setUp(): void
    {
        parent::setUp();
        $this->permission = new PermissionRepository();
    }

    public function test_it_takes_one_argument_and_returns_an_array(): void
    {
        $userId = 30;

        $bookList = $this->permission->searchForAccessibleBooks($userId);

        $this->assertTrue(is_array($bookList));
    }
}
