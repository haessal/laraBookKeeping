<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\SlipEntryRepositoryInterface;
use App\Models\SlipEntry;
use Illuminate\Database\Eloquent\Builder;

class SlipEntryRepository implements SlipEntryRepositoryInterface
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
    public function create($slipId, $debit, $credit, $amount, $client, $outline, $displayOrder)
    {
        $slipEntry = new SlipEntry();
        $slipEntry->slip_id = $slipId;
        $slipEntry->debit = $debit;
        $slipEntry->credit = $credit;
        $slipEntry->amount = $amount;
        $slipEntry->client = $client;
        $slipEntry->outline = $outline;
        $slipEntry->display_order = $displayOrder;
        $slipEntry->save();

        return $slipEntry->slip_entry_id;
    }

    /**
     * Delete the slip entry.
     *
     * @param  string  $slipEntryId
     * @return void
     */
    public function delete($slipEntryId)
    {
        /** @var \App\Models\SlipEntry|null $slipEntry */
        $slipEntry = SlipEntry::query()->find($slipEntryId);
        if (! is_null($slipEntry)) {
            $slipEntry->delete();
        }
    }

    /**
     * Find the slip entry.
     *
     * @param  string  $slipEntryId
     * @param  string  $bookId
     * @param  bool  $draftInclude
     * @return array<string, mixed>|null
     */
    public function findById($slipEntryId, $bookId, $draftInclude): ?array
    {
        $query = SlipEntry::query()
            ->join('bk2_0_slips', 'bk2_0_slips.slip_id', '=', 'bk2_0_slip_entries.slip_id')
            ->select(
                'bk2_0_slips.slip_id',
                'date',
                'slip_outline',
                'slip_memo',
                'slip_entry_id',
                'debit',
                'credit',
                'amount',
                'client',
                'outline'
            )
            ->where('slip_entry_id', $slipEntryId)
            ->where('book_id', $bookId)
            ->whereNull('bk2_0_slips.deleted_at')
            ->whereNull('bk2_0_slip_entries.deleted_at');
        if (! $draftInclude) {
            $query = $query->where('is_draft', false);
        }
        /** @var \Illuminate\Database\Eloquent\Model|null $slipEntry */
        $slipEntry = $query->first();

        return is_null($slipEntry) ? null : $slipEntry->toArray();
    }

    /**
     * Search the book for slip entries between specified dates.
     *
     * @param  string  $bookId
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  array<string, mixed>  $condition
     * @return array<int, array<string, mixed>>
     */
    public function searchBook($bookId, $fromDate, $toDate, array $condition): array
    {
        /** @var array<int, array<string, mixed>> $list */
        $list = $this->getSlipEntriesQuery($bookId, $fromDate, $toDate, $condition)
            ->select(
                'bk2_0_slips.slip_id',
                'date',
                'slip_outline',
                'slip_memo',
                'slip_entry_id',
                'debit',
                'credit',
                'amount',
                'client',
                'outline'
            )
            ->orderBy('date')
            ->orderBy('bk2_0_slip_entries.created_at')
            ->orderBy('bk2_0_slips.display_order')
            ->orderBy('bk2_0_slip_entries.display_order')
            ->get()->toArray();

        return $list;
    }

    /**
     * Search the book and calculate the sum of the slip entries between
     * specified dates for each account's debit and credit.
     *
     * @param  string  $bookId
     * @param  string  $fromDate
     * @param  string  $toDate
     * @return array<string, array<string, int>>
     */
    public function searchBookAndCalculateSum($bookId, $fromDate, $toDate): array
    {
        /** @var array<int, array<string, mixed>> $debitSumList */
        $debitSumList = $this->getSlipEntriesQuery($bookId, $fromDate, $toDate, [])
            ->groupBy('debit')
            ->selectRaw('debit, sum(amount) as debitsum')
            ->get()->toArray();
        /** @var array<int, array<string, mixed>> $creditSumList */
        $creditSumList = $this->getSlipEntriesQuery($bookId, $fromDate, $toDate, [])
            ->groupBy('credit')
            ->selectRaw('credit, sum(amount) as creditsum')
            ->get()->toArray();
        /** @var array<string, array<string, int>> $list */
        $list = [];
        foreach ($debitSumList as $debit) {
            $accountId = strval($debit['debit']);
            $list[$accountId]['debit'] = intval($debit['debitsum']);
            if (! array_key_exists('credit', $list[$accountId])) {
                $list[$accountId]['credit'] = 0;
            }
        }
        foreach ($creditSumList as $credit) {
            $accountId = strval($credit['credit']);
            $list[$accountId]['credit'] = intval($credit['creditsum']);
            if (! array_key_exists('debit', $list[$accountId])) {
                $list[$accountId]['debit'] = 0;
            }
        }

        return $list;
    }

    /**
     * Search the slip for its entries.
     *
     * @param  string  $slipId
     * @return array<int, array<string, mixed>>
     */
    public function searchSlip($slipId): array
    {
        /** @var array<int, array<string, mixed>> $list */
        $list = SlipEntry::query()
            ->select('slip_entry_id', 'slip_id', 'debit', 'credit', 'amount', 'client', 'outline')
            ->where('slip_id', $slipId)
            ->orderBy('created_at')
            ->orderBy('display_order')
            ->get()->toArray();

        return $list;
    }

    /**
     * Search the slip for its entries to export.
     *
     * @param  string  $slipId
     * @param  string|null  $slipEntryId
     * @return array<int, array<string, mixed>>
     */
    public function searchSlipForExporting($slipId, $slipEntryId = null): array
    {
        /** @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query */
        $query = SlipEntry::withTrashed()
            ->select('*')
            ->where('slip_id', $slipId);
        if (isset($slipEntryId)) {
            $query = $query->where('slip_entry_id', $slipEntryId);
        }
        /** @var array<int, array<string, mixed>> $list */
        $list = $query->get()->toArray();

        return $list;
    }

    /**
     * Update the slip entry.
     *
     * @param  string  $slipEntryId
     * @param  array<string, mixed>  $newData
     * @return void
     */
    public function update($slipEntryId, array $newData)
    {
        /** @var \App\Models\SlipEntry|null $slipEntry */
        $slipEntry = SlipEntry::query()->find($slipEntryId);
        if (! is_null($slipEntry)) {
            if (array_key_exists('debit', $newData)) {
                $slipEntry->debit = strval($newData['debit']);
            }
            if (array_key_exists('credit', $newData)) {
                $slipEntry->credit = strval($newData['credit']);
            }
            if (array_key_exists('amount', $newData)) {
                $slipEntry->amount = intval($newData['amount']);
            }
            if (array_key_exists('client', $newData)) {
                $slipEntry->client = strval($newData['client']);
            }
            if (array_key_exists('outline', $newData)) {
                $slipEntry->outline = strval($newData['outline']);
            }
            $slipEntry->save();
        }
    }

    /**
     * Query to get slip entries between specified dates.
     *
     * @param  string  $bookId
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  array<string, mixed>  $condition
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function getSlipEntriesQuery($bookId, $fromDate, $toDate, array $condition): Builder
    {
        $debit = array_key_exists('debit', $condition) ? $condition['debit'] : null;
        $credit = array_key_exists('credit', $condition) ? $condition['credit'] : null;
        $and_or = array_key_exists('and_or', $condition) ? $condition['and_or'] : null;
        /** @var string|null $keyword */
        $keyword = array_key_exists('keyword', $condition) ? $condition['keyword'] : null;

        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = SlipEntry::query()
            ->join('bk2_0_slips', 'bk2_0_slips.slip_id', '=', 'bk2_0_slip_entries.slip_id')
            ->where('book_id', $bookId)
            ->where('is_draft', false)
            ->whereNull('bk2_0_slips.deleted_at')
            ->whereNull('bk2_0_slip_entries.deleted_at')
            ->whereBetween('date', [$fromDate, $toDate]);
        if (! empty($debit) && empty($credit)) {
            $query = $query->where('debit', $debit);
        }
        if (empty($debit) && ! empty($credit)) {
            $query = $query->where('credit', $credit);
        }
        if (! empty($debit) && ! empty($credit) && ! empty($and_or)) {
            $sub_account = ['debit' => $debit, 'credit' => $credit];
            if ($and_or == 'and') {
                $query = $query
                    ->where(function ($subquery) use ($sub_account) {
                        $subquery->where('debit', $sub_account['debit'])->where('credit', $sub_account['credit']);
                    });
            }
            if ($and_or == 'or') {
                $query = $query
                    ->where(function ($subquery) use ($sub_account) {
                        $subquery->where('debit', $sub_account['debit'])->orWhere('credit', $sub_account['credit']);
                    });
            }
        }
        if (! empty($keyword)) {
            $query = $query
                ->where(function ($subquery) use ($keyword) {
                    $subquery->where('client', 'like binary', "%$keyword%")
                             ->orWhere('outline', 'like binary', "%$keyword%");
                });
        }

        return $query;
    }
}
