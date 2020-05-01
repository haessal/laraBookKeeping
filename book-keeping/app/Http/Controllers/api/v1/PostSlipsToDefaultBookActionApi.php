<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\AuthenticatedBookKeepingAPIAction;
use App\Http\Responder\api\v1\SlipJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostSlipsToDefaultBookActionApi extends AuthenticatedBookKeepingAPIAction
{
    /**
     * SlipJson responder instance.
     *
     * @var \App\Http\Responder\api\v1\SlipJsonResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param \App\Service\BookKeepingService              $BookKeeping
     * @param \App\Http\Responder\api\v1\SlipJsonResponder $responder
     *
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, SlipJsonResponder $responder)
    {
        parent::__construct($BookKeeping);
        $this->responder = $responder;
    }

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
        $slip = $request->all();
        $outline = trim($slip['outline']);
        $date = trim($slip['date']);
        $entries = $slip['entries'];
        if (empty($entries)) {
            $entries = [];
        }
        $memo = trim($slip['memo']);
        if (empty($memo)) {
            $memo = null;
        }
        $slipId = $this->BookKeeping->createSlip($outline, $date, $entries, $memo);
        $context['slips'] = $this->BookKeeping->retrieveSlip($slipId);

        return $this->responder->response($context);
    }
}
