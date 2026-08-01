<?php

namespace Tests\Unit\Repositories\CreditCardStatementRepositoryInterface;

use App\Repositories\Eloquent\CreditCardStatementRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    protected $creditCardStatement;

    public function setUp(): void
    {
        parent::setUp();
        $this->creditCardStatement = new CreditCardStatementRepository();
    }

    public function test_it_takes_one_argument_and_returns_nothing(): void
    {
        $creditCardStatementId = (string) Str::uuid();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->creditCardStatement->delete($creditCardStatementId);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(true);
    }
}
