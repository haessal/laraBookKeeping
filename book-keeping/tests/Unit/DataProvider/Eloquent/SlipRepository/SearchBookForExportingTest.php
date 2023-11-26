<?php

namespace Tests\Unit\DataProvider\Eloquent\SlipRepository;

use App\DataProvider\Eloquent\SlipRepository;
use App\Models\Slip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchBookForExportingTest extends TestCase
{
    use RefreshDatabase;

    protected $slip;

    public function setUp(): void
    {
        parent::setUp();
        $this->slip = new SlipRepository();
    }

    public function test_the_returned_array_has_keys_as_exported_slip(): void
    {
        $bookId = (string) Str::uuid();
        $slipOutline = 'outline27';
        $slipMemo = 'memo28';
        $slipDate = '2023-12-29';
        $isDraft = true;
        $displayOrder = 1;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Slip::factory()->create([
            'book_id' => $bookId,
            'slip_outline' => $slipOutline,
            'slip_memo' => $slipMemo,
            'date' => $slipDate,
            'is_draft' => $isDraft,
            'display_order' => $displayOrder,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $slipList = $this->slip->searchBookForExporting($bookId);

        $this->assertFalse(count($slipList) === 0);
        if (! (count($slipList) === 0)) {
            $this->assertSame([
                'slip_id',
                'book_id',
                'slip_outline',
                'slip_memo',
                'date',
                'is_draft',
                'display_order',
                'created_at',
                'updated_at',
                'deleted_at',
            ], array_keys($slipList[0]));
        }
    }

    public function test_the_returned_array_has_keys_as_exported_slip_even_if_it_is_called_with_slip_id(): void
    {
        $bookId = (string) Str::uuid();
        $slipOutline = 'outline66';
        $slipMemo = 'memo67';
        $slipDate = '2023-12-08';
        $isDraft = true;
        $displayOrder = 1;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = Slip::factory()->create([
            'book_id' => $bookId,
            'slip_outline' => $slipOutline,
            'slip_memo' => $slipMemo,
            'date' => $slipDate,
            'is_draft' => $isDraft,
            'display_order' => $displayOrder,
        ])->slip_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $slipList = $this->slip->searchBookForExporting($bookId, $slipId);

        $this->assertFalse(count($slipList) === 0);
        if (! (count($slipList) === 0)) {
            $this->assertSame([
                'slip_id',
                'book_id',
                'slip_outline',
                'slip_memo',
                'date',
                'is_draft',
                'display_order',
                'created_at',
                'updated_at',
                'deleted_at',
            ], array_keys($slipList[0]));
        }
    }
}
