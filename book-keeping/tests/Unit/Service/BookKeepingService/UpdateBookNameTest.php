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

class UpdateBookNameTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_updates_the_name_of_the_book(): void
    {
        $bookId = (string) Str::uuid();
        $userIdOfOwner = 25;
        $owner = new User();
        $owner->id = $userIdOfOwner;
        $this->be($owner);
        $bookInformationForOwner = [
            'book_id'    => $bookId,
            'book_name'  => 'BookName31',
            'modifiable' => true,
            'is_owner'   => true,
            'is_default' => true,
            'created_at' => '2023-05-03 07:5101',
        ];
        $newName = 'newBookName37';
        $result_expected = [BookKeepingService::STATUS_NORMAL, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveBook')  // call from isOwner
            ->once()
            ->with($bookId, $userIdOfOwner)
            ->andReturn($bookInformationForOwner);
        $bookMock->shouldReceive('updateNameOf')
            ->once()
            ->with($bookId, $newName);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->updateBookName($bookId, $newName);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_book_is_not_available(): void
    {
        $bookId = (string) Str::uuid();
        $userIdOfOwner = 64;
        $owner = new User();
        $owner->id = $userIdOfOwner;
        $this->be($owner);
        $newName = 'newBookName68';
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveBook')  // call from isOwner
            ->once()
            ->with($bookId, $userIdOfOwner)
            ->andReturn(null);
        $bookMock->shouldNotReceive('updateNameOf');
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->updateBookName($bookId, $newName);

        $this->assertSame($result_expected, $result_actual);
    }
}
