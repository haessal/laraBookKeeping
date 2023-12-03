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
     * Create a new BookKeepingMigration instance.
     *
     * @param  \App\Service\BookMigrationService  $book
     * @param  \App\Service\AccountMigrationService  $account
     * @param  \App\Service\SlipMigrationService  $slip
     */
    public function __construct(BookMigrationService $book, AccountMigrationService $account, SlipMigrationService $slip)
    {
        $this->book = $book;
        $this->account = $account;
        $this->slip = $slip;
    }

    /**
     * Dump books.
     *
     * @return array{
     *   book_id: string,
     *   book: array{
     *     book_id: string,
     *     book_name: string,
     *     display_order: int|null,
     *     updated_at: string|null,
     *     deleted: bool,
     *   }|null,
     *   accounts: array{
     *     account_group_id: string,
     *     account_group: array{
     *       account_group_id: string,
     *       book_id: string,
     *       account_type: string,
     *       account_group_title: string,
     *       bk_uid: int|null,
     *       account_group_bk_code: int|null,
     *       is_current: bool,
     *       display_order: int|null,
     *       updated_at: string|null,
     *       deleted: bool,
     *     },
     *     items: array{
     *       account_id: string,
     *       account: array{
     *         account_id: string,
     *         account_group_id: string,
     *         account_title: string,
     *         description: string,
     *         selectable: bool,
     *         bk_uid: int|null,
     *         account_bk_code: int|null,
     *         display_order: int|null,
     *         updated_at: string|null,
     *         deleted: bool,
     *       },
     *     }[],
     *   }[],
     *   slips: array{
     *     slip_id: string,
     *     slip: array{
     *       slip_id: string,
     *       book_id: string,
     *       slip_outline: string,
     *       slip_memo: string|null,
     *       date: string,
     *       is_draft: bool,
     *       display_order: int|null,
     *       updated_at: string|null,
     *       deleted: bool,
     *     },
     *     entries: array{
     *       slip_entry_id: string,
     *       slip_entry: array{
     *         slip_entry_id: string,
     *         slip_id: string,
     *         debit: string,
     *         credit: string,
     *         amount: int,
     *         client: string,
     *         outline: string,
     *         display_order: int|null,
     *         updated_at: string|null,
     *         deleted: bool,
     *       },
     *     }[],
     *   }[],
     * }[]
     */
    public function dumpBooks(): array
    {
        $books = [];

        $bookList = $this->book->retrieveBooks(intval(Auth::id()));
        foreach ($bookList as $book) {
            $bookId = $book['book_id'];
            $books[] = [
                'book_id' => $bookId,
                'book' => $this->book->exportInformation($bookId),
                'accounts' => $this->account->dumpAccounts($bookId),
                'slips' => $this->slip->dumpSlips($bookId),
            ];
        }

        return $books;
    }

    /**
     * Export books.
     *
     * @return array{
     *   book_id: string,
     *   book: array{
     *     book_id: string,
     *     updated_at: string|null,
     *   },
     * }[]
     */
    public function exportBooks(): array
    {
        $books = [];

        $bookList = $this->book->retrieveBooks(intval(Auth::id()));
        foreach ($bookList as $book) {
            $bookId = $book['book_id'];
            $bookInformation = $this->book->exportInformation($bookId);
            if (isset($bookInformation)) {
                $books[] = [
                    'book_id' => $bookId,
                    'book' => [
                        'book_id' => $bookInformation['book_id'],
                        'updated_at' => $bookInformation['updated_at'],
                    ],
                ];
            }
        }

        return $books;
    }
}
