<?php

namespace App\Service;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BookKeepingMigrationLoader
{
    /**
     * Account migration service instance.
     *
     * @var \App\Service\AccountMigrationLoaderService
     */
    private $account;

    /**
     * Book migration service instance.
     *
     * @var \App\Service\BookMigrationLoaderService
     */
    private $book;

    /**
     * Slip migration service instance.
     *
     * @var \App\Service\SlipMigrationLoaderService
     */
    private $slip;

    /**
     * Credit card statement migration service instance.
     *
     * @var \App\Service\CreditCardStatementMigrationLoaderService
     */
    private $creditCardStatement;

    /**
     * Create a new BookKeepingMigration instance.
     *
     * @param  \App\Service\BookMigrationLoaderService  $book
     * @param  \App\Service\AccountMigrationLoaderService  $account
     * @param  \App\Service\SlipMigrationLoaderService  $slip
     * @param  \App\Service\CreditCardStatementMigrationLoaderService  $creditCardStatement
     */
    public function __construct(BookMigrationLoaderService $book, AccountMigrationLoaderService $account, SlipMigrationLoaderService $slip, CreditCardStatementMigrationLoaderService $creditCardStatement)
    {
        $this->book = $book;
        $this->account = $account;
        $this->slip = $slip;
        $this->creditCardStatement = $creditCardStatement;
    }

    /**
     * Load books.
     *
     * @param  array<string, mixed>  $contents
     * @return array{0:int, 1:array<string, mixed>|null, 2: string|null}
     */
    public function loadBooks(array $contents): array
    {
        $status = BookKeepingService::STATUS_NORMAL;
        $importResult = [];
        $errorMessage = null;

        if (key_exists('version', $contents) && is_string($contents['version'])) {
            $version = new BookKeepingMigrationVersion($contents['version']);
            $importResult['version'] = $contents['version'];
        } else {
            $status = BookKeepingService::STATUS_ERROR_BAD_CONDITION;
            $errorMessage = 'invalid data format: version';

            return [$status, $importResult, $errorMessage];
        }
        if (key_exists('books', $contents) && is_array($contents['books'])) {
            $importResult['books'] = [];
            /** @var array<int, array<string, mixed>> $books */
            $books = $contents['books'];
            $booksNumber = count($books);
            $booksCount = 0;
            foreach ($books as $bookIndex => $book) {
                $importResult['books'][$bookIndex] = [];
                if (key_exists('book_id', $book)) {
                    /** @var string $bookId */
                    $bookId = $book['book_id'];
                } else {
                    $status = BookKeepingService::STATUS_ERROR_BAD_CONDITION;
                    $errorMessage = 'invalid data format: book_id';

                    return [$status, $importResult, $errorMessage];
                }
                Log::debug('load: start book '
                    .sprintf('%2d', $booksCount)
                    .'/'
                    .sprintf('%2d', $booksNumber)
                    .' '
                    .$bookId
                );
                [$importable, $reason] = $this->isImportable($bookId);
                if ($importable) {
                    // book
                    if (key_exists('book', $book)) {
                        /** @var array<string, mixed> $bookInformation */
                        $bookInformation = $book['book'];
                        [$resultOfImportBook, $errorMessage] = $this->book->loadInformation(intval(Auth::id()), $bookInformation);
                        $importResult['books'][$bookIndex]['book'] = $resultOfImportBook;
                        if (isset($errorMessage)) {
                            $status = BookKeepingService::STATUS_ERROR_BAD_CONDITION;
                            break;
                        }
                        /** @var string $result_for_log */
                        $result_for_log = key_exists('result', $resultOfImportBook)
                            ? $resultOfImportBook['result']
                            : 'null';
                        Log::debug('load: book information '
                            .sprintf('%2d', $booksCount)
                            .'/'
                            .sprintf('%2d', $booksNumber)
                            .' '
                            .$bookId
                            .' '
                            .$result_for_log
                        );
                    }
                    $booksCount++;
                    // accounts
                    if (key_exists('accounts', $book)) {
                        /** @var array<string, array<string, mixed>> $accounts */
                        $accounts = $book['accounts'];
                        [$resultOfImportAccounts, $errorMessage] = $this->account->loadAccounts($version, $bookId, $accounts);
                        $importResult['books'][$bookIndex]['accounts'] = $resultOfImportAccounts;
                        if (isset($errorMessage)) {
                            $status = BookKeepingService::STATUS_ERROR_BAD_CONDITION;
                            break;
                        }
                    }
                    // credit card statements
                    if (key_exists('creditCardStatements', $book)) {
                        /** @var array<string, array<string, mixed>> $creditCardStatements */
                        $creditCardStatements = $book['creditCardStatements'];
                        [$resultOfImportCreditCardStatements, $errorMessage] = $this->creditCardStatement->loadCreditCardStatements($bookId, $creditCardStatements);
                        $importResult['books'][$bookIndex]['creditCardStatements'] = $resultOfImportCreditCardStatements;
                        if (isset($errorMessage)) {
                            $status = BookKeepingService::STATUS_ERROR_BAD_CONDITION;
                            break;
                        }
                    }
                    // slips
                    if (key_exists('slips', $book)) {
                        /** @var array<string, array<string, mixed>> $slips */
                        $slips = $book['slips'];
                        [$resultOfImportSlips, $errorMessage] = $this->slip->loadSlips($version, $bookId, $slips);
                        $importResult['books'][$bookIndex]['slips'] = $resultOfImportSlips;
                        if (isset($errorMessage)) {
                            $status = BookKeepingService::STATUS_ERROR_BAD_CONDITION;
                            break;
                        }
                    }
                } else {
                    $errorMessage = 'The book is already exist and prohibit to write. '.$bookId;
                    $status = $reason;
                    break;
                }
            }
        } else {
            $status = BookKeepingService::STATUS_ERROR_BAD_CONDITION;
            $errorMessage = 'invalid data format: books';

            return [$status, $importResult, $errorMessage];
        }

        return [$status, $importResult, $errorMessage];
    }

    /**
     * Check if the authenticated user can create or update the book.
     *
     * @param  string  $bookId
     * @return array{0:bool, 1:int}
     */
    private function isImportable($bookId): array
    {
        $bookInformation = $this->book->retrieveInformationOf($bookId);
        if (isset($bookInformation)) {
            $bookItem = $this->book->retrieveBook($bookId, intval(Auth::id()));
            if (is_null($bookItem)) {
                return [false, BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE];
            }
            if ($bookItem['modifiable'] == false) {
                return [false, BookKeepingService::STATUS_ERROR_AUTH_FORBIDDEN];
            }

            return [true, BookKeepingService::STATUS_NORMAL];
        }

        return [true, BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE];
    }
}
