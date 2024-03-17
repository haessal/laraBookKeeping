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
    public function createForImporting(array $newSlip)
    {
        $slip = new Slip();
        $slip->slip_id = $newSlip['slip_id'];
        $slip->book_id = $newSlip['book_id'];
        $slip->slip_outline = $newSlip['slip_outline'];
        $slip->slip_memo = $newSlip['slip_memo'];
        $slip->date = $newSlip['date'];
        $slip->is_draft = $newSlip['is_draft'];
        $slip->display_order = $newSlip['display_order'];
        $slip->save();
        $slip->refresh();
        if ($newSlip['deleted']) {
            $slip->delete();
        }
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
     * Search the book for slips to export.
     *
     * @param  string  $bookId
     * @param  string|null  $slipId
     * @return array<int, array<string, mixed>>
     */
    public function searchBookForExporting($bookId, $slipId = null): array
    {
        /** @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query */
        $query = Slip::withTrashed()
            ->select('*')
            ->where('book_id', $bookId);
        if (isset($slipId)) {
            $query = $query->where('slip_id', $slipId);
        }
        /** @var array<int, array<string, mixed>> $list */
        $list = $query->get()->toArray();

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
    public function updateForImporting(array $newSlip)
    {
        /** @var \App\Models\Slip|null $slip */
        $slip = Slip::withTrashed()->find($newSlip['slip_id']);
        if (! is_null($slip)) {
            $slip->book_id = $newSlip['book_id'];
            $slip->slip_outline = $newSlip['slip_outline'];
            $slip->slip_memo = $newSlip['slip_memo'];
            $slip->date = $newSlip['date'];
            $slip->is_draft = $newSlip['is_draft'];
            $slip->display_order = $newSlip['display_order'];
            $slip->touch();
            $slip->save();
            $slip->refresh();
            if ($slip->trashed()) {
                if (! $newSlip['deleted']) {
                    $slip->restore();
                }
            } else {
                if ($newSlip['deleted']) {
                    $slip->delete();
                }
            }
        }
    }
}
