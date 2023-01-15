<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\SlipEntryRepositoryInterface;
use App\Models\SlipEntry;

class SlipEntryRepository implements SlipEntryRepositoryInterface
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
    public function searchBookAndCalculateSum(string $bookId, string $fromDate, string $toDate): array
    {
        $debitSumList = $this->getSlipEntriesQuery($fromDate, $toDate, [], $bookId)
            ->groupBy('debit')
            ->selectRaw('debit, sum(amount) as debitsum')
            ->get()->toArray();
        $creditSumList = $this->getSlipEntriesQuery($fromDate, $toDate, [], $bookId)
            ->groupBy('credit')
            ->selectRaw('credit, sum(amount) as creditsum')
            ->get()->toArray();
        $list = [];
        foreach ($debitSumList as $debit) {
            $accountId = $debit['debit'];
            $list[$accountId]['debit'] = intval($debit['debitsum']);
            if (! array_key_exists('credit', $list[$accountId])) {
                $list[$accountId]['credit'] = 0;
            }
        }
        foreach ($creditSumList as $credit) {
            $accountId = $credit['credit'];
            $list[$accountId]['credit'] = intval($credit['creditsum']);
            if (! array_key_exists('debit', $list[$accountId])) {
                $list[$accountId]['debit'] = 0;
            }
        }

        return $list;
    }

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
    public function create(string $slipId, string $debit, string $credit, int $amount, string $client, string $outline, ?int $displayOrder): string
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
    public function delete(string $slipEntryId): void
    {
        $slipEntry = SlipEntry::find($slipEntryId);
        if (! is_null($slipEntry)) {
            $slipEntry->delete();
        }
    }

    /**
     * Search the slip for its entries.
     *
     * @param  string  $slipId
     * @return array<int, array<string, mixed>>
     */
    public function searchSlip(string $slipId): array
    {
        $list = SlipEntry::select('slip_entry_id', 'slip_id', 'debit', 'credit', 'amount', 'client', 'outline')
            ->where('slip_id', $slipId)
            ->orderBy('created_at')
            ->orderBy('display_order')
            ->get()->toArray();

        return $list;
    }

    /**
     * Find the slip entry.
     *
     * @param  string  $slipEntryId
     * @param  string  $bookId
     * @param  bool  $draftInclude
     * @return array<string, mixed>|null
     */
    public function findById(string $slipEntryId, string $bookId, bool $draftInclude): ?array
    {
        $query = SlipEntry::join('bk2_0_slips', 'bk2_0_slips.slip_id', '=', 'bk2_0_slip_entries.slip_id')
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
        $slipEntry = $query->first();

        return is_null($slipEntry) ? null : $slipEntry->toArray();
    }

    /**
     * Search the book for the slip entries between specified date.
     *
     * @param  string  $bookId
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  array  $condition
     * @return array<int, array<string, mixed>>
     */
    public function searchBook(string $bookId, string $fromDate, string $toDate, array $condition): array
    {
        $list = $this->getSlipEntriesQuery($fromDate, $toDate, $condition, $bookId)
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
     * Update the slip entry.
     *
     * @param  string  $slipEntryId
     * @param  array<string, mixed>  $newData
     * @return void
     */
    public function update(string $slipEntryId, array $newData): void
    {
        $slipEntry = SlipEntry::find($slipEntryId);
        if (! is_null($slipEntry)) {
            if (array_key_exists('debit', $newData)) {
                $slipEntry->debit = $newData['debit'];
            }
            if (array_key_exists('credit', $newData)) {
                $slipEntry->credit = $newData['credit'];
            }
            if (array_key_exists('amount', $newData)) {
                $slipEntry->amount = $newData['amount'];
            }
            if (array_key_exists('client', $newData)) {
                $slipEntry->client = $newData['client'];
            }
            if (array_key_exists('outline', $newData)) {
                $slipEntry->outline = $newData['outline'];
            }
            $slipEntry->save();
        }
    }

    /**
     * Query to get slip entries between specified date.
     *
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  array  $condition
     * @param  string  $bookId
     * @return \Illuminate\Database\Query\Builder
     */
    private function getSlipEntriesQuery(string $fromDate, string $toDate, array $condition, string $bookId)
    {
        $debit = array_key_exists('debit', $condition) ? $condition['debit'] : null;
        $credit = array_key_exists('credit', $condition) ? $condition['credit'] : null;
        $and_or = array_key_exists('and_or', $condition) ? $condition['and_or'] : null;
        $keyword = array_key_exists('keyword', $condition) ? $condition['keyword'] : null;

        $query = SlipEntry::join('bk2_0_slips', 'bk2_0_slips.slip_id', '=', 'bk2_0_slip_entries.slip_id')
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
