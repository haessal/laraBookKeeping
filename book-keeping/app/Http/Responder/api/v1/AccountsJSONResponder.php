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
        $accounts = $context['accounts'];
        $this->response->setData($accounts);
        $this->response->setStatusCode(JsonResponse::HTTP_OK);

        return $this->response;
    }
}
