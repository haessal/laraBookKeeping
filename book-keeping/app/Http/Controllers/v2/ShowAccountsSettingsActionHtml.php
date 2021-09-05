<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\v2\ShowAccountsSettingsViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ShowAccountsSettingsActionHtml extends AuthenticatedBookKeepingAction
{
    /**
     * ShowAccountsSettingsView responder instance.
     *
     * @var \App\Http\Responder\v2\ShowAccountsSettingsViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param \App\Service\BookKeepingService                          $BookKeeping
     * @param \App\Http\Responder\v2\ShowAccountsSettingsViewResponder $responder
     *
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, ShowAccountsSettingsViewResponder $responder)
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
    public function __invoke(Request $request, string $bookId): Response
    {
        $context = [];

        $context['book'] = $this->BookKeeping->retrieveBookInformation($bookId);
        $context['accounts'] = $this->BookKeeping->retrieveAccounts(false, $bookId);

        return $this->responder->response($context);
    }
}