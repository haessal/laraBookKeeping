<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\v1\CreateSlipViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CreateSlipActionHTML extends AuthenticatedBookKeepingAction
{
    /**
     * CreateSlipView responder instance.
     *
     * @var \App\Http\Responder\v1\CreateSlipViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param \App\Service\BookKeepingService                $BookKeeping
     * @param \App\Http\Responder\v1\CreateSlipViewResponder $responder
     *
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, CreateSlipViewResponder $responder)
    {
        parent::__construct($BookKeeping);
        $this->responder = $responder;
    }

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request): Response
    {
        $context = [];

        return $this->responder->response($context);
    }
}
