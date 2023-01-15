<?php

namespace App\DataProvider;

interface SlipEntryRepositoryInterface
{
    /**
     * Search the book and calculate the sum of the slip entries between
     * specified dates for each account's debit and credit.
     *
     * @param  string  $bookId
     * @param  string  $fromDate
     * @param  string  $toDate
     * @return array<string, array<string, int>>
     */
    public function searchBookAndCalculateSum(string $bookId, string $fromDate, string $toDate): array;

    /**
     * Create a slip entry to be bound in the slip.
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
    public function create(string $slipId, string $debit, string $credit, int $amount, string $client, string $outline, ?int $displayOrder): string;

    /**
     * Delete the slip entry.
     *
     * @param  string  $slipEntryId
     * @return void
     */
    public function delete(string $slipEntryId): void;

    /**
     * Search the slip for its entries.
     *
     * @param  string  $slipId
     * @return array<int, array<string, mixed>>
     */
    public function searchSlip(string $slipId): array;

    /**
     * Find the slip entry.
     *
     * @param  string  $slipEntryId
     * @param  string  $bookId
     * @param  bool  $draftInclude
     * @return array<string, mixed>|null
     */
    public function findById(string $slipEntryId, string $bookId, bool $draftInclude): ?array;

    /**
     * Search the book for the slip entries between specified date.
     *
     * @param  string  $bookId
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  array  $condition
     * @return array<int, array<string, mixed>>
     */
    public function searchBook(string $bookId, string $fromDate, string $toDate, array $condition): array;

    /**
     * Update the slip entry.
     *
     * @param  string  $slipEntryId
     * @param  array<string, mixed>  $newData
     * @return void
     */
    public function update(string $slipEntryId, array $newData): void;
}
