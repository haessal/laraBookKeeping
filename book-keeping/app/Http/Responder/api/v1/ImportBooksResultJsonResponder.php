<?php

namespace App\Http\Responder\api\v1;

use App\Http\Responder\api\BaseJsonResponder;
use Illuminate\Http\JsonResponse;

class ImportBooksResultJsonResponder extends BaseJsonResponder
{
    /**
     * Setup a new JSON response.
     *
     * @param  array{
     *   sourceUrl: string,
     *   result: array<string, mixed>,
     * }  $context
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(array $context): JsonResponse
    {
        $this->response->setData($context);
        $this->response->setStatusCode(JsonResponse::HTTP_OK);

        return $this->response;
    }
}
