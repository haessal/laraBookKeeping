<?php

namespace Tests\Unit\Service\BookMigrationService;

use App\DataProvider\BookRepositoryInterface;
use App\DataProvider\PermissionRepositoryInterface;
use App\Service\BookMigrationService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class ExportInformationTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_export_book_information(): void
    {
        $bookId = (string) Str::uuid();
        $bookName = 'name23';
        $displayOrder = 24;
        $createdAt = '2023-12-10 10:01:01';
        $updatedAt = '2023-12-10 10:01:02';
        $bookInformation = [
            'book_id' => $bookId,
            'book_name' => $bookName,
            'display_order' => $displayOrder,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'deleted_at' => null,
        ];
        $bookInformation_expected = [
            'book_id' => $bookId,
            'book_name' => $bookName,
            'display_order' => $displayOrder,
            'updated_at' => $updatedAt,
            'deleted' => false,
        ];
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        $bookMock->shouldReceive('findByIdForExporting')
            ->once()
            ->with($bookId)
            ->andReturn($bookInformation);
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);

        $service = new BookMigrationService($bookMock, $permissionMock);
        $bookInformation_actual = $service->exportInformation($bookId);

        $this->assertSame($bookInformation_expected, $bookInformation_actual);
    }

    public function test_it_calls_repository_and_retruns_null_as_same_as_repository_do(): void
    {
        $bookId = (string) Str::uuid();
        $bookInformation_expected = null;
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        $bookMock->shouldReceive('findByIdForExporting')
            ->once()
            ->with($bookId)
            ->andReturn(null);
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);

        $service = new BookMigrationService($bookMock, $permissionMock);
        $bookInformation_actual = $service->exportInformation($bookId);

        $this->assertSame($bookInformation_expected, $bookInformation_actual);
    }
}
