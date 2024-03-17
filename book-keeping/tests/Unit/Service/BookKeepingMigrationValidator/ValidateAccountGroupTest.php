<?php

namespace Tests\Unit\Service\BookKeepingMigrationValidator;

use App\Service\BookKeepingMigrationValidator;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class ValidateAccountGroupTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @dataProvider forTestValidateAccountGroup
     */
    public function test_it_validates_the_format_of_the_account_group($accountGroup, $accountGroup_expected): void
    {
        $service = new BookKeepingMigrationValidator();
        $accountGroup_actual = $service->validateAccountGroup($accountGroup);

        $this->assertSame($accountGroup_expected, $accountGroup_actual);
    }

    public static function forTestValidateAccountGroup()
    {
        $accountGroupId = (string) Str::uuid();
        $bookId = (string) Str::uuid();
        $accountType = 'asset';
        $accountGroupTitle = 'accountGroup36';
        $bk_uid = 34;
        $bk_code = 35;
        $displayOrder = 2;
        $updatedAt = '2023-02-25T16:03:02+09:00';

        return [
            [
                [
                    'account_group_id' => $accountGroupId,
                    'book_id' => $bookId,
                    'account_type' => $accountType,
                    'account_group_title' => $accountGroupTitle,
                    'bk_uid' => $bk_uid,
                    'account_group_bk_code' => $bk_code,
                    'is_current' => 0,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                [
                    'account_group_id' => $accountGroupId,
                    'book_id' => $bookId,
                    'account_type' => $accountType,
                    'account_group_title' => $accountGroupTitle,
                    'bk_uid' => $bk_uid,
                    'account_group_bk_code' => $bk_code,
                    'is_current' => false,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
            ],
            [
                [
                    'account_group_id' => $accountGroupId,
                    'book_id' => $bookId,
                    'account_type' => $accountType,
                    'account_group_title' => $accountGroupTitle,
                    'bk_uid' => null,
                    'account_group_bk_code' => null,
                    'is_current' => 1,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
                [
                    'account_group_id' => $accountGroupId,
                    'book_id' => $bookId,
                    'account_type' => $accountType,
                    'account_group_title' => $accountGroupTitle,
                    'bk_uid' => null,
                    'account_group_bk_code' => null,
                    'is_current' => true,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
            ],
            [
                [
                    // 'account_group_id' => $accountGroupId, key missing
                    'book_id' => $bookId,
                    'account_type' => $accountType,
                    'account_group_title' => $accountGroupTitle,
                    'bk_uid' => $bk_uid,
                    'account_group_bk_code' => $bk_code,
                    'is_current' => 0,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'account_group_id' => 123, // invalid uuid (not string)
                    'book_id' => $bookId,
                    'account_type' => $accountType,
                    'account_group_title' => $accountGroupTitle,
                    'bk_uid' => $bk_uid,
                    'account_group_bk_code' => $bk_code,
                    'is_current' => 0,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'account_group_id' => $accountGroupId,
                    'book_id' => 'aaaa', // invalid uuid
                    'account_type' => $accountType,
                    'account_group_title' => $accountGroupTitle,
                    'bk_uid' => $bk_uid,
                    'account_group_bk_code' => $bk_code,
                    'is_current' => 0,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'account_group_id' => $accountGroupId,
                    'book_id' => $bookId,
                    'account_type' => 0, // invalid type (not string)
                    'account_group_title' => $accountGroupTitle,
                    'bk_uid' => $bk_uid,
                    'account_group_bk_code' => $bk_code,
                    'is_current' => 0,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'account_group_id' => $accountGroupId,
                    'book_id' => $bookId,
                    'account_type' => 'invalid-account-type', // invalid type
                    'account_group_title' => $accountGroupTitle,
                    'bk_uid' => $bk_uid,
                    'account_group_bk_code' => $bk_code,
                    'is_current' => 0,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'account_group_id' => $accountGroupId,
                    'book_id' => $bookId,
                    'account_type' => $accountType,
                    'account_group_title' => 171, // invalid (not string)
                    'bk_uid' => $bk_uid,
                    'account_group_bk_code' => $bk_code,
                    'is_current' => 0,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'account_group_id' => $accountGroupId,
                    'book_id' => $bookId,
                    'account_type' => $accountType,
                    'account_group_title' => $accountGroupTitle,
                    'bk_uid' => '123', // invalid (not int)
                    'account_group_bk_code' => $bk_code,
                    'is_current' => 0,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'account_group_id' => $accountGroupId,
                    'book_id' => $bookId,
                    'account_type' => $accountType,
                    'account_group_title' => $accountGroupTitle,
                    'bk_uid' => $bk_uid,
                    'account_group_bk_code' => '123', // invalid (not int)
                    'is_current' => 0,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'account_group_id' => $accountGroupId,
                    'book_id' => $bookId,
                    'account_type' => $accountType,
                    'account_group_title' => $accountGroupTitle,
                    'bk_uid' => $bk_uid,
                    'account_group_bk_code' => $bk_code,
                    'is_current' => false, // invalid (not int)
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'account_group_id' => $accountGroupId,
                    'book_id' => $bookId,
                    'account_type' => $accountType,
                    'account_group_title' => $accountGroupTitle,
                    'bk_uid' => $bk_uid,
                    'account_group_bk_code' => $bk_code,
                    'is_current' => 0,
                    'display_order' => '3', // invalid (not int)
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'account_group_id' => $accountGroupId,
                    'book_id' => $bookId,
                    'account_type' => $accountType,
                    'account_group_title' => $accountGroupTitle,
                    'bk_uid' => $bk_uid,
                    'account_group_bk_code' => $bk_code,
                    'is_current' => 0,
                    'display_order' => $displayOrder,
                    'updated_at' => '2023-02-25 16:03:02', // invalid format
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'account_group_id' => $accountGroupId,
                    'book_id' => $bookId,
                    'account_type' => $accountType,
                    'account_group_title' => $accountGroupTitle,
                    'bk_uid' => $bk_uid,
                    'account_group_bk_code' => $bk_code,
                    'is_current' => 0,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => 0, // invalid (not bool)
                ],
                null,
            ],
        ];
    }
}
