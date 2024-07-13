<?php

namespace Tests\Unit\DataProvider\CreditCardStatementRepositoryInterface;

use App\DataProvider\Eloquent\CreditCardStatementRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    protected $creditCardStatement;

    public function setUp(): void
    {
        parent::setUp();
        $this->creditCardStatement = new CreditCardStatementRepository();
    }

    public function test_it_takes_five_arguments_and_returns_a_value_of_type_string(): void
    {
        $bookId = (string) Str::uuid();
        $outline = 'outline26';
        $date = '2024-07-27';

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $creditCardStatementId = $this->creditCardStatement->create($bookId, $outline, null, $date, null);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(is_string($creditCardStatementId));
    }
}
