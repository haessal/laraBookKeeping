<?php

namespace Tests\Unit\DataProvider\CreditCardStatementRepositoryInterface;

use App\DataProvider\Eloquent\CreditCardStatementRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    protected $creditCardStatement;

    public function setUp(): void
    {
        parent::setUp();
        $this->creditCardStatement = new CreditCardStatementRepository();
    }

    public function test_it_takes_two_arguments_and_returns_nothing(): void
    {
        $creditCardStatementId = (string) Str::uuid();
        $newData = [];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->creditCardStatement->update($creditCardStatementId, $newData);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(true);
    }
}
