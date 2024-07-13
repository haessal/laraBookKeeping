<?php

namespace Tests\Unit\Service\CreditCardStatementService;

use App\DataProvider\CreditCardStatementRepositoryInterface;
use App\Service\CreditCardStatementService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class CreateCreditCardStatementTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_create_a_new_credit_card_statement(): void
    {
        $bookId = (string) Str::uuid();
        $outline = 'outline21';
        $memo = 'memo22';
        $date = '2024-07-13';
        $displayOrder = 1;
        $creditCardStatementId_expected = (string) Str::uuid();
        /** @var \App\DataProvider\CreditCardStatementRepositoryInterface|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementRepositoryInterface::class);
        $creditCardStatementMock->shouldReceive('create')
            ->once()
            ->with($bookId, $outline, $memo, $date, $displayOrder)
            ->andReturn($creditCardStatementId_expected);

        $creditCardStatement = new CreditCardStatementService($creditCardStatementMock);
        $creditCardStatementId_actual = $creditCardStatement->createCreditCardStatement($bookId, $outline, $memo, $date, $displayOrder);

        $this->assertSame($creditCardStatementId_expected, $creditCardStatementId_actual);
    }
}
