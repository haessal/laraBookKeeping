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

class SetBookAsDefaultTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_sets_the_book_as_the_default_one(): void
    {
        $bookId = (string) Str::uuid();
        $bookName = 'BookName225';
        $userIdOfOwner = 26;
        $owner = new User();
        $owner->id = $userIdOfOwner;
        $this->be($owner);
        $bookInformationForOwner = [
            'book_id'    => $bookId,
            'book_name'  => $bookName,
            'modifiable' => true,
            'is_owner'   => true,
            'is_default' => false,
            'created_at' => '2023-05-02 23:33:01',
        ];
        $bookInformationForOwnerAfterUpdated = [
            'book_id'    => $bookId,
            'book_name'  => $bookName,
            'modifiable' => true,
            'is_owner'   => true,
            'is_default' => true,
            'created_at' => '2023-05-02 23:33:01',
        ];
        $userNameOfOwner = 'owner38';
        $book = [
            'id'         => $bookId,
            'name'       => $bookName,
            'is_default' => true,
            'is_owner'   => true,
            'modifiable' => true,
            'owner'      => $userNameOfOwner,
        ];
        $result_expected = [BookKeepingService::STATUS_NORMAL, $book];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveBook')  // call from isOwner
            ->once()
            ->with($bookId, $userIdOfOwner)
            ->andReturn($bookInformationForOwner);
        $bookMock->shouldReceive('retrieveDefaultBook')
            ->once()
            ->with($userIdOfOwner)
            ->andReturn(null);
        $bookMock->shouldReceive('updateDefaultMarkOf')
            ->once()
            ->with($bookId, $userIdOfOwner, true);
        $bookMock->shouldReceive('retrieveBook')  // call from $this->retrieveBook
            ->once()
            ->with($bookId, $userIdOfOwner)
            ->andReturn($bookInformationForOwnerAfterUpdated);
        $bookMock->shouldReceive('retrieveOwnerNameOf')  // call from $this->retrieveBook
            ->once()
            ->with($bookId)
            ->andReturn($userNameOfOwner);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->setBookAsDefault($bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_book_is_not_available(): void
    {
        $bookId = (string) Str::uuid();
        $userIdOfOwner = 93;
        $owner = new User();
        $owner->id = $userIdOfOwner;
        $this->be($owner);
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveBook')  // call from isOwner
            ->once()
            ->with($bookId, $userIdOfOwner)
            ->andReturn(null);
        $bookMock->shouldNotReceive('retrieveDefaultBook');
        $bookMock->shouldNotReceive('updateDefaultMarkOf');
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->setBookAsDefault($bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_default_book_is_already_set(): void
    {
        $bookId = (string) Str::uuid();
        $defaultBookId = (string) Str::uuid();
        $userIdOfOwner = 26;
        $owner = new User();
        $owner->id = $userIdOfOwner;
        $this->be($owner);
        $bookInformationForOwner = [
            'book_id'    => $bookId,
            'book_name'  => 'BookName129',
            'modifiable' => true,
            'is_owner'   => true,
            'is_default' => false,
            'created_at' => '2023-05-02 23:33:01',
        ];
        $result_expected = [BookKeepingService::STATUS_NORMAL, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveBook')  // call from isOwner
            ->once()
            ->with($bookId, $userIdOfOwner)
            ->andReturn($bookInformationForOwner);
        $bookMock->shouldReceive('retrieveDefaultBook')
            ->once()
            ->with($userIdOfOwner)
            ->andReturn($defaultBookId);
        $bookMock->shouldNotReceive('updateDefaultMarkOf');
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->setBookAsDefault($bookId);

        $this->assertSame($result_expected, $result_actual);
    }
}
