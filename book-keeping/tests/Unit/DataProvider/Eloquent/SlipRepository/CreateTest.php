<?php

namespace Tests\Unit\DataProvider\Eloquent\SlipRepository;

use App\DataProvider\Eloquent\SlipRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateTest extends TestCase
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
        $bookId = (string) Str::uuid();
        $outline = 'outline2';
        $date = '2019-07-02';
        $memo = 'memo2';
        $isDraft = false;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = $this->slip->create($bookId, $outline, $date, $memo, null, $isDraft);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertDatabaseHas('bk2_0_slips', [
            'slip_id'       => $slipId,
            'book_id'       => $bookId,
            'slip_outline'  => $outline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft,
        ]);
    }
}
