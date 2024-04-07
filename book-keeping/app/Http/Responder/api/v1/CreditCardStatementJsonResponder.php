<?php

namespace App\Http\Responder\api\v1;

use App\Http\Responder\api\BaseJsonResponder;
use Illuminate\Http\JsonResponse;

class CreditCardStatementJsonResponder extends BaseJsonResponder
{
    /**
     * Setup a new JSON response.
     *
     * @param  array{
     *   creditCardStatement: array{
     *     statement: array{
     *       slip_entries: array{
     *         slip_id: string,
     *         date: string,
     *         slip_outline: string,
     *         slip_memo: string,
     *         slip_entry_id: string,
     *         debit: string,
     *         credit: string,
     *         amount: int,
     *         client: string,
     *         outline: string,
     *         credit_card_statement_id: string,
     *       }[],
     *       total_amount: int,
     *     },
     *     payment: array{
     *       slip_entries: array{
     *         slip_id: string,
     *         date: string,
     *         slip_outline: string,
     *         slip_memo: string,
     *         slip_entry_id: string,
     *         debit: string,
     *         credit: string,
     *         amount: int,
     *         client: string,
     *         outline: string,
     *         credit_card_statement_id: string,
     *       }[],
     *       total_amount: int,
     *     },
     *     unpaid_amount: int,
     *   }
     * }  $context
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(array $context): JsonResponse
    {
        $this->response->setData($context['creditCardStatement']);
        $this->response->setStatusCode(JsonResponse::HTTP_OK);

        return $this->response;
    }
}
