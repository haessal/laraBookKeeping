<?php

namespace App\Http\Controllers\page\v2;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\page\v2\ShowSettingsViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ShowSettingsActionHtml extends AuthenticatedBookKeepingAction
{
    /**
     * ShowSettingsView responder instance.
     *
     * @var \App\Http\Responder\page\v2\ShowSettingsViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\page\v2\ShowSettingsViewResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, ShowSettingsViewResponder $responder)
    {
        parent::__construct($BookKeeping);
        $this->responder = $responder;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $bookId
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, string $bookId): Response
    {
        $context = [];

        if (! $this->BookKeeping->validateUuid($bookId)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        [$status, $information] = $this->BookKeeping->retrieveBookInformation($bookId);
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                if (isset($information)) {
                    $context['bookId'] = $bookId;
                    $context['book'] = $information;
                } else {
                    abort(Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                break;
            case BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE:
                abort(Response::HTTP_NOT_FOUND);
            default:
                abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->responder->response($context);
    }
}
