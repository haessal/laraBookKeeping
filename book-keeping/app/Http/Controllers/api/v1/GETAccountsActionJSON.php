<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\AuthenticatedBookKeepingAPIAction;
use App\Http\Responder\api\v1\AccountsJSONResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GETAccountsActionJSON extends AuthenticatedBookKeepingAPIAction
{
    /**
     * AccountsJSON responder instance.
     *
     * @var \App\Http\Responder\api\v1\AccountsJSONResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param \App\Service\BookKeepingService                  $BookKeeping
     * @param \App\Http\Responder\api\v1\AccountsJSONResponder $responder
     *
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, AccountsJSONResponder $responder)
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
        $context['accounts'] = $this->BookKeeping->retrieveAccounts();

        return $this->responder->response($context);
    }
}
