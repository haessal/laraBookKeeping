<?php

namespace App\Service;

use App\DataProvider\SlipEntryRepositoryInterface;
use App\DataProvider\SlipRepositoryInterface;

class SlipService
{
    /**
     * Slip repository instance.
     *
     * @var \App\DataProvider\SlipRepositoryInterface
     */
    private $slip;

    /**
     * Slip repository instance.
     *
     * @var \App\DataProvider\SlipEntryRepositoryInterface
     */
    private $slipEntry;

    /**
     * Create a new SlipService instance.
     *
     * @param  \App\DataProvider\SlipRepositoryInterface  $slip
     * @param  \App\DataProvider\SlipEntryRepositoryInterface  $slipEntry
     */
    public function __construct(SlipRepositoryInterface $slip, SlipEntryRepositoryInterface $slipEntry)
    {
        $this->slip = $slip;
        $this->slipEntry = $slipEntry;
    }

    /**
     * Create a new slip as draft.
     *
     * @param  string  $bookId
     * @param  string  $outline
     * @param  string  $date
     * @param  array{
     *   debit: string,
     *   credit: string,
     *   amount: int,
     *   client: string,
     *   outline: string,
     *   display_order?: string,
     * }[]  $entries
     * @param  string  $memo
     * @param  int  $displayOrder
     * @return string
     */
    public function createSlipAsDraft($bookId, $outline, $date, array $entries, $memo = null, $displayOrder = null)
    {
        $slipId = $this->slip->create($bookId, $outline, $date, $memo, $displayOrder, true);
        foreach ($entries as $entry) {
            $slipEntryDisplayOrder = null;
            if (array_key_exists('display_order', $entry)) {
                $slipEntryDisplayOrder = intval($entry['display_order']);
            }
            $this->slipEntry->create(
                $slipId,
                strval($entry['debit']),
                strval($entry['credit']),
                intval($entry['amount']),
                strval($entry['client']),
                strval($entry['outline']),
                $slipEntryDisplayOrder
            );
        }

        return $slipId;
    }

    /**
     * Create a new slip entry.
     *
     * @param  string  $slipId
     * @param  string  $debit
     * @param  string  $credit
     * @param  int  $amount
     * @param  string  $client
     * @param  string  $outline
     * @param  int  $displayOrder
     * @return string
     */
    public function createSlipEntry($slipId, $debit, $credit, $amount, $client, $outline, $displayOrder = null)
    {
        return $this->slipEntry->create($slipId, $debit, $credit, $amount, $client, $outline, $displayOrder);
    }

    /**
     * Delete the slip.
     *
     * @param  string  $slipId
     * @return void
     */
    public function deleteSlip($slipId)
    {
        $this->slip->delete($slipId);
    }

    /**
     * Delete the slip entry.
     *
     * @param  string  $slipEntryId
     * @return void
     */
    public function deleteSlipEntry($slipEntryId)
    {
        $this->slipEntry->delete($slipEntryId);
    }

    /**
     * Retrieve the amount flow of each account over the specified period.
     *
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  string  $bookId
     * @return array<string, array{debit: int, credit: int}>
     */
    public function retrieveAmountFlows($fromDate, $toDate, $bookId): array
    {
        $amountFlows = [];

        $list = $this->slipEntry->searchBookAndCalculateSum($bookId, $fromDate, $toDate);
        foreach ($list as $accountId => $amountFlow) {
            $amountFlows[$accountId] = [
                'debit' => $amountFlow['debit'],
                'credit' => $amountFlow['credit'],
            ];
        }

        return $amountFlows;
    }

    /**
     * Retrieve a list of draft slips.
     *
     * @param  string  $bookId
     * @return array{
     *   slip_id: string,
     *   date: string,
     *   slip_outline: string,
     *   slip_memo: string,
     * }[]
     */
    public function retrieveDraftSlips($bookId): array
    {
        $slips = [];

        $list = $this->slip->searchBookForDraft($bookId);
        foreach ($list as $slip) {
            $slips[] = [
                'slip_id' => strval($slip['slip_id']),
                'date' => strval($slip['date']),
                'slip_outline' => strval($slip['slip_outline']),
                'slip_memo' => strval($slip['slip_memo']),
            ];
        }

        return $slips;
    }

    /**
     * Retrieve the slip.
     *
     * @param  string  $slipId
     * @param  string  $bookId
     * @return array{
     *   book_id: string,
     *   slip_id: string,
     *   date: string,
     *   slip_outline: string,
     *   slip_memo: string,
     * }|null
     */
    public function retrieveSlip($slipId, $bookId): ?array
    {
        $slip = $this->slip->findById($slipId, $bookId);

        return is_null($slip) ? null : [
            'book_id' => strval($slip['book_id']),
            'slip_id' => strval($slip['slip_id']),
            'date' => strval($slip['date']),
            'slip_outline' => strval($slip['slip_outline']),
            'slip_memo' => strval($slip['slip_memo']),
        ];
    }

    /**
     * Retrieve a list of slip entries that match the condition.
     *
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  array{
     *   debit?: string|null,
     *   credit?: string|null,
     *   and_or?: string|null,
     *   keyword?: string|null,
     * }  $condition
     * @param  string  $bookId
     * @return array{
     *   slip_id: string,
     *   date: string,
     *   slip_outline: string,
     *   slip_memo: string,
     *   slip_entry_id: string,
     *   debit: string,
     *   credit: string,
     *   amount: int,
     *   client: string,
     *   outline: string,
     * }[]
     */
    public function retrieveSlipEntries($fromDate, $toDate, array $condition, $bookId): array
    {
        $slipEntries = [];

        $list = $this->slipEntry->searchBook($bookId, $fromDate, $toDate, $condition);
        foreach ($list as $slipEntry) {
            $slipEntries[] = [
                'slip_id' => strval($slipEntry['slip_id']),
                'date' => strval($slipEntry['date']),
                'slip_outline' => strval($slipEntry['slip_outline']),
                'slip_memo' => strval($slipEntry['slip_memo']),
                'slip_entry_id' => strval($slipEntry['slip_entry_id']),
                'debit' => strval($slipEntry['debit']),
                'credit' => strval($slipEntry['credit']),
                'amount' => intval($slipEntry['amount']),
                'client' => strval($slipEntry['client']),
                'outline' => strval($slipEntry['outline']),
            ];
        }

        return $slipEntries;
    }

    /**
     * Retrieve a list of slip entries that are bound to the slip.
     *
     * @param  string  $slipId
     * @return array{
     *   slip_entry_id: string,
     *   slip_id: string,
     *   debit: string,
     *   credit: string,
     *   amount: int,
     *   client: string,
     *   outline: string,
     * }[]
     */
    public function retrieveSlipEntriesBoundTo($slipId): array
    {
        $slipEntries = [];

        $list = $this->slipEntry->searchSlip($slipId);
        foreach ($list as $slipEntry) {
            $slipEntries[] = [
                'slip_entry_id' => strval($slipEntry['slip_entry_id']),
                'slip_id' => strval($slipEntry['slip_id']),
                'debit' => strval($slipEntry['debit']),
                'credit' => strval($slipEntry['credit']),
                'amount' => intval($slipEntry['amount']),
                'client' => strval($slipEntry['client']),
                'outline' => strval($slipEntry['outline']),
            ];
        }

        return $slipEntries;
    }

    /**
     * Retrieve the slip entry.
     *
     * @param  string  $slipEntryId
     * @param  string  $bookId
     * @param  bool  $draftInclude
     * @return array{
     *   slip_id: string,
     *   date: string,
     *   slip_outline: string,
     *   slip_memo: string,
     *   slip_entry_id: string,
     *   debit: string,
     *   credit: string,
     *   amount: int,
     *   client: string,
     *   outline: string,
     * }|null
     */
    public function retrieveSlipEntry($slipEntryId, $bookId, $draftInclude): ?array
    {
        $slipEntry = $this->slipEntry->findById($slipEntryId, $bookId, $draftInclude);

        return is_null($slipEntry) ? null : [
            'slip_id' => strval($slipEntry['slip_id']),
            'date' => strval($slipEntry['date']),
            'slip_outline' => strval($slipEntry['slip_outline']),
            'slip_memo' => strval($slipEntry['slip_memo']),
            'slip_entry_id' => strval($slipEntry['slip_entry_id']),
            'debit' => strval($slipEntry['debit']),
            'credit' => strval($slipEntry['credit']),
            'amount' => intval($slipEntry['amount']),
            'client' => strval($slipEntry['client']),
            'outline' => strval($slipEntry['outline']),
        ];
    }

    /**
     * Submit the slip.
     *
     * @param  string  $slipId
     * @return void
     */
    public function submitSlip($slipId)
    {
        $this->slip->updateDraftMark($slipId, false);
    }

    /**
     * Update the date of the slip.
     *
     * @param  string  $slipId
     * @param  string  $date
     * @return void
     */
    public function updateDateOf($slipId, $date)
    {
        $this->slip->update($slipId, ['date' => $date]);
    }

    /**
     * Update the slip.
     *
     * @param  string  $slipId
     * @param  array{
     *   outline?: string,
     *   memo?: string,
     *   date?: string,
     * }  $newData
     * @return void
     */
    public function updateSlip($slipId, array $newData)
    {
        $this->slip->update($slipId, $newData);
    }

    /**
     * Update the slip entry.
     *
     * @param  string  $slipEntryId
     * @param  array{
     *   debit?: string,
     *   credit?: string,
     *   amount?: int,
     *   client?: string,
     *   outline?: string,
     * }  $newData
     * @return void
     */
    public function updateSlipEntry($slipEntryId, array $newData)
    {
        $this->slipEntry->update($slipEntryId, $newData);
    }
}
