<?php

namespace App\DataProvider;

interface SlipRepositoryInterface
{
    /**
     * Create a slip to be bound in the book.
     *
     * @param  string  $bookId
     * @param  string  $outline
     * @param  string  $date
     * @param  string|null  $memo
     * @param  int|null  $displayOrder
     * @param  bool  $isDraft
     * @return string
     */
    public function create(string $bookId, string $outline, string $date, ?string $memo, ?int $displayOrder, bool $isDraft): string;

    /**
     * Delete the slip.
     *
     * @param  string  $slipId
     * @return void
     */
    public function delete(string $slipId): void;

    /**
     * Find the slip.
     *
     * @param  string  $slipId
     * @param  string  $bookId
     * @return array<string, string>|null
     */
    public function findById(string $slipId, string $bookId): ?array;

    /**
     * Search the book for draft slips.
     *
     * @param  string  $bookId
     * @return array<int, array<string, string>>
     */
    public function searchBookForDraft(string $bookId): array;

    /**
     * Update the slip.
     *
     * @param  string  $slipId
     * @param  array<string, string>  $newData
     * @return void
     */
    public function update(string $slipId, array $newData): void;

    /**
     * Update the mark for indicating that the slip is a draft.
     *
     * @param  string  $slipId
     * @param  bool  $isDraft
     * @return void
     */
    public function updateDraftMark(string $slipId, bool $isDraft): void;
}
