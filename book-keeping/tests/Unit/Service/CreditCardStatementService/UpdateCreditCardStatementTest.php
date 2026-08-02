<?php

namespace Tests\Unit\Service\CreditCardStatementService;

use App\Repositories\CreditCardStatementRepositoryInterface;
use App\Service\CreditCardStatementService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class UpdateCreditCardStatementTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_calls_repository_to_update_the_credit_card_statement(): void
    {
        $creditCardStatementId = (string) Str::uuid();
        $newData = [
            'outline' => 'outline122',
            'memo' => 'memo123',
            'date' => '2024-05-24',
        ];
        /** @var \App\Repositories\CreditCardStatementRepositoryInterface|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementRepositoryInterface::class);
        $creditCardStatementMock->shouldReceive('update')
            ->once()
            ->with($creditCardStatementId, $newData);

        $creditCardStatement = new CreditCardStatementService($creditCardStatementMock);
        $creditCardStatement->updateCreditCardStatement($creditCardStatementId, $newData);

        $this->assertTrue(true);
    }
}
