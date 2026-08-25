<?php

namespace Tests\Unit\Repositories\Eloquent\SlipRepository;

use App\Models\DataProvider\Eloquent\Slip;
use App\Repositories\Eloquent\SlipRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class FindByIdTest extends TestCase
{
    use RefreshDatabase;

    /** @var SlipRepository */
    protected $slip;

    public function setUp(): void
    {
        parent::setUp();
        $this->slip = new SlipRepository();
    }

    public function test_it_returns_the_slip(): void
    {
        $bookId = (string) Str::uuid();
        $outline = 'outline120';
        $memo = 'memo120';
        $date = '2019-01-22';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = Slip::factory()->create([
            'book_id' => $bookId,
            'slip_outline' => $outline,
            'slip_memo' => $memo,
            'date' => $date,
            'is_draft' => false,
        ])->slip_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $slip_expected = ['book_id' => $bookId, 'slip_id' => $slipId, 'date' => $date, 'slip_outline' => $outline, 'slip_memo' => $memo];

        $slip_actual = $this->slip->findById($slipId, $bookId);

        $this->assertSame($slip_expected, $slip_actual);
    }
}
