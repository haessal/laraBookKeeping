<?php

namespace App\Service;

use App\Repositories\CreditCardStatementRepositoryInterface;
use Illuminate\Support\Facades\Log;

class CreditCardStatementMigrationLoaderService extends CreditCardStatementMigrationService
{
    /**
     * Validator for loading.
     *
     * @var \App\Service\BookKeepingMigrationValidator
     */
    private $validator;

    /**
     * Create a new CreditCardStatementMigrationLoaderService instance.
     *
     * @param  \App\Repositories\CreditCardStatementRepositoryInterface  $creditCardStatement
     * @param  \App\Service\BookKeepingMigrationTools  $tools
     */
    public function __construct(CreditCardStatementRepositoryInterface $creditCardStatement, BookKeepingMigrationTools $tools, BookKeepingMigrationValidator $validator)
    {
        parent::__construct($creditCardStatement, $tools);
        $this->validator = $validator;
    }

    /**
     * Load the credit card statement.
     *
     * @param  array<string, mixed>  $creditCardStatement
     * @param array<string, array{
     *   credit_card_statement_id: string,
     *   updated_at: string|null,
     * }> $destinationCreditCardStatements
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function loadCreditCardStatement(array $creditCardStatement, array $destinationCreditCardStatements): array
    {
        $mode = null;
        $result = null;
        $error = null;

        $newCreditCardStatement = $this->validator->validateCreditCardStatement($creditCardStatement);
        if (is_null($newCreditCardStatement)) {
            $error = 'invalid data format: credit card statement';

            return [['credit_card_statement_id' => null, 'result' => $result], $error];
        }
        $creditCardStatementId = $newCreditCardStatement['credit_card_statement_id'];
        if (key_exists($creditCardStatementId, $destinationCreditCardStatements)) {
            $sourceUpdateAt = $newCreditCardStatement['updated_at'];
            $destinationUpdateAt = $destinationCreditCardStatements[$creditCardStatementId]['updated_at'];
            if ($this->tools->isSourceLater($sourceUpdateAt, $destinationUpdateAt)) {
                $mode = 'update';
            }
        } else {
            $mode = 'create';
        }
        if (isset($mode)) {
            switch($mode) {
                case 'update':
                    $this->creditCardStatement->updateForImporting($newCreditCardStatement);
                    $result = 'updated';
                    break;
                case 'create':
                    $this->creditCardStatement->createForImporting($newCreditCardStatement);
                    $result = 'created';
                    break;
                default:
                    break;
            }
        } else {
            $result = 'already up-to-date';
        }

        return [['credit_card_statement_id' => $creditCardStatementId, 'result' => $result], $error];
    }

    /**
     * Load the credit card statements of the book.
     *
     * @param  string  $bookId
     * @param  array<string, array<string, mixed>>  $creditCardStatements
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function loadCreditCardStatements($bookId, array $creditCardStatements): array
    {
        $result = [];
        $error = null;

        $destinationCreditCardStatements = $this->exportCreditCardStatements($bookId);
        $creditCardStatementNumber = count($creditCardStatements);
        $creditCardStatementCount = 0;
        foreach ($creditCardStatements as $creditCardStatementIndex => $creditCardStatement) {
            [$result[$creditCardStatementIndex], $error] = $this->loadCreditCardStatement(
                $creditCardStatement, $destinationCreditCardStatements
            );
            if (isset($error)) {
                break;
            }
            /** @var string $creditCardStatementId */
            $creditCardStatementId = $result[$creditCardStatementIndex]['credit_card_statement_id'];
            /** @var string $result_for_log */
            $result_for_log = key_exists('result', $result[$creditCardStatementIndex])
                ? $result[$creditCardStatementIndex]['result']
                : 'null';
            Log::debug('load: credit card statement '
                .sprintf('%2d', $creditCardStatementCount)
                .'/'
                .sprintf('%2d', $creditCardStatementNumber)
                .' '
                .$creditCardStatementId
                .' '
                .$result_for_log
            );
            $creditCardStatementCount++;
        }

        return [$result, $error];
    }
}
