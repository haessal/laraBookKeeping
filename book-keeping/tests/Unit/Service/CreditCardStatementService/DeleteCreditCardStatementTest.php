<?php

namespace Tests\Unit\Service\CreditCardStatementService;

use App\DataProvider\CreditCardStatementRepositoryInterface;
use App\Service\CreditCardStatementService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class DeleteCreditCardStatementTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_delete_the_credit_card_statement(): void
    {
        $creditCardStatementId = (string) Str::uuid();
        /** @var \App\DataProvider\CreditCardStatementRepositoryInterface|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementRepositoryInterface::class);
        $creditCardStatementMock->shouldReceive('delete')
            ->once()
            ->with($creditCardStatementId);

        $creditCardStatement = new CreditCardStatementService($creditCardStatementMock);
        $creditCardStatement->deleteCreditCardStatement($creditCardStatementId);

        $this->assertTrue(true);
    }
}
