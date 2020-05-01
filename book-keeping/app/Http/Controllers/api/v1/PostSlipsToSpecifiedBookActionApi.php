<?php

namespace App\Http\Controllers\api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostSlipsToSpecifiedBookActionApi extends PostSlipsActionApi
{
    /**
     * Handle the incoming request.
     *
     * @param string                   $bookId
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(string $bookId, Request $request): JsonResponse
    {
        $context = [];
        $slip = $this->trimDraftSlip($request->all());
        $accounts = $this->BookKeeping->retrieveAccountsList($bookId);
        if ($this->validateDraftSlip($slip, $accounts)) {
            $slipId = $this->BookKeeping->createSlip($slip['outline'], $slip['date'], $slip['entries'], $slip['memo'], $bookId);
            $context['slips'] = $this->BookKeeping->retrieveSlip($slipId);
            $response = $this->responder->response($context);
        } else {
            $response = new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        return $response;
    }
}
