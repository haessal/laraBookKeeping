<?php

namespace Tests\Unit\Service\BookKeepingMigrationValidator;

use App\Service\BookKeepingMigrationValidator;
use App\Service\BookKeepingMigrationVersion;
use Illuminate\Support\Str;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ValidateAccountItemTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[DataProvider('forTestValidateAccountItem')]
    public function test_it_validates_the_format_of_the_account_item($versionString, $accountItem, $accountItem_expected): void
    {
        $version = new BookKeepingMigrationVersion($versionString);

        $service = new BookKeepingMigrationValidator();
        $accountItem_actual = $service->validateAccountItem($version, $accountItem);

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
                '2.0',
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
                    'is_credit_card' => null,
                    'bk_uid' => $bk_uid,
                    'account_bk_code' => $bk_code,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
            ],
            [
                '2.0',
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
                    'is_credit_card' => null,
                    'bk_uid' => null,
                    'account_bk_code' => null,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
            ],
            [
                '2.0',
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
                '2.0',
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
                '2.0',
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
                '2.0',
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
                '2.0',
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
                '2.0',
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
                '2.0',
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
                '2.0',
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
                '2.0',
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
                '2.0',
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
                '2.0',
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
            [
                '2.1.0',
                [
                    'account_id' => $accountId,
                    'account_group_id' => $accountGroupId,
                    'account_title' => $accountTitle,
                    'description' => $accountDescription,
                    'selectable' => 1,
                    'is_credit_card' => 1,
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
                    'is_credit_card' => true,
                    'bk_uid' => $bk_uid,
                    'account_bk_code' => $bk_code,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
            ],
            [
                '2.1.0',
                [
                    'account_id' => $accountId,
                    'account_group_id' => $accountGroupId,
                    'account_title' => $accountTitle,
                    'description' => $accountDescription,
                    'selectable' => 1,
                    'is_credit_card' => true, // invalid (not int)
                    'bk_uid' => $bk_uid,
                    'account_bk_code' => $bk_code,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
        ];
    }
}
