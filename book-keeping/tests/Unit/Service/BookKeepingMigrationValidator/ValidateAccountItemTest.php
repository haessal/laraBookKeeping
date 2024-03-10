<?php

namespace Tests\Unit\Service\BookKeepingMigrationValidator;

use App\Service\BookKeepingMigrationValidator;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class ValidateAccountItemTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @dataProvider forTestValidateAccountItem
     */
    public function test_it_validates_the_format_of_the_account_item($accountItem, $accountItem_expected): void
    {
        $service = new BookKeepingMigrationValidator();
        $accountItem_actual = $service->validateAccountItem($accountItem);

        $this->assertSame($accountItem_expected, $accountItem_actual);
    }

    public static function forTestValidateAccountItem()
    {
        $accountId = (string) Str::uuid();
        $accountGroupId = (string) Str::uuid();
        $accountTitle = 'account32';
        $accountDescription = 'description33';
        $bk_uid = 34;
        $bk_code = 35;
        $displayOrder = 2;
        $updatedAt = '2023-03-03T18:38:02+09:00';

        return [
            [
                [
                    'account_id' => $accountId,
                    'account_group_id' => $accountGroupId,
                    'account_title' => $accountTitle,
                    'description' => $accountDescription,
                    'selectable' => 1,
                    'bk_uid' => $bk_uid,
                    'account_bk_code' => $bk_code,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                [
                    'account_id' => $accountId,
                    'account_group_id' => $accountGroupId,
                    'account_title' => $accountTitle,
                    'description' => $accountDescription,
                    'selectable' => true,
                    'bk_uid' => $bk_uid,
                    'account_bk_code' => $bk_code,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
            ],
            [
                [
                    'account_id' => $accountId,
                    'account_group_id' => $accountGroupId,
                    'account_title' => $accountTitle,
                    'description' => $accountDescription,
                    'selectable' => 1,
                    'bk_uid' => null,
                    'account_bk_code' => null,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
                [
                    'account_id' => $accountId,
                    'account_group_id' => $accountGroupId,
                    'account_title' => $accountTitle,
                    'description' => $accountDescription,
                    'selectable' => true,
                    'bk_uid' => null,
                    'account_bk_code' => null,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
            ],
            [
                [
                    // 'account_id' => $accountId, key missing
                    'account_group_id' => $accountGroupId,
                    'account_title' => $accountTitle,
                    'description' => $accountDescription,
                    'selectable' => 1,
                    'bk_uid' => $bk_uid,
                    'account_bk_code' => $bk_code,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'account_id' => 123, // invalid uuid (not string)
                    'account_group_id' => $accountGroupId,
                    'account_title' => $accountTitle,
                    'description' => $accountDescription,
                    'selectable' => 1,
                    'bk_uid' => $bk_uid,
                    'account_bk_code' => $bk_code,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'account_id' => $accountId,
                    'account_group_id' => 'aaaa', // invalid uuid
                    'account_title' => $accountTitle,
                    'description' => $accountDescription,
                    'selectable' => 1,
                    'bk_uid' => $bk_uid,
                    'account_bk_code' => $bk_code,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'account_id' => $accountId,
                    'account_group_id' => $accountGroupId,
                    'account_title' => 0, // invalid type (not string)
                    'description' => $accountDescription,
                    'selectable' => 1,
                    'bk_uid' => $bk_uid,
                    'account_bk_code' => $bk_code,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'account_id' => $accountId,
                    'account_group_id' => $accountGroupId,
                    'account_title' => $accountTitle,
                    'description' => 0, // invalid type (not string)
                    'selectable' => 1,
                    'bk_uid' => $bk_uid,
                    'account_bk_code' => $bk_code,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'account_id' => $accountId,
                    'account_group_id' => $accountGroupId,
                    'account_title' => $accountTitle,
                    'description' => $accountDescription,
                    'selectable' => true, // invalid (not int)
                    'bk_uid' => $bk_uid,
                    'account_bk_code' => $bk_code,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'account_id' => $accountId,
                    'account_group_id' => $accountGroupId,
                    'account_title' => $accountTitle,
                    'description' => $accountDescription,
                    'selectable' => 1,
                    'bk_uid' => '123', // invalid (not int)
                    'account_bk_code' => $bk_code,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'account_id' => $accountId,
                    'account_group_id' => $accountGroupId,
                    'account_title' => $accountTitle,
                    'description' => $accountDescription,
                    'selectable' => 1,
                    'bk_uid' => $bk_uid,
                    'account_bk_code' => '123', // invalid (not int)
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'account_id' => $accountId,
                    'account_group_id' => $accountGroupId,
                    'account_title' => $accountTitle,
                    'description' => $accountDescription,
                    'selectable' => 1,
                    'bk_uid' => $bk_uid,
                    'account_bk_code' => $bk_code,
                    'display_order' => '3', // invalid (not int)
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'account_id' => $accountId,
                    'account_group_id' => $accountGroupId,
                    'account_title' => $accountTitle,
                    'description' => $accountDescription,
                    'selectable' => 1,
                    'bk_uid' => $bk_uid,
                    'account_bk_code' => $bk_code,
                    'display_order' => $displayOrder,
                    'updated_at' => '2023-03-03 18:52:02', // invalid format
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'account_id' => $accountId,
                    'account_group_id' => $accountGroupId,
                    'account_title' => $accountTitle,
                    'description' => $accountDescription,
                    'selectable' => 1,
                    'bk_uid' => $bk_uid,
                    'account_bk_code' => $bk_code,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => 0, // invalid (not bool)
                ],
                null,
            ],
        ];
    }
}
