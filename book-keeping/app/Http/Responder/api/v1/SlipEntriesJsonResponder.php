<?php

namespace App\Http\Responder\api\v1;

use App\Http\Responder\api\BaseJsonResponder;
use Illuminate\Http\JsonResponse;

class SlipEntriesJsonResponder extends BaseJsonResponder
{
    /**
     * Setup a new JSON response.
     *
     * @param  array{
     *   slips: array<string, array{
     *     date: string,
     *     slip_outline: string,
     *     slip_memo: string,
     *     items: array<string, array{
     *       debit: array{account_id: string, account_title: string},
     *       credit: array{account_id: string, account_title: string},
     *       amount: int,
     *       client: string,
     *       outline: string,
     *     }>
     *   }>|array{}
     * }  $context
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(array $context): JsonResponse
    {
        $this->response->setData($this->convertSlips($context['slips']));
        $this->response->setStatusCode(JsonResponse::HTTP_OK);

        return $this->response;
    }

    /**
     * Convert the slips to the slip entries to output JSON.
     *
     * @param  array<string, array{
     *   date: string,
     *   slip_outline: string,
     *   slip_memo: string,
     *   items: array<string, array{
     *     debit: array{account_id: string, account_title: string},
     *     credit: array{account_id: string, account_title: string},
     *     amount: int,
     *     client: string,
     *     outline: string,
     *   }>
     * }>|array{}  $slips
     * @return array{
     *   id: string,
     *   debit: array{id: string, title: string},
     *   credit: array{id: string, title: string},
     *   amount: int,
     *   client: string,
     *   outline: string,
     *   slip: array{
     *     id: string,
     *     date: string,
     *     outline: string,
     *     memo: string,
     *   }
     * }[]
     */
    private function convertSlips(array $slips): array
    {
        $slipEntries = [];

        foreach ($slips as $slipId => $slip) {
            foreach ($slip['items'] as $slipEntryId => $slipEntry) {
                $slipEntries[] = [
                    'id'      => $slipEntryId,
                    'debit'   => ['id' => $slipEntry['debit']['account_id'], 'title' => $slipEntry['debit']['account_title']],
                    'credit'  => ['id' => $slipEntry['credit']['account_id'], 'title' => $slipEntry['credit']['account_title']],
                    'amount'  => $slipEntry['amount'],
                    'client'  => $slipEntry['client'],
                    'outline' => $slipEntry['outline'],
                    'slip'    => [
                        'id'      => $slipId,
                        'date'    => $slip['date'],
                        'outline' => $slip['slip_outline'],
                        'memo'    => $slip['slip_memo'],
                    ],
                ];
            }
        }

        return $slipEntries;
    }
}
