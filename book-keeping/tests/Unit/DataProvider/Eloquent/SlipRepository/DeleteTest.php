<?php

namespace Tests\Unit\DataProvider\Eloquent\SlipRepository;

use App\DataProvider\Eloquent\SlipRepository;
use App\Models\Slip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    protected $slip;

    public function setUp(): void
    {
        parent::setUp();
        $this->slip = new SlipRepository();
    }

    public function test_one_record_is_soft_deleted(): void
    {
        $bookId = (string) Str::uuid();
        $outline = 'outline4';
        $memo = 'memo4';
        $date = '2019-09-03';
        $isDraft = false;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = Slip::factory()->create([
            'book_id' => $bookId,
            'slip_outline' => $outline,
            'slip_memo' => $memo,
            'date' => $date,
            'is_draft' => $isDraft,
        ])->slip_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->slip->delete($slipId);

        $this->assertSoftDeleted('bk2_0_slips', [
            'slip_id' => $slipId,
            'book_id' => $bookId,
            'slip_outline' => $outline,
            'slip_memo' => $memo,
            'date' => $date,
            'is_draft' => $isDraft,
        ]);
    }
}
