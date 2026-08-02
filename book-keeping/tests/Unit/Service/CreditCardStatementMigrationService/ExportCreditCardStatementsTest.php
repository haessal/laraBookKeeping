<?php

namespace Tests\Unit\Service\CreditCardStatementMigrationService;

use App\Repositories\CreditCardStatementRepositoryInterface;
use App\Service\BookKeepingMigrationTools;
use App\Service\CreditCardStatementMigrationService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class ExportCreditCardStatementsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_calls_repository_to_export_credit_card_statements(): void
    {
        $bookId = Str::uuid();
        $creditCardStatementId = (string) Str::uuid();
        $updatedAt = '2024-07-06T19:50:02+09:00';
        $creditCardStatement_1 = [
            'credit_card_statement_id' => $creditCardStatementId,
            'updated_at' => $updatedAt,
        ];
        $creditCardStatements_expected = [
            $creditCardStatementId => [
                'credit_card_statement_id' => $creditCardStatementId,
                'updated_at' => $updatedAt,
            ],
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldReceive('convertExportedTimestamp')
            ->once()
            ->with($updatedAt)
            ->andReturn($updatedAt);

        /** @var \App\Repositories\CreditCardStatementRepositoryInterface|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementRepositoryInterface::class);
        $creditCardStatementMock->shouldReceive('searchBookForExporting')
            ->once()
            ->with($bookId)
            ->andReturn([$creditCardStatement_1]);

        $service = new CreditCardStatementMigrationService($creditCardStatementMock, $toolsMock);
        $creditCardStatements_actual = $service->exportCreditCardStatements($bookId);

        $this->assertSame($creditCardStatements_expected, $creditCardStatements_actual);
    }
}
