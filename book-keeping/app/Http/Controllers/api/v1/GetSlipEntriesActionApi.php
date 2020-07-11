<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingActionApi;
use App\Http\Responder\api\v1\SlipEntriesJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetSlipEntriesActionApi extends AuthenticatedBookKeepingActionApi
{
    /**
     * SlipEntriesJson responder instance.
     *
     * @var \App\Http\Responder\api\v1\SlipEntriesJsonResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param \App\Service\BookKeepingService                     $BookKeeping
     * @param \App\Http\Responder\api\v1\SlipEntriesJsonResponder $responder
     *
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, SlipEntriesJsonResponder $responder)
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
        $result = $this->validateAndTrimSlipEntriesQuery($request->all());
        if ($result['success']) {
            $query = $result['query'];
            $context['slips'] = $this->BookKeeping->retrieveSlips($query['from'], $query['to'], $query['debit'], $query['credit'], $query['operand'], $query['keyword']);
            $response = $this->responder->response($context);
        } else {
            $response = new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        return $response;
    }

    /**
     * Validate query.
     *
     * @param array $queries
     *
     * @return array
     */
    private function validateAndTrimSlipEntriesQuery($query): array
    {
        $success = true;
        $from = null;
        $to = null;
        $debit = null;
        $credit = null;
        $operand = null;
        $keyword = null;

        foreach ($query as $queryKey => $queryItem) {
            switch ($queryKey) {
                case 'from':
                    $from = trim($queryItem);
                    break;
                case 'to':
                    $to = trim($queryItem);
                    break;
                case 'debit':
                    $debit = trim($queryItem);
                    break;
                case 'credit':
                    $credit = trim($queryItem);
                    break;
                case 'operand':
                    $operand = trim($queryItem);
                    break;
                case 'keyword':
                    $keyword = trim($queryItem);
                    break;
                default:
                    $success = false;
                    break;
            }
        }
        if (empty($from) && empty($to) && empty($debit) && empty($credit) && empty($keyword)) {
            $success = false;
        }
        if (!($this->BookKeeping->validatePeriod($from, $to))) {
            $success = false;
        }
        if (!empty($operand) && ($operand != 'and') && ($operand != 'or')) {
            $success = false;
        }
        if (!empty($debit) && !($this->BookKeeping->validateUuid($debit))) {
            $success = false;
        }
        if (!empty($credit) && !($this->BookKeeping->validateUuid($credit))) {
            $success = false;
        }
        if (!empty($debit) && !empty($credit) && empty($operand)) {
            $success = false;
        }
        $trimmed_query = ['from' => $from, 'to' => $to, 'debit' => $debit, 'credit' => $credit, 'operand' => $operand, 'keyword' => $keyword];

        return ['success' => $success, 'query' => $trimmed_query];
    }
}
