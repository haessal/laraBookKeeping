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
     * @param  \App\Service\BookKeepingMigrationTools  $tools
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
     * @return array{
     *   slip_id: string,
     *   slip: array{
     *     slip_id: string,
     *     book_id: string,
     *     slip_outline: string,
     *     slip_memo: string|null,
     *     date: string,
     *     is_draft: bool,
     *     display_order: int|null,
     *     updated_at: string|null,
     *     deleted: bool,
     *   },
     *   entries: array{
     *     slip_entry_id: string,
     *     slip_entry: array{
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
     *     }
     *   }[],
     * }[]
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
                $entries[] = [
                    'slip_entry_id' => $slipEntry['slip_entry_id'],
                    'slip_entry' => $convertedSlipEntry,
                ];
            }
            $slips[] = [
                'slip_id' => $slipId,
                'slip' => $convertedSlip,
                'entries' => $entries,
            ];
        }

        return $slips;
    }
}
