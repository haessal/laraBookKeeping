<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\SlipRepositoryInterface;
use App\Models\Slip;

class SlipRepository implements SlipRepositoryInterface
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
    public function create($bookId, $outline, $date, $memo, $displayOrder, $isDraft)
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
    public function delete($slipId)
    {
        /** @var \App\Models\Slip|null $slip */
        $slip = Slip::query()->find($slipId);
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
    public function findById($slipId, $bookId): ?array
    {
        /** @var \Illuminate\Database\Eloquent\Model|null $slip */
        $slip = Slip::query()
            ->select('book_id', 'slip_id', 'date', 'slip_outline', 'slip_memo')
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
    public function searchBookForDraft($bookId): array
    {
        /** @var array<int, array<string, string>> $list */
        $list = Slip::query()
            ->select('slip_id', 'date', 'slip_outline', 'slip_memo')
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
    public function update($slipId, array $newData)
    {
        /** @var \App\Models\Slip|null $slip */
        $slip = Slip::query()->find($slipId);
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
    public function updateDraftMark($slipId, $isDraft)
    {
        /** @var \App\Models\Slip|null $slip */
        $slip = Slip::query()->find($slipId);
        if (! is_null($slip)) {
            $slip->is_draft = $isDraft;
            $slip->save();
        }
    }
}
