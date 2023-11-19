<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingActionApi;
use App\Http\Responder\api\v1\ExportedBooksJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExportBooksActionApi extends AuthenticatedBookKeepingActionApi
{
    /**
     * ExportedBooksJsonResponder responder instance.
     *
     * @var \App\Http\Responder\api\v1\ExportedBooksJsonResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param \App\Service\BookKeepingService                       $BookKeeping
     * @param \App\Http\Responder\api\v1\ExportedBooksJsonResponder $responder
     *
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, ExportedBooksJsonResponder $responder)
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
        $context['version'] = '2.0';

        $dumpRequired = $request->query('mode');
        if ($dumpRequired=='dump') {
            $context['books'] = $this->BookKeeping->exportBooks(true);
        } else {
            $context['books'] = $this->BookKeeping->exportBooks(false);
        }

        return $this->responder->response($context);
    }
}
