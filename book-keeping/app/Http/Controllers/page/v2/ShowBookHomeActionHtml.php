<?php

namespace App\Http\Controllers\page\v2;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\page\v2\ShowBookHomePageResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowBookHomeActionHtml extends AuthenticatedBookKeepingAction
{
    /**
     * ShowBookHomePage responder instance.
     *
     * @var \App\Http\Responder\page\v2\ShowBookHomePageResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\page\v2\ShowBookHomePageResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, ShowBookHomePageResponder $responder)
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

        $today = date('Y-m-d');
        [$status, $statements] = $this->BookKeeping->retrieveProfitLossBalanceSheetSlipsOfOneDay($today, $bookId);
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                if (! isset($statements)) {
                    abort(Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                break;
            case BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE:
                abort(Response::HTTP_NOT_FOUND);
            default:
                abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $book = $this->BookKeeping->retrieveBook($bookId);
        if (is_null($book)) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $context['book'] = $book;
        // $context['book'] = $this->BookKeeping->retrieveBook($bookId);

        // $context['date'] = $today;
        // $context['income_statement'] = $statements['profit_loss'];
        // $context['balance_sheet'] = $statements['balance_sheet'];
        // $context['slips'] = $statements['slips'];

        return $this->responder->response($request, $context);
    }
}
