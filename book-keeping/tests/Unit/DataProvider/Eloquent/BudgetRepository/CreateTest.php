<?php

namespace Tests\Unit\DataProvider\Eloquent\BudgetRepository;

use App\DataProvider\Eloquent\BudgetRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    protected $budget;

    public function setUp(): void
    {
        parent::setUp();
        $this->budget = new BudgetRepository();
    }

    public function test_one_record_is_created(): void
    {
        $bookId = (string) Str::uuid();
        $accountId = (string) Str::uuid();
        $date = '2019-09-01';
        $amount = 10000;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $budgetId = $this->budget->create($bookId, $accountId, $date, $amount);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertDatabaseHas('bk2_0_budgets', [
            'budget_id' => $budgetId,
            'book_id' => $bookId,
            'account_code' => $accountId,
            'date' => $date,
            'amount' => $amount,
        ]);
    }
}
