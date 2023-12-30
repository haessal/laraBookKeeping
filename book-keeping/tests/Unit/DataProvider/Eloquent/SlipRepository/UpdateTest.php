<?php

namespace Tests\Unit\DataProvider\Eloquent\SlipRepository;

use App\DataProvider\Eloquent\SlipRepository;
use App\Models\Slip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateTest extends TestCase
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
        $outline = 'outline6';
        $memo = 'memo6';
        $date = '2019-11-03';
        $isDraft = false;
        $outline_updated = 'outline6_updated';
        $memo_updated = 'memo6_updated';
        $date_updated = '2019-12-03';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = Slip::factory()->create([
            'book_id' => $bookId,
            'slip_outline' => $outline,
            'slip_memo' => $memo,
            'date' => $date,
            'is_draft' => $isDraft,
        ])->slip_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->slip->update($slipId, [
            'outline' => $outline_updated,
            'memo' => $memo_updated,
            'date' => $date_updated,
        ]);

        $this->assertDatabaseHas('bk2_0_slips', [
            'slip_id' => $slipId,
            'book_id' => $bookId,
            'slip_outline' => $outline_updated,
            'slip_memo' => $memo_updated,
            'date' => $date_updated,
            'is_draft' => $isDraft,
        ]);
    }
}
