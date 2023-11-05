<?php

namespace App\Service;

use App\DataProvider\SlipEntryRepositoryInterface;
use App\DataProvider\SlipRepositoryInterface;

class SlipMigrationService extends SlipService
{
    /**
     * BookKeeping migration tools instance.
     *
     * @var \App\Service\BookKeepingMigrationTools
     */
    private $tools;

    /**
     * Create a new SlipMigrationService instance.
     *
     * @param  \App\DataProvider\SlipRepositoryInterface  $slip
     * @param  \App\DataProvider\SlipEntryRepositoryInterface  $slipEntry
     * @param  \App\Service\BookKeepingMigrationTools $tools
     */
    public function __construct(SlipRepositoryInterface $slip, SlipEntryRepositoryInterface $slipEntry, BookKeepingMigrationTools $tools)
    {
        parent::__construct($slip, $slipEntry);
        $this->tools = $tools;
    }

    /**
     * Dump slips of the book.
     *
     * @param  string  $bookId
     * @return array<string, array{
     *   slip_id: string,
     *   book_id: string,
     *   slip_outline: string,
     *   slip_memo: string|null,
     *   date: string,
     *   is_draft: bool,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     *   entries: array<string, array{
     *     slip_entry_id: string,
     *     slip_id: string,
     *     debit: string,
     *     credit: string,
     *     amount: int,
     *     client: string,
     *     outline: string,
     *     display_order: int|null,
     *     updated_at: string|null,
     *     deleted: bool,
     *   }>,
     * }>
     */
    public function dumpSlips($bookId): array
    {
        $slips = [];

        /** @var array{
         *   slip_id: string,
         *   book_id: string,
         *   slip_outline: string,
         *   slip_memo: string|null,
         *   date: string,
         *   is_draft: bool,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }[] $slipList
         */
        $slipList = $this->slip->searchBookForExporting($bookId);
        foreach ($slipList as $slip) {
            $slipId = $slip['slip_id'];
            /** @var array{
             *   slip_id: string,
             *   book_id: string,
             *   slip_outline: string,
             *   slip_memo: string|null,
             *   date: string,
             *   is_draft: bool,
             *   display_order: int|null,
             *   updated_at: string|null,
             *   deleted: bool,
             * } $convertedSlip
             */
            $convertedSlip = $this->tools->convertExportedTimestamps($slip);
            $slips[$slipId] = $convertedSlip;
            $entries = [];
            /** @var array{
             *   slip_entry_id: string,
             *   slip_id: string,
             *   debit: string,
             *   credit: string,
             *   amount: int,
             *   client: string,
             *   outline: string,
             *   display_order: int|null,
             *   created_at: string|null,
             *   updated_at: string|null,
             *   deleted_at: string|null,
             * }[] $slipEntryList
             */
            $slipEntryList = $this->slipEntry->searchSlipForExporting($slipId);
            foreach ($slipEntryList as $slipEntry) {
                /** @var array{
                 *   slip_entry_id: string,
                 *   slip_id: string,
                 *   debit: string,
                 *   credit: string,
                 *   amount: int,
                 *   client: string,
                 *   outline: string,
                 *   display_order: int|null,
                 *   updated_at: string|null,
                 *   deleted: bool,
                 * } $convertedSlipEntry
                 */
                $convertedSlipEntry = $this->tools->convertExportedTimestamps($slipEntry);
                $entries[$slipEntry['slip_entry_id']] = $convertedSlipEntry;
            }
            $slips[$slipId]['entries'] = $entries;
        }

        return $slips;
    }

    /**
     * Export the slip.
     *
     * @param  string  $bookId
     * @param  string  $slipId
     * @return array<string, array{
     *   slip_id: string,
     *   book_id: string,
     *   slip_outline: string,
     *   slip_memo: string|null,
     *   date: string,
     *   is_draft: bool,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }>
     */
    public function exportSlip($bookId, $slipId): array
    {
        $slips = [];

        /** @var array{
         *   slip_id: string,
         *   book_id: string,
         *   slip_outline: string,
         *   slip_memo: string|null,
         *   date: string,
         *   is_draft: bool,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }[] $slipList
         */
        $slipList = $this->slip->searchBookForExporting($bookId, $slipId);
        foreach ($slipList as $slip) {
            /** @var array{
             *   slip_id: string,
             *   book_id: string,
             *   slip_outline: string,
             *   slip_memo: string|null,
             *   date: string,
             *   is_draft: bool,
             *   display_order: int|null,
             *   updated_at: string|null,
             *   deleted: bool,
             * } $convertedSlip
             */
            $convertedSlip = $this->tools->convertExportedTimestamps($slip);
            $slips[$slip['slip_id']] = $convertedSlip;
        }

        return $slips;
    }

    /**
     * Export a list of entries on the slip.
     *
     * @param  string  $bookId
     * @param  string  $slipId
     * @return array<string, array{
     *   entries: array<string, array{
     *     slip_entry_id: string,
     *     updated_at: string|null,
     *   }>,
     * }>
     */
    public function exportSlipEntries($bookId, $slipId): array
    {
        $slips = [];

        /** @var array{
         *   slip_id: string,
         *   book_id: string,
         *   slip_outline: string,
         *   slip_memo: string|null,
         *   date: string,
         *   is_draft: bool,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }[] $slipList
         */
        $slipList = $this->slip->searchBookForExporting($bookId, $slipId);
        foreach ($slipList as $slip) {
            $entries = [];
            /** @var array{
             *   slip_entry_id: string,
             *   slip_id: string,
             *   debit: string,
             *   credit: string,
             *   amount: int,
             *   client: string,
             *   outline: string,
             *   display_order: int|null,
             *   created_at: string|null,
             *   updated_at: string|null,
             *   deleted_at: string|null,
             * }[] $slipEntryList
             */
            $slipEntryList = $this->slipEntry->searchSlipForExporting(strval($slip['slip_id']));
            foreach ($slipEntryList as $slipEntry) {
                $entries[$slipEntry['slip_entry_id']] = [
                    'slip_entry_id' => $slipEntry['slip_entry_id'],
                    'updated_at'    => $slipEntry['updated_at'],
                ];
            }
            $slips[$slip['slip_id']] = ['entries' => $entries];
        }

        return $slips;
    }

    /**
     * Export the slip entry.
     *
     * @param  string  $bookId
     * @param  string  $slipId
     * @param  string  $slipEntryId
     * @return array<string, array{
     *   entries: array<string, array{
     *     slip_entry_id: string,
     *     slip_id: string,
     *     debit: string,
     *     credit: string,
     *     amount: int,
     *     client: string,
     *     outline: string,
     *     display_order: int|null,
     *     updated_at: string|null,
     *     deleted: bool,
     *   }>,
     * }>
     */
    public function exportSlipEntry($bookId, $slipId, $slipEntryId): array
    {
        $slips = [];

        /** @var array{
         *   slip_id: string,
         *   book_id: string,
         *   slip_outline: string,
         *   slip_memo: string|null,
         *   date: string,
         *   is_draft: bool,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }[] $slipList
         */
        $slipList = $this->slip->searchBookForExporting($bookId, $slipId);
        foreach ($slipList as $slip) {
            $entries = [];
            /** @var array{
             *   slip_entry_id: string,
             *   slip_id: string,
             *   debit: string,
             *   credit: string,
             *   amount: int,
             *   client: string,
             *   outline: string,
             *   display_order: int|null,
             *   created_at: string|null,
             *   updated_at: string|null,
             *   deleted_at: string|null,
             * }[] $slipEntryList
             */
            $slipEntryList = $this->slipEntry->searchSlipForExporting(strval($slip['slip_id']), $slipEntryId);
            foreach ($slipEntryList as $slipEntry) {
                /** @var array{
                 *   slip_entry_id: string,
                 *   slip_id: string,
                 *   debit: string,
                 *   credit: string,
                 *   amount: int,
                 *   client: string,
                 *   outline: string,
                 *   display_order: int|null,
                 *   updated_at: string|null,
                 *   deleted: bool,
                 * } $convertedSlipEntry
                 */
                $convertedSlipEntry = $this->tools->convertExportedTimestamps($slipEntry);
                $entries[$slipEntry['slip_entry_id']] = $convertedSlipEntry;
            }
            $slips[$slip['slip_id']] = ['entries' => $entries];
        }

        return $slips;
    }

    /**
     * Export slips of the book.
     *
     * @param  string  $bookId
     * @return array<string, array{
     *   slip_id: string,
     *   updated_at: string|null,
     * }>
     */
    public function exportSlips($bookId): array
    {
        $slips = [];

        /** @var array{
         *   slip_id: string,
         *   book_id: string,
         *   slip_outline: string,
         *   slip_memo: string|null,
         *   date: string,
         *   is_draft: bool,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }[] $slipList
         */
        $slipList = $this->slip->searchBookForExporting($bookId);
        foreach ($slipList as $slip) {
            $slipId = $slip['slip_id'];
            $slips[$slipId] = [
                'slip_id'    => $slip['slip_id'],
                'updated_at' => $slip['updated_at'],
            ];
        }

        return $slips;
    }

    /**
     * Import slips of the book.
     *
     * @param  string  $sourceUrl
     * @param  string  $accessToken
     * @param  string  $bookId
     * @return array<string, mixed>
     */
    public function importSlips($sourceUrl, $accessToken, $bookId): array
    {
        $debug = ['sourceUrl' => $sourceUrl, 'accessToken' => $accessToken, 'bookId'=> $bookId];

        return ['debug' => $debug];
    }
}
