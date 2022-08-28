<?php

namespace App\Http\Responder\api\v1;

use App\Http\Responder\api\BaseJsonResponder;
use Illuminate\Http\JsonResponse;

class SlipJsonResponder extends BaseJsonResponder
{
    /**
     * Respond with the Accounts JSON.
     *
     * @param array $context
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(array $context, int $status = JsonResponse::HTTP_OK): JsonResponse
    {
        $this->response->setData($this->translateSlipFormat($context['slips']));
        $this->response->setStatusCode($status);

        return $this->response;
    }

    /**
     * Translate slip format for JSON.
     *
     * @param array $slips
     *
     * @return array
     */
    private function translateSlipFormat(array $slips): array
    {
        $slipId = key($slips);

        $slip = [
            'id'      => $slipId,
            'date'    => $slips[$slipId]['date'],
            'outline' => $slips[$slipId]['slip_outline'],
            'memo'    => $slips[$slipId]['slip_memo'],
            'entries' => [],
        ];
        foreach ($slips[$slipId]['items'] as $slipEntryId => $slipEntryItem) {
            $slip['entries'][] = [
                'id'      => $slipEntryId,
                'debit'   => ['id' => $slipEntryItem['debit']['account_id'], 'title' => $slipEntryItem['debit']['account_title']],
                'credit'  => ['id' => $slipEntryItem['credit']['account_id'], 'title' => $slipEntryItem['credit']['account_title']],
                'amount'  => $slipEntryItem['amount'],
                'client'  => $slipEntryItem['client'],
                'outline' => $slipEntryItem['outline'],
            ];
        }

        return $slip;
    }
}
