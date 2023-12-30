<?php

namespace Tests\Unit\Service\BookKeepingService;

use App\Service\AccountService;
use App\Service\BookKeepingService;
use App\Service\BookService;
use App\Service\BudgetService;
use App\Service\SlipService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrievePermittedUsersTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_retrieves_a_list_of_users_who_can_access_to_the_book(): void
    {
        $bookId = (string) Str::uuid();
        $userIdOfOwner = 25;
        $owner = new User();
        $owner->id = $userIdOfOwner;
        $this->be($owner);
        $bookInformationForOwner = [
            'book_id' => $bookId,
            'book_name' => 'BookName331',
            'modifiable' => true,
            'is_owner' => true,
            'is_default' => true,
            'created_at' => '2023-05-02 23:17:01',
        ];
        $permissionList = [
            ['user' => 'owner41', 'permitted_to' => 'ReadWrite'],
        ];
        $result_expected = [BookKeepingService::STATUS_NORMAL, $permissionList];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveBook')  // call from isOwner
            ->once()
            ->with($bookId, $userIdOfOwner)
            ->andReturn($bookInformationForOwner);
        $bookMock->shouldReceive('retrievePermissions')
            ->once()
            ->with($bookId)
            ->andReturn($permissionList);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->retrievePermittedUsers($bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_book_is_not_available(): void
    {
        $bookId = (string) Str::uuid();
        $userIdOfOwner = 67;
        $owner = new User();
        $owner->id = $userIdOfOwner;
        $this->be($owner);
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, []];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveBook')  // call from isOwner
            ->once()
            ->with($bookId, $userIdOfOwner)
            ->andReturn(null);
        $bookMock->shouldNotReceive('retrievePermissions');
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->retrievePermittedUsers($bookId);

        $this->assertSame($result_expected, $result_actual);
    }
}
