<?php

namespace App\Http\Responder\api\v1;

use App\Http\Responder\api\BaseJsonResponder;
use Illuminate\Http\JsonResponse;

class AccountsJsonResponder extends BaseJsonResponder
{
    /**
     * Setup a new JSON response.
     *
     * @param  array{
     *   accounts: array<string, array{
     *     account_type: string,
     *     account_group_id: string,
     *     account_group_title: string,
     *     is_current: bool,
     *     account_id: string,
     *     account_title: string,
     *     description: string,
     *     selectable: bool,
     *   }>
     * }  $context
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(array $context): JsonResponse
    {
        $this->response->setData($this->convert($context['accounts']));
        $this->response->setStatusCode(JsonResponse::HTTP_OK);

        return $this->response;
    }

    /**
     * Convert the array to output JSON.
     *
     * @param  array<string, array{
     *   account_type: string,
     *   account_group_id: string,
     *   account_group_title: string,
     *   is_current: bool,
     *   account_id: string,
     *   account_title: string,
     *   description: string,
     *   selectable: bool,
     * }>  $accounts
     * @return array{
     *   id: string,
     *   title: string,
     *   description: string,
     *   group: string,
     *   group_title: string,
     *   is_current: bool,
     *   type: string,
     * }[]
     */
    private function convert(array $accounts): array
    {
        $accountList = [];

        foreach ($accounts as $accountId => $accountItem) {
            $accountList[] = [
                'id'          => $accountId,
                'title'       => $accountItem['account_title'],
                'description' => $accountItem['description'],
                'group'       => $accountItem['account_group_id'],
                'group_title' => $accountItem['account_group_title'],
                'is_current'  => $accountItem['is_current'],
                'type'        => $accountItem['account_type'],
            ];
        }

        return $accountList;
    }
}
