<?php

namespace App\DataProvider;

interface SlipEntryRepositoryInterface
{
    /**
     * Create a new slip entry to be bound in the slip.
     *
     * @param  string  $slipId
     * @param  string  $debit
     * @param  string  $credit
     * @param  int  $amount
     * @param  string  $client
     * @param  string  $outline
     * @param  int|null  $displayOrder
     * @return string
     */
    public function create($slipId, $debit, $credit, $amount, $client, $outline, $displayOrder);

    /**
     * Create a new slip entry to import.
     *
     * @param  array{
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
     * }  $newSlipEntry
     * @return void
     */
    public function createForImporting(array $newSlipEntry);

    /**
     * Delete the slip entry.
     *
     * @param  string  $slipEntryId
     * @return void
     */
    public function delete($slipEntryId);

    /**
     * Find the slip entry.
     *
     * @param  string  $slipEntryId
     * @param  string  $bookId
     * @param  bool  $draftInclude
     * @return array<string, mixed>|null
     */
    public function findById($slipEntryId, $bookId, $draftInclude): ?array;

    /**
     * Search the book for slip entries between specified dates.
     *
     * @param  string  $bookId
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  array<string, mixed>  $condition
     * @return array<int, array<string, mixed>>
     */
    public function searchBook($bookId, $fromDate, $toDate, array $condition): array;

    /**
     * Search the book and calculate the sum of the slip entries between
     * specified dates for each account's debit and credit.
     *
     * @param  string  $bookId
     * @param  string  $fromDate
     * @param  string  $toDate
     * @return array<string, array<string, int>>
     */
    public function searchBookAndCalculateSum($bookId, $fromDate, $toDate): array;

    /**
     * Search the slip for its entries.
     *
     * @param  string  $slipId
     * @return array<int, array<string, mixed>>
     */
    public function searchSlip($slipId): array;

    /**
     * Search the slip for its entries to export.
     *
     * @param  string  $slipId
     * @param  string|null  $slipEntryId
     * @return array<int, array<string, mixed>>
     */
    public function searchSlipForExporting($slipId, $slipEntryId = null): array;

    /**
     * Update the slip entry.
     *
     * @param  string  $slipEntryId
     * @param  array<string, mixed>  $newData
     * @return void
     */
    public function update($slipEntryId, array $newData);

    /**
     * Update the slip entry to import.
     *
     * @param  array{
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
     * }  $newSlipEntry
     * @return void
     */
    public function updateForImporting(array $newSlipEntry);
}
