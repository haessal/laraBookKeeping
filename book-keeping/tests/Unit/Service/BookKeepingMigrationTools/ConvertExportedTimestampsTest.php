<?php

namespace Tests\Unit\Service\BookKeepingMigrationTools;

use App\Service\BookKeepingMigrationTools;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class ConvertExportedTimestampsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_convert_the_format_of_the_array(): void
    {
        $bookId = (string) Str::uuid();
        $bookName = 'name19';
        $displayOrder = 20;
        $createdAt = '2023-12-09 21:01:01';
        $updatedAt = '2023-12-09 21:01:02';
        $deletedAt = '2023-12-09 21:01:03';
        $book = [
            'book_id' => $bookId,
            'book_name' => $bookName,
            'display_order' => $displayOrder,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'deleted_at' => $deletedAt,
        ];
        $converted_expected = [
            'book_id' => $bookId,
            'book_name' => $bookName,
            'display_order' => $displayOrder,
            'updated_at' => $updatedAt,
            'deleted' => true,
        ];

        $service = new BookKeepingMigrationTools();
        $converted_actual = $service->convertExportedTimestamps($book);

        $this->assertSame($converted_expected, $converted_actual);
    }
}
