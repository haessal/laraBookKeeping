<?php

namespace App\DataProvider;

interface SlipRepositoryInterface
{
    /**
     * Create a new slip to be bound in the book.
     *
     * @param  string  $bookId
     * @param  string  $outline
     * @param  string  $date
     * @param  string|null  $memo
     * @param  int|null  $displayOrder
     * @param  bool  $isDraft
     * @return string
     */
    public function create($bookId, $outline, $date, $memo, $displayOrder, $isDraft);

    /**
     * Create a new slip to import.
     *
     * @param  array{
     *   slip_id: string,
     *   book_id: string,
     *   slip_outline: string,
     *   slip_memo: string|null,
     *   date: string,
     *   is_draft: bool,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }  $newSlip
     * @return void
     */
    public function createForImporting(array $newSlip);

    /**
     * Delete the slip.
     *
     * @param  string  $slipId
     * @return void
     */
    public function delete($slipId);

    /**
     * Find the slip.
     *
     * @param  string  $slipId
     * @param  string  $bookId
     * @return array<string, string>|null
     */
    public function findById($slipId, $bookId): ?array;

    /**
     * Search the book for draft slips.
     *
     * @param  string  $bookId
     * @return array<int, array<string, string>>
     */
    public function searchBookForDraft($bookId): array;

    /**
     * Search the book for slips to export.
     *
     * @param  string  $bookId
     * @param  string|null  $slipId
     * @return array<int, array<string, mixed>>
     */
    public function searchBookForExporting($bookId, $slipId = null): array;

    /**
     * Update the slip.
     *
     * @param  string  $slipId
     * @param  array<string, string>  $newData
     * @return void
     */
    public function update($slipId, array $newData);

    /**
     * Update the mark for indicating that the slip is a draft.
     *
     * @param  string  $slipId
     * @param  bool  $isDraft
     * @return void
     */
    public function updateDraftMark($slipId, $isDraft);

    /**
     * Update the slip to import.
     *
     * @param  array{
     *   slip_id: string,
     *   book_id: string,
     *   slip_outline: string,
     *   slip_memo: string|null,
     *   date: string,
     *   is_draft: bool,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }  $newSlip
     * @return void
     */
    public function updateForImporting(array $newSlip);
}
