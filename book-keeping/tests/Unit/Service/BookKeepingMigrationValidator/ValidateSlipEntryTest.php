<?php

namespace Tests\Unit\Service\BookKeepingMigrationValidator;

use App\Service\BookKeepingMigrationValidator;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class ValidateSlipEntryTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @dataProvider forTestValidateSlipEntry
     */
    public function test_it_validates_the_format_of_the_slip_entry($slipEntry, $slipEntry_expected): void
    {
        $service = new BookKeepingMigrationValidator();
        $slipEntry_actual = $service->validateSlipEntry($slipEntry);

        $this->assertSame($slipEntry_expected, $slipEntry_actual);
    }

    public static function forTestValidateSlipEntry()
    {
        $slipEntryId = (string) Str::uuid();
        $slipId = (string) Str::uuid();
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        $amount = 340;
        $client = 'client35';
        $outline = 'outline36';
        $displayOrder = 2;
        $updatedAt = '2023-03-03T19:30:02+09:00';

        return [
            [
                [
                    'slip_entry_id' => $slipEntryId,
                    'slip_id' => $slipId,
                    'debit' => $debit,
                    'credit' => $credit,
                    'amount' => $amount,
                    'client' => $client,
                    'outline' => $outline,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                [
                    'slip_entry_id' => $slipEntryId,
                    'slip_id' => $slipId,
                    'debit' => $debit,
                    'credit' => $credit,
                    'amount' => $amount,
                    'client' => $client,
                    'outline' => $outline,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
            ],
            [
                [
                    'slip_entry_id' => $slipEntryId,
                    'slip_id' => $slipId,
                    'debit' => $debit,
                    'credit' => $credit,
                    'amount' => $amount,
                    'client' => $client,
                    'outline' => $outline,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
                [
                    'slip_entry_id' => $slipEntryId,
                    'slip_id' => $slipId,
                    'debit' => $debit,
                    'credit' => $credit,
                    'amount' => $amount,
                    'client' => $client,
                    'outline' => $outline,
                    'display_order' => null,
                    'updated_at' => null,
                    'deleted' => false,
                ],
            ],
            [
                [
                    // 'slip_entry_id' => $slipEntryId, key missing
                    'slip_id' => $slipId,
                    'debit' => $debit,
                    'credit' => $credit,
                    'amount' => $amount,
                    'client' => $client,
                    'outline' => $outline,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'slip_entry_id' => 123, // invalid uuid (not string)
                    'slip_id' => $slipId,
                    'debit' => $debit,
                    'credit' => $credit,
                    'amount' => $amount,
                    'client' => $client,
                    'outline' => $outline,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'slip_entry_id' => $slipEntryId,
                    'slip_id' => 'aaaa', // invalid uuid
                    'debit' => $debit,
                    'credit' => $credit,
                    'amount' => $amount,
                    'client' => $client,
                    'outline' => $outline,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'slip_entry_id' => $slipEntryId,
                    'slip_id' => $slipId,
                    'debit' => 141, // invalid type (not string)
                    'credit' => $credit,
                    'amount' => $amount,
                    'client' => $client,
                    'outline' => $outline,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'slip_entry_id' => $slipEntryId,
                    'slip_id' => $slipId,
                    'debit' => $debit,
                    'credit' => 157, // invalid type (not string)
                    'amount' => $amount,
                    'client' => $client,
                    'outline' => $outline,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'slip_entry_id' => $slipEntryId,
                    'slip_id' => $slipId,
                    'debit' => $debit,
                    'credit' => $credit,
                    'amount' => '184', // invalid (not int)
                    'client' => $client,
                    'outline' => $outline,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'slip_entry_id' => $slipEntryId,
                    'slip_id' => $slipId,
                    'debit' => $debit,
                    'credit' => $credit,
                    'amount' => $amount,
                    'client' => 200, // invalid type (not string)
                    'outline' => $outline,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'slip_entry_id' => $slipEntryId,
                    'slip_id' => $slipId,
                    'debit' => $debit,
                    'credit' => $credit,
                    'amount' => $amount,
                    'client' => $client,
                    'outline' => 227, // invalid type (not string)
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'slip_entry_id' => $slipEntryId,
                    'slip_id' => $slipId,
                    'debit' => $debit,
                    'credit' => $credit,
                    'amount' => $amount,
                    'client' => $client,
                    'outline' => $outline,
                    'display_order' => '3', // invalid (not int)
                    'updated_at' => $updatedAt,
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'slip_entry_id' => $slipEntryId,
                    'slip_id' => $slipId,
                    'debit' => $debit,
                    'credit' => $credit,
                    'amount' => $amount,
                    'client' => $client,
                    'outline' => $outline,
                    'display_order' => $displayOrder,
                    'updated_at' => '2023-03-03 19:30:02', // invalid format
                    'deleted' => false,
                ],
                null,
            ],
            [
                [
                    'slip_entry_id' => $slipEntryId,
                    'slip_id' => $slipId,
                    'debit' => $debit,
                    'credit' => $credit,
                    'amount' => $amount,
                    'client' => $client,
                    'outline' => $outline,
                    'display_order' => $displayOrder,
                    'updated_at' => $updatedAt,
                    'deleted' => 0, // invalid (not bool)
                ],
                null,
            ],
        ];
    }
}
