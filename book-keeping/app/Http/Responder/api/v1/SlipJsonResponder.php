<?php

namespace App\Http\Responder\api\v1;

use App\Http\Responder\api\BaseJsonResponder;
use Illuminate\Http\JsonResponse;

class SlipJsonResponder extends BaseJsonResponder
{
    /**
     * Setup a new JSON response.
     *
     * @param  array{
     *   slip_id: string,
     *   slip: array{
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
     *   }
     * }  $context
     * @param  int  $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(array $context, int $status = JsonResponse::HTTP_OK): JsonResponse
    {
        $this->response->setData($this->convert($context['slip_id'], $context['slip']));
        $this->response->setStatusCode($status);

        return $this->response;
    }

    /**
     * Convert the array to output JSON.
     *
     * @param  string  $slipId
     * @param  array{
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
     * }  $slip
     * @return array{
     *   id: string,
     *   date: string,
     *   outline: string,
     *   memo: string,
     *   entries: array{
     *     id: string,
     *     debit: array{id: string, title: string},
     *     credit: array{id: string, title: string},
     *     amount: int,
     *     client: string,
     *     outline: string,
     *   }[]
     * }
     */
    private function convert(string $slipId, array $slip): array
    {
        $slipItem = [
            'id' => $slipId,
            'date' => $slip['date'],
            'outline' => $slip['slip_outline'],
            'memo' => $slip['slip_memo'],
            'entries' => [],
        ];
        foreach ($slip['items'] as $slipEntryId => $slipEntryItem) {
            $slipItem['entries'][] = [
                'id' => $slipEntryId,
                'debit' => ['id' => $slipEntryItem['debit']['account_id'], 'title' => $slipEntryItem['debit']['account_title']],
                'credit' => ['id' => $slipEntryItem['credit']['account_id'], 'title' => $slipEntryItem['credit']['account_title']],
                'amount' => $slipEntryItem['amount'],
                'client' => $slipEntryItem['client'],
                'outline' => $slipEntryItem['outline'],
            ];
        }

        return $slipItem;
    }
}
