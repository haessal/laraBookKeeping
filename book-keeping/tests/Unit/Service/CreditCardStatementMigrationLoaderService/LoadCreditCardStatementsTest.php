<?php

namespace Tests\Unit\Service\CreditCardStatementMigrationLoaderService;

use App\DataProvider\CreditCardStatementRepositoryInterface;
use App\Service\BookKeepingMigrationTools;
use App\Service\BookKeepingMigrationValidator;
use App\Service\CreditCardStatementMigrationLoaderService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class LoadCreditCardStatementsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_loads_the_credit_card_statements(): void
    {
        $bookId = (string) Str::uuid();
        $creditCardStatementId_1 = (string) Str::uuid();
        $creditCardStatement_1 = [
            'credit_card_statement_id' => $creditCardStatementId_1,
        ];
        $creditCardStatements = [
            $creditCardStatementId_1 => $creditCardStatement_1,
        ];
        $result_expected = [
            [
                $creditCardStatementId_1 => [
                    'credit_card_statement_id' => $creditCardStatementId_1,
                    'result' => 'created',
                ],
            ],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateCreditCardStatement')  // call from loadCreditCardStatement
            ->once()
            ->with($creditCardStatement_1)
            ->andReturn($creditCardStatement_1);
        /** @var \App\DataProvider\CreditCardStatementRepositoryInterface|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementRepositoryInterface::class);
        $creditCardStatementMock->shouldReceive('searchBookForExporting')  // call from exportCreditCardStatements
            ->once()
            ->with($bookId)
            ->andReturn([]);
        $creditCardStatementMock->shouldReceive('createForImporting')  // call from loadCreditCardStatement
            ->once()
            ->with($creditCardStatement_1);

        $service = new CreditCardStatementMigrationLoaderService($creditCardStatementMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadCreditCardStatements($bookId, $creditCardStatements);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_one_of_the_credit_card_statements_is_in_a_invalid_format(): void
    {
        $bookId = (string) Str::uuid();
        $creditCardStatementId_1 = (string) Str::uuid();
        $creditCardStatement_1 = [
            'credit_card_statement_id' => $creditCardStatementId_1,
        ];
        $creditCardStatements = [
            $creditCardStatementId_1 => $creditCardStatement_1,
        ];
        $result_expected = [
            [
                $creditCardStatementId_1 => [
                    'credit_card_statement_id' => null,
                    'result' => null,
                ],
            ],
            'invalid data format: credit card statement',
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateCreditCardStatement')  // call from loadCreditCardStatement
            ->once()
            ->with($creditCardStatement_1)
            ->andReturn(null);
        /** @var \App\DataProvider\CreditCardStatementRepositoryInterface|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementRepositoryInterface::class);
        $creditCardStatementMock->shouldReceive('searchBookForExporting')  // call from exportCreditCardStatements
            ->once()
            ->with($bookId)
            ->andReturn([]);

        $service = new CreditCardStatementMigrationLoaderService($creditCardStatementMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadCreditCardStatements($bookId, $creditCardStatements);

        $this->assertSame($result_expected, $result_actual);
    }
}
