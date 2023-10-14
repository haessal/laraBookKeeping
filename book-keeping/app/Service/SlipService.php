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
     * @param \App\DataProvider\SlipRepositoryInterface      $slip
     * @param \App\DataProvider\SlipEntryRepositoryInterface $slipEntry
     */
    public function __construct(SlipRepositoryInterface $slip, SlipEntryRepositoryInterface $slipEntry)
    {
        $this->slip = $slip;
        $this->slipEntry = $slipEntry;
    }

    /**
     * Create new Slip as draft.
     *
     * @param string $bookId
     * @param string $outline
     * @param string $date
     * @param array  $entries
     * @param string $memo
     * @param string $displayOrder
     *
     * @return string $slipId
     */
    public function createSlipAsDraft(string $bookId, string $outline, string $date, array $entries, string $memo = null, int $displayOrder = null): string
    {
        $slipId = $this->slip->create($bookId, $outline, $date, $memo, $displayOrder, true);
        foreach ($entries as &$entry) {
            $slipEntryDisplayOrder = null;
            if (array_key_exists('display_order', $entry)) {
                $slipEntryDisplayOrder = $entry['display_order'];
            }
            $this->slipEntry->create($slipId, $entry['debit'], $entry['credit'], $entry['amount'], $entry['client'], $entry['outline'], $slipEntryDisplayOrder);
        }

        return $slipId;
    }

    /**
     * Create a new slip entry.
     *
     * @param string $slipId
     * @param string $debit
     * @param string $credit
     * @param int    $amount
     * @param string $client
     * @param string $outline
     * @param int    $displayOrder
     *
     * @return string $slipEntryId
     */
    public function createSlipEntry(string $slipId, string $debit, string $credit, int $amount, string $client, string $outline, int $displayOrder = null): string
    {
        return $this->slipEntry->create($slipId, $debit, $credit, $amount, $client, $outline, $displayOrder);
    }

    /**
     * Delete the specified slip.
     *
     * @param string $slipId
     *
     * @return void
     */
    public function deleteSlip(string $slipId)
    {
        $this->slip->delete($slipId);
    }

    /**
     * Delete the specified slip entry.
     *
     * @param string $slipEntryId
     *
     * @return void
     */
    public function deleteSlipEntry(string $slipEntryId)
    {
        $this->slipEntry->delete($slipEntryId);
    }

    /**
     * Retrieve the amount flow of each account between specified data.
     *
     * @param string $fromDate
     * @param string $toDate
     * @param string $bookId
     *
     * @return array
     */
    public function retrieveAmountFlows(string $fromDate, string $toDate, string $bookId): array
    {
        return $this->slipEntry->calculateSum($fromDate, $toDate, $bookId);
    }

    /**
     * Retrieve draft slips.
     *
     * @param string $bookId
     *
     * @return array
     */
    public function retrieveDraftSlips(string $bookId): array
    {
        return $this->slip->findAllDraftByBookId($bookId);
    }

    /**
     * Retrieve a slip.
     *
     * @param string $slipId
     * @param string $bookId
     *
     * @return array|null
     */
    public function retrieveSlip(string $slipId, string $bookId): ?array
    {
        return $this->slip->findById($slipId, $bookId);
    }

    /**
     * Retrieve a slip.
     *
     * @param string $slipId
     * @param string $bookId
     *
     * @return array|null
     */
    public function exportSlips(string $bookId): ?array
    {
        $slips = [];
        $slipList = $this->slip->searchForExport($bookId);

        foreach ($slipList as $slip) {
            $slipId = $slip['slip_id'];
            $slips[$slipId] = $slip;

            $entries = [];
            $slipEntryList = $this->slipEntry->searchSlipEntriesForExport($slipId);
            foreach ($slipEntryList as $slipEntry) {
                $entries[$slipEntry['slip_entry_id']] = $slipEntry;
            }

            $slips[$slipId]['entries'] = $entries;

        }
        return $slips;
    }

    /**
     * Retrieve slip entries between specified date.
     *
     * @param string $fromDate
     * @param string $toDate
     * @param array  $condition
     * @param string $bookId
     *
     * @return array
     */
    public function retrieveSlipEntries(string $fromDate, string $toDate, array $condition, string $bookId): array
    {
        return $this->slipEntry->searchSlipEntries($fromDate, $toDate, $condition, $bookId);
    }

    /**
     * Retrieve slip entries bound to specify slip.
     *
     * @param string $slipId
     *
     * @return array
     */
    public function retrieveSlipEntriesBoundTo(string $slipId): array
    {
        return $this->slipEntry->findAllBySlipId($slipId);
    }

    /**
     * Retrieve slip that bound specify slip entry.
     *
     * @param string $slipEntryId
     * @param string $bookId
     * @param bool   $draftInclude
     *
     * @return array | null
     */
    public function retrieveSlipThatBound(string $slipEntryId, string $bookId, bool $draftInclude): ?array
    {
        return $this->slipEntry->findById($slipEntryId, $bookId, $draftInclude);
    }

    /**
     * Submit the slip.
     *
     * @param string $slipId
     */
    public function submitSlip(string $slipId)
    {
        $this->slip->updateIsDraft($slipId, false);
    }

    /**
     * Update the slip date.
     *
     * @param string $slipId
     * @param string $date
     */
    public function updateDate(string $slipId, string $date)
    {
        $this->slip->update($slipId, ['date' => $date]);
    }

    /**
     * Update the slip.
     *
     * @param string $slipId
     * @param array  $newData
     */
    public function updateSlip(string $slipId, array $newData)
    {
        $this->slip->update($slipId, $newData);
    }

    /**
     * Update the slip entry.
     *
     * @param string $slipEntryId
     * @param array  $newData
     */
    public function updateSlipEntry(string $slipEntryId, array $newData)
    {
        $this->slipEntry->update($slipEntryId, $newData);
    }
}
