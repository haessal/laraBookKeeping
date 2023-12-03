<?php

namespace Tests\Unit\Service\BookKeepingMigration;

use App\Service\AccountMigrationService;
use App\Service\BookKeepingMigration;
use App\Service\BookMigrationService;
use App\Service\SlipMigrationService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class DumpBooksTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_export_books_as_dump(): void
    {
        $userId = 25;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $bookId = (string) Str::uuid();
        $accounts = [
            [
                'account_group_id' => (string) Str::uuid(),
                'items' => [
                    ['account_id' => (string) Str::uuid()],
                ],
            ],
        ];
        $slips = [
            [
                'slip_id' => (string) Str::uuid(),
                'entries' => [
                    ['slip_entry_id' => (string) Str::uuid()],
                ],
            ],
        ];
        $books_expected = [
            [
                'book_id' => $bookId,
                'book' => [
                    'book_id' => $bookId,
                ],
                'accounts' => $accounts,
                'slips' => $slips,
            ],
        ];
        /** @var \App\Service\BookMigrationService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookMigrationService::class);
        $bookMock->shouldReceive('retrieveBooks')
            ->once()
            ->with($userId)
            ->andReturn([['book_id' => $bookId]]);
        $bookMock->shouldReceive('exportInformation')
            ->once()
            ->with($bookId)
            ->andReturn(['book_id' => $bookId]);
        /** @var \App\Service\AccountMigrationService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountMigrationService::class);
        $accountMock->shouldReceive('dumpAccounts')
            ->once()
            ->with($bookId)
            ->andReturn($accounts);
        /** @var \App\Service\SlipMigrationService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipMigrationService::class);
        $slipMock->shouldReceive('dumpSlips')
            ->once()
            ->with($bookId)
            ->andReturn($slips);

        $service = new BookKeepingMigration($bookMock, $accountMock, $slipMock);
        $books_actual = $service->dumpBooks();

        $this->assertSame($books_expected, $books_actual);
    }
}
