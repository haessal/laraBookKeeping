<?php

namespace Tests\Unit\DataProvider\Eloquent\SlipRepository;

use App\DataProvider\Eloquent\SlipRepository;
use App\Models\Slip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateForImportingTest extends TestCase
{
    use RefreshDatabase;

    protected $slip;

    public function setUp(): void
    {
        parent::setUp();
        $this->slip = new SlipRepository();
    }

    public function test_one_record_is_updated(): void
    {
        $bookId = (string) Str::uuid();
        $slipOutline = 'outline27';
        $slipMemo = 'memo28';
        $slipDate = '2023-12-29';
        $isDraft = false;
        $displayOrder = 1;
        $deleted = false;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = Slip::factory()->create([
            'book_id' => (string) Str::uuid(),
            'slip_outline' => 'outline36',
            'slip_memo' => 'memo37',
            'date' => '2023-12-08',
            'is_draft' => true,
            'display_order' => 2,
        ])->slip_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
        $this->slip->updateForImporting($newSlip);
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

    public function test_one_record_is_updated_and_then_deleted(): void
    {
        $bookId = (string) Str::uuid();
        $slipOutline = 'outline73';
        $slipMemo = 'memo74';
        $slipDate = '2023-12-05';
        $isDraft = true;
        $displayOrder = 1;
        $deleted = true;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = Slip::factory()->create([
            'book_id' => (string) Str::uuid(),
            'slip_outline' => 'outline82',
            'slip_memo' => 'memo83',
            'date' => '2023-12-04',
            'is_draft' => true,
            'display_order' => 2,
        ])->slip_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
        $this->slip->updateForImporting($newSlip);
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

    public function test_one_record_is_updated_and_then_restored(): void
    {
        $bookId = (string) Str::uuid();
        $slipOutline = 'outline118';
        $slipMemo = 'memo119';
        $slipDate = '2023-12-20';
        $isDraft = true;
        $displayOrder = 1;
        $deleted = false;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slip = Slip::factory()->create([
            'book_id' => (string) Str::uuid(),
            'slip_outline' => 'outline127',
            'slip_memo' => 'memo128',
            'date' => '2023-12-29',
            'is_draft' => true,
            'display_order' => 2,
        ]);
        $slipId = $slip->slip_id;
        $slip->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
        $this->slip->updateForImporting($newSlip);
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

    public function test_one_record_is_updated_and_still_in_the_trash(): void
    {
        $bookId = (string) Str::uuid();
        $slipOutline = 'outline166';
        $slipMemo = 'memo167';
        $slipDate = '2023-12-08';
        $isDraft = true;
        $displayOrder = 1;
        $deleted = true;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slip = Slip::factory()->create([
            'book_id' => (string) Str::uuid(),
            'slip_outline' => 'outline75',
            'slip_memo' => 'memo76',
            'date' => '2023-12-07',
            'is_draft' => true,
            'display_order' => 2,
        ]);
        $slipId = $slip->slip_id;
        $slip->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
        $this->slip->updateForImporting($newSlip);
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
