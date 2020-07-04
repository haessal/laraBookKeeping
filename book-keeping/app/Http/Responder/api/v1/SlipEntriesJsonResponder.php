<?php

namespace App\Http\Responder\api\v1;

use App\Http\Responder\api\BaseJsonResponder;
use Illuminate\Http\JsonResponse;

class SlipEntriesJsonResponder extends BaseJsonResponder
{
    /**
     * Respond with the SlipEntries JSON.
     *
     * @param array $context
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function response(array $context): JsonResponse
    {
        $this->response->setData($this->translateSlipsFormat($context['slips']));
        $this->response->setStatusCode(JsonResponse::HTTP_OK);

        return $this->response;
    }

    /**
     * Translate slips to slip entry format for JSON.
     *
     * @param array $slips
     *
     * @return array
     */
    private function translateSlipsFormat(array $slips): array
    {
        $translatedSlipEntries = [];

        foreach ($slips as $slipId => $slip) {
            foreach ($slip['items'] as $slipEntryId => $slipEntry) {
                $translatedSlipEntries[] = [
                    'id'      => $slipEntryId,
                    'debit'   => $slipEntry['debit']['account_id'],
                    'credit'  => $slipEntry['credit']['account_id'],
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

        return $translatedSlipEntries;
    }
}