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

class AuthorizeToAccessTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_authorizes_the_user_to_access_the_book(): void
    {
        $bookId = (string) Str::uuid();
        $userIdOfOwner = 23;
        $owner = new User();
        $owner->id = $userIdOfOwner;
        $this->be($owner);
        $bookInformationForOwner = [
            'book_id' => $bookId,
            'book_name' => 'BookName31',
            'modifiable' => true,
            'is_owner' => true,
            'is_default' => true,
            'created_at' => '2023-05-02 10:29:01',
        ];
        $userName = 'user25';
        $mode = 'ReadWrite';
        $bookPermission = ['user' => $userName, 'permitted_to' => $mode];
        $result_expected = [BookKeepingService::STATUS_NORMAL, $bookPermission];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveBook')  // call from isOwner
            ->once()
            ->with($bookId, $userIdOfOwner)
            ->andReturn($bookInformationForOwner);
        $bookMock->shouldReceive('retrievePermissions')
            ->once()
            ->with($bookId)
            ->andReturn([]);
        $bookMock->shouldReceive('createPermission')
            ->once()
            ->with($bookId, $userName, $mode)
            ->andReturn($bookPermission);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->authorizeToAccess($bookId, $userName, $mode);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_book_is_not_available(): void
    {
        $bookId = (string) Str::uuid();
        $userIdOfOwner = 71;
        $owner = new User();
        $owner->id = $userIdOfOwner;
        $this->be($owner);
        $userName = 'user75';
        $mode = 'ReadWrite';
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveBook')  // call from isOwner
            ->once()
            ->with($bookId, $userIdOfOwner)
            ->andReturn(null);
        $bookMock->shouldNotReceive('retrievePermissions');
        $bookMock->shouldReceive('createPermission');
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->authorizeToAccess($bookId, $userName, $mode);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_user_is_already_authorized_with_expected_permission(): void
    {
        $bookId = (string) Str::uuid();
        $userIdOfOwner = 102;
        $owner = new User();
        $owner->id = $userIdOfOwner;
        $this->be($owner);
        $bookInformationForOwner = [
            'book_id' => $bookId,
            'book_name' => 'BookName108',
            'modifiable' => true,
            'is_owner' => true,
            'is_default' => true,
            'created_at' => '2023-05-02 10:57:01',
        ];
        $userName = 'user114';
        $existing_permissions = [
            ['user' => $userName, 'permitted_to' => 'ReadOnly'],
        ];
        $mode = 'ReadOnly';
        $result_expected = [BookKeepingService::STATUS_NORMAL, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveBook')  // call from isOwner
            ->once()
            ->with($bookId, $userIdOfOwner)
            ->andReturn($bookInformationForOwner);
        $bookMock->shouldReceive('retrievePermissions')
            ->once()
            ->with($bookId)
            ->andReturn($existing_permissions);
        $bookMock->shouldNotReceive('createPermission');
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->authorizeToAccess($bookId, $userName, $mode);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_user_is_already_authorized_with_unexpected_permission(): void
    {
        $bookId = (string) Str::uuid();
        $userIdOfOwner = 147;
        $owner = new User();
        $owner->id = $userIdOfOwner;
        $this->be($owner);
        $bookInformationForOwner = [
            'book_id' => $bookId,
            'book_name' => 'BookName153',
            'modifiable' => true,
            'is_owner' => true,
            'is_default' => true,
            'created_at' => '2023-06-29 10:57:01',
        ];
        $userName = 'user114';
        $existing_permissions = [
            ['user' => $userName, 'permitted_to' => 'ReadOnly'],
        ];
        $mode = 'ReadWrite';
        $result_expected = [BookKeepingService::STATUS_ERROR_BAD_CONDITION, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveBook')  // call from isOwner
            ->once()
            ->with($bookId, $userIdOfOwner)
            ->andReturn($bookInformationForOwner);
        $bookMock->shouldReceive('retrievePermissions')
            ->once()
            ->with($bookId)
            ->andReturn($existing_permissions);
        $bookMock->shouldNotReceive('createPermission');
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->authorizeToAccess($bookId, $userName, $mode);

        $this->assertSame($result_expected, $result_actual);
    }
}
