<?php

namespace App\Http\Controllers\page;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\page\ShowDashboardPageResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowDashboardActionHtml extends AuthenticatedBookKeepingAction
{
    /**
     * ShowDashboardView responder instance.
     *
     * @var \App\Http\Responder\page\ShowDashboardPageResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\page\ShowDashboardPageResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, ShowDashboardPageResponder $responder)
    {
        parent::__construct($BookKeeping);
        $this->responder = $responder;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request): Response
    {
        $context = [];

        $context['books'] = $this->BookKeeping->retrieveAvailableBooks();

        return $this->responder->response($request, $context);
    }
}
