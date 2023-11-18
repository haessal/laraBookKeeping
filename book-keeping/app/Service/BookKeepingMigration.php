<?php

namespace App\Service;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BookKeepingMigration
{
    /**
     * Account migration service instance.
     *
     * @var \App\Service\AccountMigrationService
     */
    private $account;

    /**
     * Book migration service instance.
     *
     * @var \App\Service\BookMigrationService
     */
    private $book;

    /**
     * Slip migration service instance.
     *
     * @var \App\Service\SlipMigrationService
     */
    private $slip;

    /**
     * BookKeeping migration tools instance.
     *
     * @var \App\Service\BookKeepingMigrationTools
     */
    private $tools;

    /**
     * Create a new BookKeepingMigration instance.
     *
     * @param  \App\Service\BookMigrationService  $book
     * @param  \App\Service\AccountMigrationService  $account
     * @param  \App\Service\SlipMigrationService  $slip
     * @param  \App\Service\BookKeepingMigrationTools  $tools
     */
    public function __construct(BookMigrationService $book, AccountMigrationService $account, SlipMigrationService $slip, BookKeepingMigrationTools $tools)
    {
        $this->book = $book;
        $this->account = $account;
        $this->slip = $slip;
        $this->tools = $tools;
    }

    /**
     * Dump books.
     *
     * @return array<string, array{
     *   book: array{
     *     book_id: string,
     *     book_name: string,
     *     display_order: int|null,
     *     updated_at: string|null,
     *     deleted: bool,
     *   }|null,
     *   accounts: array<string, array{
     *     account_group_id: string,
     *     book_id: string,
     *     account_type: string,
     *     account_group_title: string,
     *     bk_uid: int|null,
     *     account_group_bk_code: int|null,
     *     is_current: bool,
     *     display_order: int|null,
     *     updated_at: string|null,
     *     deleted: bool,
     *     items: array<string, array{
     *       account_id: string,
     *       account_group_id: string,
     *       account_title: string,
     *       description: string,
     *       selectable: bool,
     *       bk_uid: int|null,
     *       account_bk_code: int|null,
     *       display_order: int|null,
     *       updated_at: string|null,
     *       deleted: bool,
     *     }>,
     *   }>,
     *   slips: array<string, array{
     *     slip_id: string,
     *     book_id: string,
     *     slip_outline: string,
     *     slip_memo: string|null,
     *     date: string,
     *     is_draft: bool,
     *     display_order: int|null,
     *     updated_at: string|null,
     *     deleted: bool,
     *     entries: array<string, array{
     *       slip_entry_id: string,
     *       slip_id: string,
     *       debit: string,
     *       credit: string,
     *       amount: int,
     *       client: string,
     *       outline: string,
     *       display_order: int|null,
     *       updated_at: string|null,
     *       deleted: bool,
     *     }>,
     *   }>,
     * }>
     */
    public function dumpBooks(): array
    {
        $books = [];

        $bookList = $this->book->retrieveBooks(intval(Auth::id()));
        foreach ($bookList as $book) {
            $bookId = $book['book_id'];
            $books[$bookId] = [
                'book'     => $this->book->exportInformation($bookId),
                'accounts' => $this->account->dumpAccounts($bookId),
                'slips'    => $this->slip->dumpSlips($bookId),
            ];
        }

        return $books;
    }

    /**
     * Export the account group.
     *
     * @param  string  $bookId
     * @param  string  $accountGroupId
     * @return array{0:int, 1:array<string, array{
     *   accounts: array<string, array{
     *     account_group_id: string,
     *     book_id: string,
     *     account_type: string,
     *     account_group_title: string,
     *     bk_uid: int|null,
     *     account_group_bk_code: int|null,
     *     is_current: bool,
     *     display_order: int|null,
     *     updated_at: string|null,
     *       deleted: bool,
     *   }>,
     * }>|null}
     */
    public function exportAccountGroup($bookId, $accountGroupId): array
    {
        [$authorizedStatus, $bookId]
            = $this->book->retrieveDefaultBookOrCheckReadable($bookId, intval(Auth::id()));
        if ($authorizedStatus != BookKeepingService::STATUS_NORMAL) {
            return [$authorizedStatus, null];
        }

        $books = [];
        $books[$bookId] = ['accounts' => $this->account->exportAccountGroup($bookId, $accountGroupId)];

        return [BookKeepingService::STATUS_NORMAL, $books];
    }

    /**
     * Export the account item.
     *
     * @param  string  $bookId
     * @param  string  $accountGroupId
     * @param  string  $accountId
     * @return array{0:int, 1:array<string, array{
     *   accounts: array<string, array{
     *     items: array<string, array{
     *       account_id: string,
     *       account_group_id: string,
     *       account_title: string,
     *       description: string,
     *       selectable: bool,
     *       bk_uid: int|null,
     *       account_bk_code: int|null,
     *       display_order: int|null,
     *       updated_at: string|null,
     *       deleted: bool,
     *     }>,
     *   }>,
     * }>|null}
     */
    public function exportAccountItem($bookId, $accountGroupId, $accountId): array
    {
        [$authorizedStatus, $bookId]
            = $this->book->retrieveDefaultBookOrCheckReadable($bookId, intval(Auth::id()));
        if ($authorizedStatus != BookKeepingService::STATUS_NORMAL) {
            return [$authorizedStatus, null];
        }

        $books = [];
        $books[$bookId] = ['accounts' => $this->account->exportAccountItem($bookId, $accountGroupId, $accountId)];

        return [BookKeepingService::STATUS_NORMAL, $books];
    }

    /**
     * Export a list of account items belonging to the account group.
     *
     * @param  string  $bookId
     * @param  string  $accountGroupId
     * @return array{0:int, 1:array<string, array{
     *   accounts: array<string, array{
     *     items: array<string, array{
     *       account_id: string,
     *       updated_at: string|null,
     *     }>,
     *   }>,
     * }>|null}
     */
    public function exportAccountItems($bookId, $accountGroupId): array
    {
        [$authorizedStatus, $bookId]
            = $this->book->retrieveDefaultBookOrCheckReadable($bookId, intval(Auth::id()));
        if ($authorizedStatus != BookKeepingService::STATUS_NORMAL) {
            return [$authorizedStatus, null];
        }

        $books = [];
        $books[$bookId] = ['accounts' => $this->account->exportAccountItems($bookId, $accountGroupId)];

        return [BookKeepingService::STATUS_NORMAL, $books];
    }

    /**
     * Export accounts of the book.
     *
     * @param  string  $bookId
     * @return array{0:int, 1:array<string, array{
     *   accounts: array<string, array{
     *     account_group_id: string,
     *     updated_at: string|null,
     *   }>,
     * }>|null}
     */
    public function exportAccounts($bookId): array
    {
        [$authorizedStatus, $bookId]
            = $this->book->retrieveDefaultBookOrCheckReadable($bookId, intval(Auth::id()));
        if ($authorizedStatus != BookKeepingService::STATUS_NORMAL) {
            return [$authorizedStatus, null];
        }

        $books = [];
        $books[$bookId] = ['accounts' => $this->account->exportAccounts($bookId)];

        return [BookKeepingService::STATUS_NORMAL, $books];
    }

    /**
     * Export the book.
     *
     * @param  string  $bookId
     * @return array{0:int, 1:array<string, array{
     *   book: array{
     *     book_id: string,
     *     book_name: string,
     *     display_order: int|null,
     *     updated_at: string|null,
     *     deleted: bool,
     *   }|null,
     * }>|null}
     */
    public function exportBook($bookId): array
    {
        [$authorizedStatus, $bookId]
            = $this->book->retrieveDefaultBookOrCheckReadable($bookId, intval(Auth::id()));
        if ($authorizedStatus != BookKeepingService::STATUS_NORMAL) {
            return [$authorizedStatus, null];
        }

        $books = [];
        $books[$bookId] = ['book' => $this->book->exportInformation($bookId)];

        return [BookKeepingService::STATUS_NORMAL, $books];
    }

    /**
     * Export books.
     *
     * @return array<string, array{
     *   book: array{
     *     book_id: string,
     *     updated_at: string|null,
     *   },
     * }>
     */
    public function exportBooks(): array
    {
        $books = [];

        $bookList = $this->book->retrieveBooks(intval(Auth::id()));
        foreach ($bookList as $book) {
            $bookId = $book['book_id'];
            $bookInformation = $this->book->exportInformation($bookId);
            if (isset($bookInformation)) {
                $books[$bookId] = [
                    'book' => [
                        'book_id'    => $bookInformation['book_id'],
                        'updated_at' => $bookInformation['updated_at'],
                    ],
                ];
            }
        }

        return $books;
    }

    /**
     * Export the slip.
     *
     * @param  string  $bookId
     * @param  string  $slipId
     * @return array{0:int, 1:array<string, array{
     *   slips: array<string, array{
     *     slip_id: string,
     *     book_id: string,
     *     slip_outline: string,
     *     slip_memo: string|null,
     *     date: string,
     *     is_draft: bool,
     *     display_order: int|null,
     *     updated_at: string|null,
     *     deleted: bool,
     *   }>,
     * }>|null}
     */
    public function exportSlip($bookId, $slipId): array
    {
        [$authorizedStatus, $bookId]
            = $this->book->retrieveDefaultBookOrCheckReadable($bookId, intval(Auth::id()));
        if ($authorizedStatus != BookKeepingService::STATUS_NORMAL) {
            return [$authorizedStatus, null];
        }

        $books = [];
        $books[$bookId] = ['slips' => $this->slip->exportSlip($bookId, $slipId)];

        return [BookKeepingService::STATUS_NORMAL, $books];
    }

    /**
     * Export a list of entries on the slip.
     *
     * @param  string  $bookId
     * @param  string  $slipId
     * @return array{0:int, 1:array<string, array{
     *   slips: array<string, array{
     *     entries: array<string, array{
     *       slip_entry_id: string,
     *       updated_at: string|null,
     *     }>,
     *   }>,
     * }>|null}
     */
    public function exportSlipEntries($bookId, $slipId): array
    {
        [$authorizedStatus, $bookId]
            = $this->book->retrieveDefaultBookOrCheckReadable($bookId, intval(Auth::id()));
        if ($authorizedStatus != BookKeepingService::STATUS_NORMAL) {
            return [$authorizedStatus, null];
        }

        $books = [];
        $books[$bookId] = ['slips' => $this->slip->exportSlipEntries($bookId, $slipId)];

        return [BookKeepingService::STATUS_NORMAL, $books];
    }

    /**
     * Export the slip entry.
     *
     * @param  string  $bookId
     * @param  string  $slipId
     * @param  string  $slipEntryId
     * @return array{0:int, 1:array<string, array{
     *   slips: array<string, array{
     *     entries: array<string, array{
     *       slip_entry_id: string,
     *       slip_id: string,
     *       debit: string,
     *       credit: string,
     *       amount: int,
     *       client: string,
     *       outline: string,
     *       display_order: int|null,
     *       updated_at: string|null,
     *       deleted: bool,
     *     }>,
     *   }>,
     * }>|null}
     */
    public function exportSlipEntry($bookId, $slipId, $slipEntryId): array
    {
        [$authorizedStatus, $bookId]
            = $this->book->retrieveDefaultBookOrCheckReadable($bookId, intval(Auth::id()));
        if ($authorizedStatus != BookKeepingService::STATUS_NORMAL) {
            return [$authorizedStatus, null];
        }

        $books = [];
        $books[$bookId] = ['slips' => $this->slip->exportSlipEntry($bookId, $slipId, $slipEntryId)];

        return [BookKeepingService::STATUS_NORMAL, $books];
    }

    /**
     * Export slips of the book.
     *
     * @param  string  $bookId
     * @return array{0:int, 1:array<string, array{
     *   slips: array<string, array{
     *     slip_id: string,
     *     updated_at: string|null,
     *   }>,
     * }>|null}
     */
    public function exportSlips($bookId): array
    {
        [$authorizedStatus, $bookId]
            = $this->book->retrieveDefaultBookOrCheckReadable($bookId, intval(Auth::id()));
        if ($authorizedStatus != BookKeepingService::STATUS_NORMAL) {
            return [$authorizedStatus, null];
        }

        $books = [];
        $books[$bookId] = ['slips' => $this->slip->exportSlips($bookId)];

        return [BookKeepingService::STATUS_NORMAL, $books];
    }

    /**
     * Import books.
     *
     * @param  string  $sourceUrl
     * @param  string  $accessToken
     * @return array{0:int, 1:array<string, mixed>|null, 2: string|null}
     */
    public function importBooks($sourceUrl, $accessToken)
    {
        $status = BookKeepingService::STATUS_NORMAL;
        $importResult = null;
        $errorMessage = null;

        $response = $this->tools->getFromExporter($sourceUrl, $accessToken);
        if ($response->ok()) {
            $importResult = [];
            /** @var array{
             *   version: string,
             *   books: array<string, array{
             *     book: array{
             *       book_id: string,
             *       updated_at: string|null,
             *     }
             *   }>,
             * }|null $responseBody
             */
            $responseBody = $response->json();
            if (isset($responseBody)) {
                $books = $responseBody['books'];
                $importResult['version'] = $responseBody['version'];
                $importResult['books'] = [];
                $booksNumber = count($books);
                $booksCount = 0;
                foreach ($books as $bookId => $book) {
                    $importResult['books'][$bookId] = [];
                    [$importable, $reason] = $this->isImportable($bookId);
                    if ($importable) {
                        // book
                        [$resultOfImportBook, $errorMessage] = $this->book->importInformation(
                            $sourceUrl, $accessToken, intval(Auth::id()), $book['book']
                        );
                        $importResult['books'][$bookId]['book'] = $resultOfImportBook;
                        if (isset($errorMessage)) {
                            $status = BookKeepingService::STATUS_ERROR_BAD_CONDITION;
                            break;
                        }
                        Log::debug('import: book information '.sprintf('%2d', $booksCount).'/'.sprintf('%2d', $booksNumber).' '.$bookId.' '.$resultOfImportBook['result']);
                        $booksCount++;
                        // accounts
                        [$resultOfImportAccounts, $errorMessage] = $this->account->importAccounts(
                            $sourceUrl, $accessToken, $book['book']['book_id']
                        );
                        $importResult['books'][$bookId]['accounts'] = $resultOfImportAccounts;
                        if (isset($errorMessage)) {
                            $status = BookKeepingService::STATUS_ERROR_BAD_CONDITION;
                            break;
                        }
                        // slips
                        [$resultOfImportSlips, $errorMessage] = $this->slip->importSlips(
                            $sourceUrl, $accessToken, $book['book']['book_id']
                        );
                        $importResult['books'][$bookId]['slips'] = $resultOfImportSlips;
                        if (isset($errorMessage)) {
                            $status = BookKeepingService::STATUS_ERROR_BAD_CONDITION;
                            break;
                        }
                    } else {
                        $errorMessage = 'The book is already exist and prohibit to write. '.$bookId;
                        $status = $reason;
                        break;
                    }
                }
            } else {
                $errorMessage = 'No response data. '.$sourceUrl;
                $status = BookKeepingService::STATUS_ERROR_BAD_CONDITION;
            }
        } else {
            $errorMessage = 'Response error('.$response->status().'). '.$sourceUrl;
            $status = BookKeepingService::STATUS_ERROR_BAD_CONDITION;
        }

        return [$status, $importResult, $errorMessage];
    }

    /**
     * Load books.
     *
     * @param  array<string, mixed>  $contents
     * @return array{0:int, 1:array<string, mixed>|null, 2: string|null}
     */
    public function loadBooks($contents)
    {
        $status = BookKeepingService::STATUS_NORMAL;
        $importResult = null;
        $errorMessage = null;

        $importResult = [];
        if (key_exists('version', $contents) && is_string($contents['version'])) {
            $importResult['version'] = $contents['version'];
        } else {
            $status = BookKeepingService::STATUS_ERROR_BAD_CONDITION;
            $errorMessage = 'invalid data format: version';

            return [$status, $importResult, $errorMessage];
        }
        if (key_exists('books', $contents) && is_array($contents['books'])) {
            $importResult['books'] = [];
            $books = $contents['books'];
            $booksNumber = count($books);
            $booksCount = 0;
            foreach ($books as $bookId => $book) {
                Log::debug('load: start book '.sprintf('%2d', $booksCount).'/'.sprintf('%2d', $booksNumber).' '.$bookId);
                $importResult['books'][$bookId] = [];
                [$importable, $reason] = $this->isImportable($bookId);
                if ($importable) {
                    // book
                    if (key_exists('book', $book)) {
                        [$resultOfImportBook, $errorMessage] = $this->book->loadInformation(intval(Auth::id()), $book['book']);
                        $importResult['books'][$bookId]['book'] = $resultOfImportBook;
                        if (isset($errorMessage)) {
                            $status = BookKeepingService::STATUS_ERROR_BAD_CONDITION;
                            break;
                        }
                        Log::debug('load: book information '.sprintf('%2d', $booksCount).'/'.sprintf('%2d', $booksNumber).' '.$bookId.' '.$resultOfImportBook['result']);
                    }
                    $booksCount++;
                    // accounts
                    if (key_exists('accounts', $book)) {
                        [$resultOfImportAccounts, $errorMessage] = $this->account->loadAccounts($bookId, $book['accounts']);
                        $importResult['books'][$bookId]['accounts'] = $resultOfImportAccounts;
                        if (isset($errorMessage)) {
                            $status = BookKeepingService::STATUS_ERROR_BAD_CONDITION;
                            break;
                        }
                    }
                    // slips
                    if (key_exists('slips', $book)) {
                        [$resultOfImportSlips, $errorMessage] = $this->slip->loadSlips($bookId, $book['slips']);
                        $importResult['books'][$bookId]['slips'] = $resultOfImportSlips;
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

    /**
     * Check if the UUID is in valid format.
     *
     * @param  string  $uuid
     * @return bool
     */
    public function validateUuid($uuid)
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) === 1;
    }
}
