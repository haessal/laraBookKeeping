<?php

namespace App\Http\Controllers\api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostSlipsToDefaultBookActionApi extends PostSlipsActionApi
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $context = [];
        $slip = $this->trimDraftSlip($request->all());
        $accounts = $this->BookKeeping->retrieveAccountsList();
        if ($this->validateDraftSlip($slip, $accounts)) {
            $slipId = $this->BookKeeping->createSlip($slip['outline'], $slip['date'], $slip['entries'], $slip['memo']);
            $context['slips'] = $this->BookKeeping->retrieveSlip($slipId);
            $response = $this->responder->response($context);
        } else {
            $response = new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        return $response;
    }
}
