<?php

namespace App\Http\Controllers\page\v2;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\page\v2\ShowBookSettingsPageResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowBookSettingsActionHtml extends AuthenticatedBookKeepingAction
{
    /**
     * ShowBookSettingsPage responder instance.
     *
     * @var \App\Http\Responder\page\v2\ShowBookSettingsPageResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\page\v2\ShowBookSettingsPageResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, ShowBookSettingsPageResponder $responder)
    {
        parent::__construct($BookKeeping);
        $this->responder = $responder;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $bookId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request, string $bookId): Response
    {
        if (! $this->BookKeeping->validateUuid($bookId)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $book = $this->BookKeeping->retrieveBook($bookId);
        if (is_null($book)) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $context['book'] = $book;

        return $this->responder->response($request, $context);
    }
}
