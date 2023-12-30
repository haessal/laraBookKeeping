<?php

namespace Tests\Unit\DataProvider\Eloquent\SlipRepository;

use App\DataProvider\Eloquent\SlipRepository;
use App\Models\Slip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchBookForDraftTest extends TestCase
{
    use RefreshDatabase;

    protected $slip;

    public function setUp(): void
    {
        parent::setUp();
        $this->slip = new SlipRepository();
    }

    public function test_it_returns_the_slip(): void
    {
        $bookId = (string) Str::uuid();
        $outline = 'outline5';
        $memo = 'memo5';
        $date = '2019-10-03';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = Slip::factory()->create([
            'book_id' => $bookId,
            'slip_outline' => $outline,
            'slip_memo' => $memo,
            'date' => $date,
            'is_draft' => true,
        ])->slip_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $slips_expected = [
            ['slip_id' => $slipId, 'date' => $date, 'slip_outline' => $outline, 'slip_memo' => $memo],
        ];

        $slips_actual = $this->slip->searchBookForDraft($bookId);

        $this->assertSame($slips_expected, $slips_actual);
    }
}
