<?php

namespace Tests\Unit\Service\CreditCardStatementMigrationLoaderService;

use App\DataProvider\CreditCardStatementRepositoryInterface;
use App\Service\BookKeepingMigrationTools;
use App\Service\BookKeepingMigrationValidator;
use App\Service\CreditCardStatementMigrationLoaderService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class LoadCreditCardStatementTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_create_the_credit_card_statement(): void
    {
        $creditCardStatementId = (string) Str::uuid();
        $creditCardStatement = [
            'credit_card_statement_id' => $creditCardStatementId,
        ];
        $result_expected = [
            [
                'credit_card_statement_id' => $creditCardStatementId,
                'result' => 'created',
            ],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldNotReceive('isSourceLater');
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateCreditCardStatement')
            ->once()
            ->with($creditCardStatement)
            ->andReturn($creditCardStatement);
        /** @var \App\DataProvider\CreditCardStatementRepositoryInterface|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementRepositoryInterface::class);
        $creditCardStatementMock->shouldNotReceive('updateForImporting');
        $creditCardStatementMock->shouldReceive('createForImporting')
            ->once()
            ->with($creditCardStatement);

        $service = new CreditCardStatementMigrationLoaderService($creditCardStatementMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadCreditCardStatement($creditCardStatement, []);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_calls_repository_to_update_the_credit_card_statement(): void
    {
        $creditCardStatementId = (string) Str::uuid();
        $creditCardStatementUpdatedAt = '2024-08-03T16:44:30+09:00';
        $destinationUpdateAt = '2024-08-02T16:44:30+09:00';
        $creditCardStatement = [
            'credit_card_statement_id' => $creditCardStatementId,
            'updated_at' => $creditCardStatementUpdatedAt,
        ];
        $destinationCreditCardStatements = [
            $creditCardStatementId => [
                'credit_card_statement_id' => $creditCardStatementId,
                'updated_at' => $destinationUpdateAt,
            ],
        ];
        $result_expected = [
            [
                'credit_card_statement_id' => $creditCardStatementId,
                'result' => 'updated',
            ],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldReceive('isSourceLater')
            ->once()
            ->with($creditCardStatementUpdatedAt, $destinationUpdateAt)
            ->andReturn(true);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateCreditCardStatement')
            ->once()
            ->with($creditCardStatement)
            ->andReturn($creditCardStatement);
        /** @var \App\DataProvider\CreditCardStatementRepositoryInterface|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementRepositoryInterface::class);
        $creditCardStatementMock->shouldReceive('updateForImporting')
            ->once()
            ->with($creditCardStatement);
        $creditCardStatementMock->shouldNotReceive('createForImporting');

        $service = new CreditCardStatementMigrationLoaderService($creditCardStatementMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadCreditCardStatement($creditCardStatement, $destinationCreditCardStatements);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_credit_card_statement_is_already_up_to_date(): void
    {
        $creditCardStatementId = (string) Str::uuid();
        $creditCardStatementUpdatedAt = '2024-08-01T16:44:30+09:00';
        $destinationUpdateAt = '2024-08-02T16:44:30+09:00';
        $creditCardStatement = [
            'credit_card_statement_id' => $creditCardStatementId,
            'updated_at' => $creditCardStatementUpdatedAt,
        ];
        $destinationCreditCardStatements = [
            $creditCardStatementId => [
                'credit_card_statement_id' => $creditCardStatementId,
                'updated_at' => $destinationUpdateAt,
            ],
        ];
        $result_expected = [
            [
                'credit_card_statement_id' => $creditCardStatementId,
                'result' => 'already up-to-date',
            ],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldReceive('isSourceLater')
            ->once()
            ->with($creditCardStatementUpdatedAt, $destinationUpdateAt)
            ->andReturn(false);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateCreditCardStatement')
            ->once()
            ->with($creditCardStatement)
            ->andReturn($creditCardStatement);
        /** @var \App\DataProvider\CreditCardStatementRepositoryInterface|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementRepositoryInterface::class);
        $creditCardStatementMock->shouldNotReceive('updateForImporting');
        $creditCardStatementMock->shouldNotReceive('createForImporting');

        $service = new CreditCardStatementMigrationLoaderService($creditCardStatementMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadCreditCardStatement($creditCardStatement, $destinationCreditCardStatements);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_credit_card_statement_is_not_valid(): void
    {
        $creditCardStatementId = (string) Str::uuid();
        $creditCardStatement = [
            'credit_card_statement_id' => $creditCardStatementId,
        ];
        $destinationCreditCardStatements = [
            $creditCardStatementId => [
                'credit_card_statement_id' => $creditCardStatementId,
            ],
        ];
        $result_expected = [
            [
                'credit_card_statement_id' => null,
                'result' => null,
            ],
            'invalid data format: credit card statement',
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldNotReceive('isSourceLater');
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateCreditCardStatement')
            ->once()
            ->with($creditCardStatement)
            ->andReturn(null);
        /** @var \App\DataProvider\CreditCardStatementRepositoryInterface|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementRepositoryInterface::class);
        $creditCardStatementMock->shouldNotReceive('updateForImporting');
        $creditCardStatementMock->shouldNotReceive('createForImporting');

        $service = new CreditCardStatementMigrationLoaderService($creditCardStatementMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadCreditCardStatement($creditCardStatement, $destinationCreditCardStatements);

        $this->assertSame($result_expected, $result_actual);
    }
}
