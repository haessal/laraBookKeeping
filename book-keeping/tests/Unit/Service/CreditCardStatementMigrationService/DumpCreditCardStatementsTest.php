<?php

namespace Tests\Unit\Service\CreditCardStatementMigrationService;

use App\Repositories\CreditCardStatementRepositoryInterface;
use App\Service\BookKeepingMigrationTools;
use App\Service\CreditCardStatementMigrationService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class DumpCreditCardStatementsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_calls_repository_to_export_credit_card_statements_as_dump(): void
    {
        $bookId = Str::uuid();
        $creditCardStatement_1 = [
            'credit_card_statement_id' => (string) Str::uuid(),
        ];
        $creditCardStatements_expected = [$creditCardStatement_1];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldReceive('convertExportedTimestamps')
            ->once()
            ->with($creditCardStatement_1)
            ->andReturn($creditCardStatement_1);

        /** @var \App\Repositories\CreditCardStatementRepositoryInterface|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementRepositoryInterface::class);
        $creditCardStatementMock->shouldReceive('searchBookForExporting')
            ->once()
            ->with($bookId)
            ->andReturn([$creditCardStatement_1]);

        $service = new CreditCardStatementMigrationService($creditCardStatementMock, $toolsMock);
        $creditCardStatements_actual = $service->dumpCreditCardStatements($bookId);

        $this->assertSame($creditCardStatements_expected, $creditCardStatements_actual);
    }
}
