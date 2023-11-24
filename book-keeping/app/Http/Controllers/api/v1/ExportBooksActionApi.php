<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingMigrationActionApi;
use App\Http\Responder\api\v1\ExportedBooksJsonResponder;
use App\Service\BookKeepingMigration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExportBooksActionApi extends AuthenticatedBookKeepingMigrationActionApi
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
     * @param  \App\Service\BookKeepingMigration  $BookKeeping
     * @param  \App\Http\Responder\api\v1\ExportedBooksJsonResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingMigration $BookKeeping, ExportedBooksJsonResponder $responder)
    {
        parent::__construct($BookKeeping);
        $this->responder = $responder;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $context['version'] = '2.0';

        $dumpRequired = $request->query('mode');
        if ($dumpRequired == 'dump') {
            $context['books'] = $this->BookKeeping->dumpBooks();
        } else {
            $context['books'] = $this->BookKeeping->exportBooks();
        }

        return $this->responder->response($context);
    }
}
