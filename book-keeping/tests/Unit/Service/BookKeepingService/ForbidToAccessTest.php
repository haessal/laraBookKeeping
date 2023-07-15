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

class ForbidToAccessTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_forbids_the_user_to_access_the_book(): void
    {
        $bookId = (string) Str::uuid();
        $userIdOfOwner = 25;
        $userNameOfOwner = 'owner26';
        $owner = new User();
        $owner->id = $userIdOfOwner;
        $this->be($owner);
        $bookInformationForOwner = [
            'book_id'    => $bookId,
            'book_name'  => 'BookName32',
            'modifiable' => true,
            'is_owner'   => true,
            'is_default' => true,
            'created_at' => '2023-05-02 14:52:01',
        ];
        $userName = 'user30';
        $bookPermission = ['user' => $userName, 'permitted_to' => 'ReadOnly'];
        $existing_permissions = [$bookPermission];
        $result_expected = [BookKeepingService::STATUS_NORMAL, $bookPermission];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveBook')  // call from isOwner
            ->once()
            ->with($bookId, $userIdOfOwner)
            ->andReturn($bookInformationForOwner);
        $bookMock->shouldReceive('retrieveOwnerNameOf')
            ->once()
            ->with($bookId)
            ->andReturn($userNameOfOwner);
        $bookMock->shouldReceive('retrievePermissions')
            ->once()
            ->with($bookId)
            ->andReturn($existing_permissions);
        $bookMock->shouldReceive('deletePermission')
            ->once()
            ->with($bookId, $userName);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->forbidToAccess($bookId, $userName);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_authenticated_user_is_not_owner_of_the_book(): void
    {
        $bookId = (string) Str::uuid();
        $userIdOfOwner = 75;
        $owner = new User();
        $owner->id = $userIdOfOwner;
        $this->be($owner);
        $bookInformationForOwner = [
            'book_id'    => $bookId,
            'book_name'  => 'BookName82',
            'modifiable' => true,
            'is_owner'   => false,
            'is_default' => false,
            'created_at' => '2023-05-02 15:52:01',
        ];
        $userName = 'user30';
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_FORBIDDEN, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveBook')  // call from isOwner
            ->once()
            ->with($bookId, $userIdOfOwner)
            ->andReturn($bookInformationForOwner);
        $bookMock->shouldNotReceive('retrieveOwnerNameOf');
        $bookMock->shouldNotReceive('retrievePermissions');
        $bookMock->shouldNotReceive('deletePermission');
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->forbidToAccess($bookId, $userName);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_user_is_the_owner_of_the_book(): void
    {
        $bookId = (string) Str::uuid();
        $userIdOfOwner = 114;
        $userNameOfOwner = 'owner115';
        $owner = new User();
        $owner->id = $userIdOfOwner;
        $this->be($owner);
        $bookInformationForOwner = [
            'book_id'    => $bookId,
            'book_name'  => 'BookName121',
            'modifiable' => true,
            'is_owner'   => true,
            'is_default' => true,
            'created_at' => '2023-05-02 15:10:01',
        ];
        $result_expected = [BookKeepingService::STATUS_ERROR_BAD_CONDITION, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveBook')  // call from isOwner
            ->once()
            ->with($bookId, $userIdOfOwner)
            ->andReturn($bookInformationForOwner);
        $bookMock->shouldReceive('retrieveOwnerNameOf')
            ->once()
            ->with($bookId)
            ->andReturn($userNameOfOwner);
        $bookMock->shouldNotReceive('retrievePermissions');
        $bookMock->shouldNotReceive('deletePermission');
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->forbidToAccess($bookId, $userNameOfOwner);

        $this->assertSame($result_expected, $result_actual);
    }
}
