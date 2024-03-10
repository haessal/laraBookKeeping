<?php

namespace Tests\Unit\Service\BookKeepingMigrationValidator;

use App\Service\BookKeepingMigrationValidator;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class ValidateSlipTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @dataProvider forTestValidateSlip
     */
    public function test_it_validates_the_format_of_the_slip($slip, $slip_expected): void
    {
        $service = new BookKeepingMigrationValidator();
        $slip_actual = $service->validateSlip($slip);

        $this->assertSame($slip_expected, $slip_actual);
    }

    public static function forTestValidateSlip()
    {
        $slipId = (string) Str::uuid();
        $bookId = (string) Str::uuid();
        $slipOutline = 'outline32';
        $slipMemo = 'memo33';
        $slipDate = '2023-02-01';
        $displayOrder = 2;
        $updatedAt = '2023-02-25T16:03:02+09:00';

        return [
            [
                [
                    'slip_id' => $slipId,
                    'book_id' => $bookId,
                    'slip_outline' => $slipOutline,
                    'slip_memo' => $slipMemo,
                    'date' => $slipDate,
                    'is_draft' => 0,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                [
                    'slip_id' => $slipId,
                    'book_id' => $bookId,
                    'slip_outline' => $slipOutline,
                    'slip_memo' => $slipMemo,
                    'date' => $slipDate,
                    'is_draft' => false,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
            ],
            [
                [
                    'slip_id' => $slipId,
                    'book_id' => $bookId,
                    'slip_outline' => $slipOutline,
                    'slip_memo' => null,
                    'date' => $slipDate,
                    'is_draft' => 0,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
                [
                    'slip_id' => $slipId,
                    'book_id' => $bookId,
                    'slip_outline' => $slipOutline,
                    'slip_memo' => null,
                    'date' => $slipDate,
                    'is_draft' => false,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
            ],
            [
                [
                    // 'slip_id' => $slipId, key missing
                    'book_id' => $bookId,
                    'slip_outline' => $slipOutline,
                    'slip_memo' => $slipMemo,
                    'date' => $slipDate,
                    'is_draft' => 0,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'slip_id' => 123, // invalid uuid (not string)
                    'book_id' => $bookId,
                    'slip_outline' => $slipOutline,
                    'slip_memo' => $slipMemo,
                    'date' => $slipDate,
                    'is_draft' => 0,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'slip_id' => $slipId,
                    'book_id' => 'aaaa', // invalid uuid
                    'slip_outline' => $slipOutline,
                    'slip_memo' => $slipMemo,
                    'date' => $slipDate,
                    'is_draft' => 0,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'slip_id' => $slipId,
                    'book_id' => $bookId,
                    'slip_outline' => 132, // invalid type (not string)
                    'slip_memo' => $slipMemo,
                    'date' => $slipDate,
                    'is_draft' => 0,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'slip_id' => $slipId,
                    'book_id' => $bookId,
                    'slip_outline' => $slipOutline,
                    'slip_memo' => 147, // invalid type (not string)
                    'date' => $slipDate,
                    'is_draft' => 0,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'slip_id' => $slipId,
                    'book_id' => $bookId,
                    'slip_outline' => $slipOutline,
                    'slip_memo' => $slipMemo,
                    'date' => 162, // invalid (not string)
                    'is_draft' => 0,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'slip_id' => $slipId,
                    'book_id' => $bookId,
                    'slip_outline' => $slipOutline,
                    'slip_memo' => $slipMemo,
                    'date' => null, // invalid (null)
                    'is_draft' => 0,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'slip_id' => $slipId,
                    'book_id' => $bookId,
                    'slip_outline' => $slipOutline,
                    'slip_memo' => $slipMemo,
                    'date' => '2023-03-32', // invalid format
                    'is_draft' => 0,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'slip_id' => $slipId,
                    'book_id' => $bookId,
                    'slip_outline' => $slipOutline,
                    'slip_memo' => $slipMemo,
                    'date' => $slipDate,
                    'is_draft' => false, // invalid (not int)
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'slip_id' => $slipId,
                    'book_id' => $bookId,
                    'slip_outline' => $slipOutline,
                    'slip_memo' => $slipMemo,
                    'date' => $slipDate,
                    'is_draft' => 0,
                    'display_order' => '3', // invalid (not int)
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'slip_id' => $slipId,
                    'book_id' => $bookId,
                    'slip_outline' => $slipOutline,
                    'slip_memo' => $slipMemo,
                    'date' => $slipDate,
                    'is_draft' => 0,
                    'display_order' => $displayOrder,
                    'updated_at' => '2023-03-03 20:03:02', // invalid format
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'slip_id' => $slipId,
                    'book_id' => $bookId,
                    'slip_outline' => $slipOutline,
                    'slip_memo' => $slipMemo,
                    'date' => $slipDate,
                    'is_draft' => 0,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => 0, // invalid (not bool)
                ],
                null,
            ],
        ];
    }
}
