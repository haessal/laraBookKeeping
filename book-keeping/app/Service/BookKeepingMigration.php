<?php

namespace App\Service;

use Illuminate\Support\Facades\Auth;

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
     * @param  \App\Service\BookKeepingMigrationTools $tools
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
     * @return array{0:int, 1:array<string, mixed>|null}
     */
    public function importBooks($sourceUrl, $accessToken)
    {
        $status = BookKeepingService::STATUS_ERROR_BAD_CONDITION;
        $importResult = null;

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
                foreach ($books as $bookId => $book) {
                    [$importable, $reason] = $this->isImportable($bookId);
                    if ($importable) {
                        $resultOfImportBook = $this->book->importInformation(
                            $sourceUrl, $accessToken, intval(Auth::id()), $book['book']
                        );
                        $resultOfImportAccounts = $this->account->importAccounts(
                            $sourceUrl, $accessToken, $book['book']['book_id']
                        );
                        $resultOfImportSlips = $this->slip->importSlips(
                            $sourceUrl, $accessToken, $book['book']['book_id']
                        );
                        $importResult['books'][$bookId] = [
                            'status'   => 'success',
                            'book'     => $resultOfImportBook,
                            'accounts' => $resultOfImportAccounts,
                            'slips'    => $resultOfImportSlips,
                        ];
                    } else {
                        $importResult['books'][$bookId] = [
                            'status' => 'failed',
                            'reason' => 'The book is already exist and prohibit to write.('.strval($reason).')',
                        ];
                    }
                }
                $status = BookKeepingService::STATUS_NORMAL;
            }
        }

        return [$status, $importResult];
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