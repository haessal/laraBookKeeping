<?php

namespace App\Service;

use Illuminate\Support\Facades\DB;

class OldBookService
{
    /**
     * Retrieve account of old DB version.
     *
     * @param int $userId
     *
     * @return array
     */
    public function retrieveAccounts(int $oldId): array
    {
        $account = DB::table('bk_acounts')->select()
            ->where('uid', $oldId)
            ->orderBy('code')
            ->get()->toArray();

        return $account;
    }

    /**
     * Retrieve budget of old DB version.
     *
     * @param int $userId
     *
     * @return array
     */
    public function retrieveBudgets(int $oldId): array
    {
        $budget = DB::table('bk_budget')->select()
            ->where('uid', $oldId)
            ->get()->toArray();

        return $budget;
    }

    /**
     * Retrieve Slip No list in jarnal of old DB version.
     *
     * @param int $userId
     *
     * @return array
     */
    public function retrieveSlipNoList(int $oldId): array
    {
        $slipnolist = DB::table('bk_jarnal')->select('slipno')
            ->where('uid', $oldId)
            ->where('cancel', 0)
            ->groupBy('slipno')
            ->get()->toArray();

        return $slipnolist;
    }

    /**
     * Retrieve Slip in jarnal of old DB version.
     *
     * @param int $userId
     * @param int $oldSlipNo
     *
     * @return array
     */
    public function retrieveSlip(int $oldId, int $oldSlipNo): array
    {
        $slip = DB::table('bk_jarnal')->select()
            ->where('uid', $oldId)
            ->where('cancel', 0)
            ->where('slipno', $oldSlipNo)
            ->get()->toArray();

        return $slip;
    }
}
