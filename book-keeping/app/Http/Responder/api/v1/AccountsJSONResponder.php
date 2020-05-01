<?php

namespace App\Http\Responder\api\v1;

use Illuminate\Http\JsonResponse;

class AccountsJSONResponder extends BaseJSONResponder
{
    /**
     * Respond with the Accounts JSON.
     *
     * @param array $context
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function response(array $context): JsonResponse
    {
        $this->response->setData($this->translateAccountsListFormat($context['accounts']));
        $this->response->setStatusCode(JsonResponse::HTTP_OK);

        return $this->response;
    }

    /**
     * Translate account list format for JSON.
     *
     * @param array $accounts
     *
     * @return array
     */
    private function translateAccountsListFormat(array $accounts): array
    {
        $account_list = [];

        foreach ($accounts as $accountId => $accountItem) {
            $account_list[] = [
                'id'          => $accountId,
                'title'       => $accountItem['account_title'],
                'description' => $accountItem['description'],
                'group'       => $accountItem['account_group_id'],
                'group_title' => $accountItem['account_group_title'],
                'is_current'  => $accountItem['is_current'],
                'type'        => $accountItem['account_type'],
            ];
        }

        return $account_list;
    }
}