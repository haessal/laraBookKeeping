<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\SlipEntryRepositoryInterface;

class SlipEntryRepository implements SlipEntryRepositoryInterface
{
    /**
     * Create new slip entry.
     *
     * @param string $slipId
     * @param string $debit
     * @param string $credit
     * @param int    $amount
     * @param string $client
     * @param string $outline
     *
     * @return string $slipEntryId
     */
    public function create(string $slipId, string $debit, string $credit, int $amount, string $client, string $outline) : string
    {
        $slipEntry = new SlipEntry();
        $slipEntry->slip_bound_on = $slipId;
        $slipEntry->debit = $debit;
        $slipEntry->credit = $credit;
        $slipEntry->amount = $amount;
        $slipEntry->client = $client;
        $slipEntry->outline = $outline;
        $slipEntry->save();

        return $slipEntry->slip_entry_id;
    }

    public function calculateSum(string $fromDate, string $toDate, string $bookId) : array
    {
        $debitSumList = $this->getSlipEntriesQuery($fromDate, $toDate, $bookId)
            ->groupBy('debit')
            ->selectRaw('debit, sum(amount) as debitsum')
            ->get()->toArray();
        $creditSumList = $this->getSlipEntriesQuery($fromDate, $toDate, $bookId)
            ->groupBy('credit')
            ->selectRaw('credit, sum(amount) as creditsum')
            ->get()->toArray();
        $list = [];
        foreach ($debitSumList as $debit) {
            $accountId = $debit['debit'];
            $list[$accountId]['debit'] = intval($debit['debitsum']);
            if (!array_key_exists('credit', $list[$accountId])) {
                $list[$accountId]['credit'] = 0;
            }
        }
        foreach ($creditSumList as $credit) {
            $accountId = $credit['credit'];
            $list[$accountId]['credit'] = intval($credit['creditsum']);
            if (!array_key_exists('debit', $list[$accountId])) {
                $list[$accountId]['debit'] = 0;
            }
        }

        return $list;
    }

    private function getSlipEntriesQuery(string $fromDate, string $toDate, string $bookId)
    {
        return SlipEntry::join('bk2_0_slips', 'bk2_0_slips.slip_id', '=', 'bk2_0_slip_entries.slip_bound_on')
            ->where('book_bound_on', $bookId)
            ->whereNull('bk2_0_slips.deleted_at')
            ->whereNull('bk2_0_slip_entries.deleted_at')
            ->whereBetween('date', [$fromDate, $toDate]);
    }
}
