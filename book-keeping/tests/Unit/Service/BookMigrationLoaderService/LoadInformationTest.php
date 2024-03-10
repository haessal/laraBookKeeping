<?php

namespace Tests\Unit\Service\BookMigrationLoaderService;

use App\DataProvider\BookRepositoryInterface;
use App\DataProvider\PermissionRepositoryInterface;
use App\Service\BookKeepingMigrationTools;
use App\Service\BookKeepingMigrationValidator;
use App\Service\BookMigrationLoaderService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class LoadInformationTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_create_the_book_information(): void
    {
        $userId = 24;
        $bookId = (string) Str::uuid();
        $bookInformation = [
            'book_id' => $bookId,
            'book_name' => 'bookName31',
        ];
        $result_expected = [
            ['bookId' => $bookId, 'result' => 'created'],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldNotReceive('isSourceLater');
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateBookInformation')
            ->once()
            ->with($bookInformation)
            ->andReturn($bookInformation);
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        $bookMock->shouldReceive('findByIdForExporting')
            ->once()
            ->with($bookId)
            ->andReturn(null);
        $bookMock->shouldNotReceive('updateForImporting');
        $bookMock->shouldReceive('createForImporting')
            ->once()
            ->with($bookInformation);
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldReceive('create')
            ->once()
            ->with($userId, $bookId, true, true, false);

        $service = new BookMigrationLoaderService($bookMock, $permissionMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadInformation($userId, $bookInformation);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_calls_repository_to_update_the_book_information(): void
    {
        $userId = 67;
        $bookId = (string) Str::uuid();
        $bookInformationUpdateAt = '2024-02-17T23:37:15+09:00';
        $destinationUpdateAt = '2024-02-17T22:37:15+09:00';
        $bookInformation = [
            'book_id' => $bookId,
            'book_name' => 'bookName77',
            'updated_at' => $bookInformationUpdateAt,
        ];
        $destinationBookInformation = [
            'book_id' => $bookId,
            'updated_at' => $destinationUpdateAt,
        ];
        $result_expected = [
            ['bookId' => $bookId, 'result' => 'updated'],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldReceive('isSourceLater')
            ->once()
            ->with($bookInformationUpdateAt, $destinationUpdateAt)
            ->andReturn(true);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateBookInformation')
            ->once()
            ->with($bookInformation)
            ->andReturn($bookInformation);
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        $bookMock->shouldReceive('findByIdForExporting')
            ->once()
            ->with($bookId)
            ->andReturn($destinationBookInformation);
        $bookMock->shouldReceive('updateForImporting')
            ->once()
            ->with($bookInformation);
        $bookMock->shouldNotReceive('createForImporting');
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldNotReceive('create');

        $service = new BookMigrationLoaderService($bookMock, $permissionMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadInformation($userId, $bookInformation);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_book_information_is_already_up_to_date(): void
    {
        $userId = 118;
        $bookId = (string) Str::uuid();
        $bookInformationUpdateAt = '2024-02-23T12:37:15+09:00';
        $destinationUpdateAt = '2024-02-23T13:37:15+09:00';
        $bookInformation = [
            'book_id' => $bookId,
            'book_name' => 'bookName133',
            'updated_at' => $bookInformationUpdateAt,
        ];
        $destinationBookInformation = [
            'book_id' => $bookId,
            'updated_at' => $destinationUpdateAt,
        ];
        $result_expected = [
            ['bookId' => $bookId, 'result' => 'already up-to-date'],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldReceive('isSourceLater')
            ->once()
            ->with($bookInformationUpdateAt, $destinationUpdateAt)
            ->andReturn(false);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateBookInformation')
            ->once()
            ->with($bookInformation)
            ->andReturn($bookInformation);
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        $bookMock->shouldReceive('findByIdForExporting')
            ->once()
            ->with($bookId)
            ->andReturn($destinationBookInformation);
        $bookMock->shouldNotReceive('updateForImporting');
        $bookMock->shouldNotReceive('createForImporting');
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldNotReceive('create');

        $service = new BookMigrationLoaderService($bookMock, $permissionMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadInformation($userId, $bookInformation);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_book_information_is_not_valid(): void
    {
        $userId = 167;
        $bookId = (string) Str::uuid();
        $bookInformationUpdateAt = '2024-02-17T23:37:15+09:00';
        $bookInformation = [
            'book_id' => $bookId,
            'book_name' => 'bookName77',
            'updated_at' => $bookInformationUpdateAt,
        ];
        $result_expected = [
            ['bookId' => null, 'result' => null],
            'invalid data format: book',
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldNotReceive('isSourceLater');
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateBookInformation')
            ->once()
            ->with($bookInformation)
            ->andReturn(null);
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        $bookMock->shouldNotReceive('findByIdForExporting');
        $bookMock->shouldNotReceive('updateForImporting');
        $bookMock->shouldNotReceive('createForImporting');
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldNotReceive('create');

        $service = new BookMigrationLoaderService($bookMock, $permissionMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadInformation($userId, $bookInformation);

        $this->assertSame($result_expected, $result_actual);
    }
}
