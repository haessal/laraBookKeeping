<?php

namespace App\Http\Responder\api\v1;

use Illuminate\Http\JsonResponse;

class BaseJSONResponder
{
    /**
     * JsonResponse instance.
     *
     * @var \Illuminate\Http\JsonResponse
     */
    protected $response;

    /**
     * Create a new BaseJSONResponder instance.
     *
     * @param \Illuminate\Http\JsonResponse $response
     */
    public function __construct(JsonResponse $response)
    {
        $this->response = $response;
    }
}
