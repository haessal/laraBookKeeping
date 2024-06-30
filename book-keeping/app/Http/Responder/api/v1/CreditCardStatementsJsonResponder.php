<?php

namespace App\Http\Responder\api\v1;

use App\Http\Responder\api\BaseJsonResponder;
use Illuminate\Http\JsonResponse;

class CreditCardStatementsJsonResponder extends BaseJsonResponder
{
    /**
     * Setup a new JSON response.
     *
     * @param  array{
     *   creditCardStatements: array<string, array{
     *     credit_card_statement_id: string,
     *     credit_card_statement_outline: string,
     *     credit_card_statement_memo: string|null,
     *     date: string,
     *   }>
     * }  $context
     * @param  int  $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(array $context, int $status = JsonResponse::HTTP_OK): JsonResponse
    {
        $this->response->setData($this->convert($context['creditCardStatements']));
        $this->response->setStatusCode($status);

        return $this->response;
    }

    /**
     * Convert the array to output JSON.
     *
     * @param  array<string, array{
     *   credit_card_statement_id: string,
     *   credit_card_statement_outline: string,
     *   credit_card_statement_memo: string|null,
     *   date: string,
     * }>  $creditCardStatements
     * @return array{
     *   id: string,
     *   outline: string,
     *   memo: string|null,
     *   date: string,
     * }[]
     */
    private function convert(array $creditCardStatements): array
    {
        $creditCardStatementList = [];

        foreach ($creditCardStatements as $creditCardStatementId => $creditCardStatementItem) {
            $creditCardStatementList[] = [
                'id' => $creditCardStatementId,
                'outline' => $creditCardStatementItem['credit_card_statement_outline'],
                'memo' => $creditCardStatementItem['credit_card_statement_memo'],
                'date' => $creditCardStatementItem['date'],
            ];
        }

        return $creditCardStatementList;
    }
}
