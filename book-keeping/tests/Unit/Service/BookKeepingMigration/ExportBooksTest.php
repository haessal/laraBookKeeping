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

class ExportBooksTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_export_books(): void
    {
        $userId = 23;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $bookId = (string) Str::uuid();
        $bookUpdatedAt = '2023-12-09 21:01:00';
        $books_expected = [
            [
                'book_id' => $bookId,
                'book' => [
                    'book_id' => $bookId,
                    'updated_at' => $bookUpdatedAt,
                ],
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
            ->andReturn(['book_id' => $bookId, 'updated_at' => $bookUpdatedAt]);
        /** @var \App\Service\AccountMigrationService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountMigrationService::class);
        /** @var \App\Service\SlipMigrationService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipMigrationService::class);

        $service = new BookKeepingMigration($bookMock, $accountMock, $slipMock);
        $books_actual = $service->exportBooks();

        $this->assertSame($books_expected, $books_actual);
    }
}
