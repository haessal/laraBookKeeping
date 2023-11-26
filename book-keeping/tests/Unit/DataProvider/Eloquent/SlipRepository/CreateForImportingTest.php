<?php

namespace Tests\Unit\DataProvider\Eloquent\SlipRepository;

use App\DataProvider\Eloquent\SlipRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateForImportingTest extends TestCase
{
    use RefreshDatabase;

    protected $slip;

    public function setUp(): void
    {
        parent::setUp();
        $this->slip = new SlipRepository();
    }

    public function test_one_record_is_created(): void
    {
        $slipId = (string) Str::uuid();
        $bookId = (string) Str::uuid();
        $slipOutline = 'outline34';
        $slipMemo = 'memo35';
        $slipDate = '2023-12-06';
        $isDraft = true;
        $displayOrder = 1;
        $deleted = false;
        $newSlip = [
            'slip_id' => $slipId,
            'book_id' => $bookId,
            'slip_outline' => $slipOutline,
            'slip_memo' => $slipMemo,
            'date' => $slipDate,
            'is_draft' => $isDraft,
            'display_order' => $displayOrder,
            'deleted' => $deleted,
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->slip->createForImporting($newSlip);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertDatabaseHas('bk2_0_slips', [
            'slip_id' => $slipId,
            'book_id' => $bookId,
            'slip_outline' => $slipOutline,
            'slip_memo' => $slipMemo,
            'date' => $slipDate,
            'is_draft' => $isDraft,
            'display_order' => $displayOrder,
            'deleted_at' => null,
        ]);
    }

    public function test_one_record_is_created_and_then_deleted(): void
    {
        $slipId = (string) Str::uuid();
        $bookId = (string) Str::uuid();
        $slipOutline = 'outline64';
        $slipMemo = 'memo65';
        $slipDate = '2023-12-16';
        $isDraft = true;
        $displayOrder = 1;
        $deleted = true;
        $newSlip = [
            'slip_id' => $slipId,
            'book_id' => $bookId,
            'slip_outline' => $slipOutline,
            'slip_memo' => $slipMemo,
            'date' => $slipDate,
            'is_draft' => $isDraft,
            'display_order' => $displayOrder,
            'deleted' => $deleted,
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->slip->createForImporting($newSlip);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertSoftDeleted('bk2_0_slips', [
            'slip_id' => $slipId,
            'book_id' => $bookId,
            'slip_outline' => $slipOutline,
            'slip_memo' => $slipMemo,
            'date' => $slipDate,
            'is_draft' => $isDraft,
            'display_order' => $displayOrder,
        ]);
    }
}
