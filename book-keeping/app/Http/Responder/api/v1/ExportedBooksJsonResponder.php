<?php

namespace App\Http\Responder\api\v1;

use App\Http\Responder\api\BaseJsonResponder;
use Illuminate\Http\JsonResponse;

class ExportedBooksJsonResponder extends BaseJsonResponder
{
    /**
     * Respond with the ExportedBooks JSON.
     *
     * @param array $context
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function response(array $context): JsonResponse
    {
        $this->response->setData($context);
        $this->response->setStatusCode(JsonResponse::HTTP_OK);

        return $this->response;
    }
}
