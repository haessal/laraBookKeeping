<?php

namespace Tests\Unit\Service\CreditCardStatementService;

use App\Repositories\CreditCardStatementRepositoryInterface;
use App\Service\CreditCardStatementService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrieveCreditCardStatementsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_calls_repository_to_retrieve_a_list_of_credit_card_statements(): void
    {
        $bookId = (string) Str::uuid();
        $creditCardStatementId = (string) Str::uuid();
        $outline = 'outline22';
        $memo = 'memo23';
        $date = '2024-6-24';
        $creditCardStatement = [
            'credit_card_statement_id' => $creditCardStatementId,
            'credit_card_statement_outline' => $outline,
            'credit_card_statement_memo' => $memo,
            'date' => $date,
        ];
        $creditCardStatements_expected = [
            $creditCardStatementId => $creditCardStatement,
        ];
        /** @var \App\Repositories\CreditCardStatementRepositoryInterface|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementRepositoryInterface::class);
        $creditCardStatementMock->shouldReceive('searchBook')
            ->once()
            ->with($bookId, $creditCardStatementId)
            ->andReturn([$creditCardStatement]);

        $creditCardStatement = new CreditCardStatementService($creditCardStatementMock);
        $creditCardStatements_actual = $creditCardStatement->retrieveCreditCardStatements($bookId, $creditCardStatementId);

        $this->assertSame($creditCardStatements_expected, $creditCardStatements_actual);
    }
}
