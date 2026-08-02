<?php

namespace Tests\Unit\Service\BookKeepingMigrationValidator;

use App\Service\BookKeepingMigrationValidator;
use Illuminate\Support\Str;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ValidateCreditCardStatementTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[DataProvider('forTestValidateCreditCardStatement')]
    public function test_it_validates_the_format_of_the_credit_card_statement($creditCardStatement, $creditCardStatement_expected): void
    {
        $service = new BookKeepingMigrationValidator();
        $creditCardStatement_actual = $service->validateCreditCardStatement($creditCardStatement);

        $this->assertSame($creditCardStatement_expected, $creditCardStatement_actual);
    }

    public static function forTestValidateCreditCardStatement()
    {
        $creditCardStatementId = (string) Str::uuid();
        $bookId = (string) Str::uuid();
        $outline = 'outline42';
        $memo = 'memo43';
        $date = '2024-07-04';
        $displayOrder = 2;
        $updatedAt = '2024-07-06T19:50:02+09:00';

        return [
            [
                [
                    'credit_card_statement_id' => $creditCardStatementId,
                    'book_id' => $bookId,
                    'credit_card_statement_outline' => $outline,
                    'credit_card_statement_memo' => $memo,
                    'date' => $date,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                [
                    'credit_card_statement_id' => $creditCardStatementId,
                    'book_id' => $bookId,
                    'credit_card_statement_outline' => $outline,
                    'credit_card_statement_memo' => $memo,
                    'date' => $date,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
            ],
            [
                [
                    'credit_card_statement_id' => $creditCardStatementId,
                    'book_id' => $bookId,
                    'credit_card_statement_outline' => $outline,
                    'credit_card_statement_memo' => null,
                    'date' => $date,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
                [
                    'credit_card_statement_id' => $creditCardStatementId,
                    'book_id' => $bookId,
                    'credit_card_statement_outline' => $outline,
                    'credit_card_statement_memo' => null,
                    'date' => $date,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
            ],
            [
                [
                    // 'credit_card_statement_id' => $creditCardStatementId, key missing
                    'book_id' => $bookId,
                    'credit_card_statement_outline' => $outline,
                    'credit_card_statement_memo' => null,
                    'date' => $date,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'credit_card_statement_id' => 123, // invalid uuid (not string)
                    'book_id' => $bookId,
                    'credit_card_statement_outline' => $outline,
                    'credit_card_statement_memo' => null,
                    'date' => $date,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'credit_card_statement_id' => 'aaaa', // invalid uuid
                    'book_id' => $bookId,
                    'credit_card_statement_outline' => $outline,
                    'credit_card_statement_memo' => null,
                    'date' => $date,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'credit_card_statement_id' => $creditCardStatementId,
                    'book_id' => 'aaaa', // invalid uuid
                    'credit_card_statement_outline' => $outline,
                    'credit_card_statement_memo' => null,
                    'date' => $date,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'credit_card_statement_id' => $creditCardStatementId,
                    'book_id' => $bookId,
                    'credit_card_statement_outline' => 123, // invalid (not string)
                    'credit_card_statement_memo' => null,
                    'date' => $date,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'credit_card_statement_id' => $creditCardStatementId,
                    'book_id' => $bookId,
                    'credit_card_statement_outline' => $outline,
                    'credit_card_statement_memo' => 123, // invalid (not string)
                    'date' => $date,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'credit_card_statement_id' => $creditCardStatementId,
                    'book_id' => $bookId,
                    'credit_card_statement_outline' => $outline,
                    'credit_card_statement_memo' => $memo,
                    'date' => '2024-06-00', // invalid (not date)
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'credit_card_statement_id' => $creditCardStatementId,
                    'book_id' => $bookId,
                    'credit_card_statement_outline' => $outline,
                    'credit_card_statement_memo' => $memo,
                    'date' => $date,
                    'display_order' => '3', // invalid (not int)
                    'updated_at' => null,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'credit_card_statement_id' => $creditCardStatementId,
                    'book_id' => $bookId,
                    'credit_card_statement_outline' => $outline,
                    'credit_card_statement_memo' => $memo,
                    'date' => $date,
                    'display_order' => null,
                    'updated_at' => '2023-03-03 19:08:02', // invalid format
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'credit_card_statement_id' => $creditCardStatementId,
                    'book_id' => $bookId,
                    'credit_card_statement_outline' => $outline,
                    'credit_card_statement_memo' => null,
                    'date' => $date,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => 0, // invalid (not bool)
                ],
                null,
            ],
        ];
    }
}
