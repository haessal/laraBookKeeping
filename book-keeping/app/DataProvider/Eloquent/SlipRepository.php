<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\SlipRepositoryInterface;
use App\Models\Slip;

class SlipRepository implements SlipRepositoryInterface
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
    public function create(string $bookId, string $outline, string $date, ?string $memo, ?int $displayOrder, bool $isDraft): string
    {
        $slip = new Slip();
        $slip->book_id = $bookId;
        $slip->slip_outline = $outline;
        $slip->slip_memo = $memo;
        $slip->date = $date;
        $slip->display_order = $displayOrder;
        $slip->is_draft = $isDraft;
        $slip->save();

        return $slip->slip_id;
    }

    /**
     * Delete the slip.
     *
     * @param  string  $slipId
     * @return void
     */
    public function delete(string $slipId): void
    {
        $slip = Slip::find($slipId);
        if (! is_null($slip)) {
            $slip->delete();
        }
    }

    /**
     * Find the slip.
     *
     * @param  string  $slipId
     * @param  string  $bookId
     * @return array<string, string>|null
     */
    public function findById(string $slipId, string $bookId): ?array
    {
        $slip = Slip::select('book_id', 'slip_id', 'date', 'slip_outline', 'slip_memo')
            ->where('book_id', $bookId)
            ->where('slip_id', $slipId)
            ->where('is_draft', false)
            ->first();

        return is_null($slip) ? null : $slip->toArray();
    }

    /**
     * Search the book for draft slips.
     *
     * @param  string  $bookId
     * @return array<int, array<string, string>>
     */
    public function searchBookForDraft(string $bookId): array
    {
        $list = Slip::select('slip_id', 'date', 'slip_outline', 'slip_memo')
            ->where('book_id', $bookId)
            ->where('is_draft', true)
            ->get()->toArray();

        return $list;
    }

    /**
     * Update the slip.
     *
     * @param  string  $slipId
     * @param  array<string, string>  $newData
     * @return void
     */
    public function update(string $slipId, array $newData): void
    {
        $slip = Slip::find($slipId);
        if (! is_null($slip)) {
            if (array_key_exists('outline', $newData)) {
                $slip->slip_outline = $newData['outline'];
            }
            if (array_key_exists('memo', $newData)) {
                $slip->slip_memo = $newData['memo'];
            }
            if (array_key_exists('date', $newData)) {
                $slip->date = $newData['date'];
            }
            $slip->save();
        }
    }

    /**
     * Update the mark for indicating that the slip is a draft.
     *
     * @param  string  $slipId
     * @param  bool  $isDraft
     * @return void
     */
    public function updateDraftMark(string $slipId, bool $isDraft): void
    {
        $slip = Slip::find($slipId);
        $slip->is_draft = $isDraft;
        $slip->save();
    }
}
