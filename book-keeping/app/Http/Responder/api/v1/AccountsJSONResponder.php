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

        foreach ($accounts as $accountTypeKey => $accountType) {
            foreach ($accountType['groups'] as $accountGroupKey => $accountGroupItem) {
                foreach ($accountGroupItem['items'] as $accountId => $accountItem) {
                    $account_list[] = [
                        'id'          => $accountId,
                        'title'       => $accountItem['title'],
                        'description' => $accountItem['description'],
                        'group'       => $accountGroupKey,
                        'group_title' => $accountGroupItem['title'],
                        'is_current'  => $accountGroupItem['isCurrent'],
                        'type'        => $accountTypeKey,
                    ];
                }
            }
        }

        return $account_list;
    }
}
