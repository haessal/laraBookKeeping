<?php

namespace App\Http\Controllers;

use App\Http\Responder\ShowDashboardViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ShowDashboardActionHtml extends AuthenticatedBookKeepingAction
{
    /**
     * ShowTopView responder instance.
     *
     * @var \App\Http\Responder\v1\ShowTopViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param \App\Service\BookKeepingService                $BookKeeping
     * @param \App\Http\Responder\ShowDashboardViewResponder $responder
     *
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, ShowDashboardViewResponder $responder)
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
