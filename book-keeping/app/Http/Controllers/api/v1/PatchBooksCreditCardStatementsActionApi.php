<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingActionApi;
use App\Http\Responder\api\v1\CreditCardStatementsJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatchBooksCreditCardStatementsActionApi extends AuthenticatedBookKeepingActionApi
{
    /**
     * CreditCardStatementJson responder instance.
     *
     * @var \App\Http\Responder\api\v1\CreditCardStatementsJsonResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\api\v1\CreditCardStatementsJsonResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, CreditCardStatementsJsonResponder $responder)
    {
        parent::__construct($BookKeeping);
        $this->responder = $responder;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $bookId
     * @param  string  $creditCardStatementId
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, string $bookId, string $creditCardStatementId): JsonResponse
    {
        $context = [];
        $response = null;

        if (! $this->BookKeeping->validateUuid($bookId)) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }
        if (! $this->BookKeeping->validateUuid($creditCardStatementId)) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }
        $result = $this->validateAndTrimCreditCardStatementContents($request->all());
        if (! $result['success']) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        $creditCardStatementContents = [];
        if (array_key_exists('date', $request->all())) {
            $creditCardStatementContents['date'] = $result['creditCardStatementContents']['date'];
        }
        if (array_key_exists('outline', $request->all())) {
            $creditCardStatementContents['outline'] = $result['creditCardStatementContents']['outline'];
        }
        if (array_key_exists('memo', $request->all())) {
            $creditCardStatementContents['memo'] = $result['creditCardStatementContents']['memo'];
        }
        [$status, $_] = $this->BookKeeping->updateCreditCardStatement($creditCardStatementId, $creditCardStatementContents, $bookId);
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                [$retrievalStatus, $updatedCreditCardStatements] = $this->BookKeeping->retrieveCreditCardStatements($bookId, $creditCardStatementId);
                switch ($retrievalStatus) {
                    case BookKeepingService::STATUS_NORMAL:
                        if (isset($updatedCreditCardStatements)) {
                            $context['creditCardStatements'] = $updatedCreditCardStatements;
                            $response = $this->responder->response($context);
                        }
                        break;
                    default:
                        break;
                }
                break;
            case BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE:
                $response = new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
                break;
            case BookKeepingService::STATUS_ERROR_AUTH_FORBIDDEN:
                $response = new JsonResponse(null, JsonResponse::HTTP_FORBIDDEN);
                break;
            default:
                break;
        }
        if (is_null($response)) {
            $response = new JsonResponse(null, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * Validate the credit card statement contents and trim string data.
     *
     * @param  array<string, mixed>  $creditCardStatementContents
     * @return array{success: bool, creditCardStatementContents: array{
     *   date: string,
     *   outline: string,
     *   memo: string,
     * }}
     */
    private function validateAndTrimCreditCardStatementContents(array $creditCardStatementContents): array
    {
        $success = true;
        $trimmed = [];

        foreach ($creditCardStatementContents as $contentsKey => $contentsItem) {
            /** @var string $contentsItemStr */
            $contentsItemStr = $contentsItem;
            switch ($contentsKey) {
                case 'date':
                    $trimmed['date'] = trim(strval($contentsItemStr));
                    break;
                case 'outline':
                    $trimmed['outline'] = trim(strval($contentsItemStr));
                    break;
                case 'memo':
                    $trimmed['memo'] = trim(strval($contentsItemStr));
                    break;
                default:
                    $success = false;
                    break;
            }
        }
        if (empty($trimmed)) {
            $success = false;
        }
        if (array_key_exists('date', $trimmed)) {
            if (! $this->BookKeeping->validateDateFormat($trimmed['date'])) {
                $success = false;
            }
        }
        if (array_key_exists('outline', $trimmed)) {
            if (empty($trimmed['outline'])) {
                $success = false;
            }
        }
        if (array_key_exists('memo', $trimmed)) {
            if (empty($trimmed['memo'])) {
                $trimmed['memo'] = null;
            }
        }

        return [
            'success' => $success,
            'creditCardStatementContents' => [
                'date' => array_key_exists('date', $trimmed) ? strval($trimmed['date']) : '',
                'outline' => array_key_exists('outline', $trimmed) ? strval($trimmed['outline']) : '',
                'memo' => array_key_exists('memo', $trimmed) ? strval($trimmed['memo']) : '',
            ],
        ];
    }
}
