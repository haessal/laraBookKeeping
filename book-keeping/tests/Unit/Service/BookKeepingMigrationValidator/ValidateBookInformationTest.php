<?php

namespace Tests\Unit\Service\BookKeepingMigrationValidator;

use App\Service\BookKeepingMigrationValidator;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class ValidateBookInformationTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @dataProvider forTestValidateBookInformation
     */
    public function test_it_validates_the_format_of_the_book_information($bookInformation, $bookInformation_expected): void
    {
        $service = new BookKeepingMigrationValidator();
        $bookInformation_actual = $service->validateBookInformation($bookInformation);

        $this->assertSame($bookInformation_expected, $bookInformation_actual);
    }

    public static function forTestValidateBookInformation()
    {
        $bookId = (string) Str::uuid();
        $bookName = 'bookname31';
        $displayOrder = 2;
        $updatedAt = '2023-03-03T19:03:02+09:00';

        return [
            [
                [
                    'book_id' => $bookId,
                    'book_name' => $bookName,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                [
                    'book_id' => $bookId,
                    'book_name' => $bookName,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
            ],
            [
                [
                    'book_id' => $bookId,
                    'book_name' => $bookName,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
                [
                    'book_id' => $bookId,
                    'book_name' => $bookName,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
            ],
            [
                [
                    // 'book_id' => $bookId, key missing
                    'book_name' => $bookName,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'book_id' => 123, // invalid uuid (not string)
                    'book_name' => $bookName,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'book_id' => 'aaaa', // invalid uuid
                    'book_name' => $bookName,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'book_id' => $bookId,
                    'book_name' => 0, // invalid type (not string)
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'book_id' => $bookId,
                    'book_name' => $bookName,
                    'display_order' => '3', // invalid (not int)
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'book_id' => $bookId,
                    'book_name' => $bookName,
                    'display_order' => $displayOrder,
                    'updated_at' => '2023-03-03 19:08:02', // invalid format
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'book_id' => $bookId,
                    'book_name' => $bookName,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => 0, // invalid (not bool)
                ],
                null,
            ],
        ];
    }
}
