<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingActionApi;
use App\Http\Responder\api\v1\CreditCardStatementsJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostBooksCreditCardStatementsActionApi extends AuthenticatedBookKeepingActionApi
{
    /**
     * CreditCardStatementsJsonResponder responder instance.
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, string $bookId): JsonResponse
    {
        $context = [];
        $response = null;

        if (! $this->BookKeeping->validateUuid($bookId)) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }
        $result = $this->validateAndTrimCreditCardStatement($request->all());
        if (! $result['success']) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        $creditCardStatement = $result['creditCardStatement'];
        [$status, $creditCardStatementId] = $this->BookKeeping->createCreditCardStatement(
            $creditCardStatement['outline'],
            $creditCardStatement['memo'],
            $creditCardStatement['date'],
            null,
            $bookId,
        );
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                if (isset($creditCardStatementId)) {
                    [$retrievalStatus, $creditCardStatements] = $this->BookKeeping->retrieveCreditCardStatements(
                        $bookId,
                        $creditCardStatementId,
                    );
                    switch ($retrievalStatus) {
                        case BookKeepingService::STATUS_NORMAL:
                            if (isset($creditCardStatements)) {
                                $context['creditCardStatements'] = $creditCardStatements;
                                $response = $this->responder->response($context, JsonResponse::HTTP_CREATED);
                            }
                            break;
                        default:
                            break;
                    }
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
     * Validate the credit card statement and trim string data.
     *
     * @param  array<string, mixed>  $creditCardStatement
     * @return array{success: bool, creditCardStatement: array{
     *   outline: string,
     *   memo: string|null,
     *   date: string,
     * }}
     */
    private function validateAndTrimCreditCardStatement(array $creditCardStatement): array
    {
        $success = true;
        $trimmedCreditCardStatement = [];

        $trimmed_outline = $this->validateAndTrimString($creditCardStatement, 'outline');
        if (is_null($trimmed_outline)) {
            $success = false;
        } else {
            $trimmedCreditCardStatement['outline'] = $trimmed_outline;
        }
        if (! array_key_exists('memo', $creditCardStatement) || is_null($creditCardStatement['memo'])) {
            $trimmedCreditCardStatement['memo'] = null;
        } else {
            if (! is_string($creditCardStatement['memo'])) {
                $success = false;
            } else {
                $trimmed_memo = trim($creditCardStatement['memo']);
                if (empty($trimmed_memo)) {
                    $trimmedCreditCardStatement['memo'] = null; // @codeCoverageIgnore
                } else {
                    $trimmedCreditCardStatement['memo'] = $trimmed_memo;
                }
            }
        }
        $trimmed_date = $this->validateAndTrimString($creditCardStatement, 'date');
        if (is_null($trimmed_date)) {
            $success = false;
        } else {
            if (! $this->BookKeeping->validateDateFormat($trimmed_date)) {
                $success = false;
            } else {
                $trimmedCreditCardStatement['date'] = $trimmed_date;
            }
        }

        return [
            'success' => $success,
            'creditCardStatement' => [
                'outline' => array_key_exists('outline', $trimmedCreditCardStatement) ? $trimmedCreditCardStatement['outline'] : '',
                'memo' => array_key_exists('memo', $trimmedCreditCardStatement) ? $trimmedCreditCardStatement['memo'] : '',
                'date' => array_key_exists('date', $trimmedCreditCardStatement) ? $trimmedCreditCardStatement['date'] : '',
            ],
        ];
    }

    /**
     * Validate and trim string data.
     *
     * @param  array<string, mixed>  $array_in
     * @param  string  $key
     * @return string|null
     */
    private function validateAndTrimString(array $array_in, string $key): ?string
    {
        if (! array_key_exists($key, $array_in) || ! is_string($array_in[$key])) {
            $string_out = null;
        } else {
            $trimmed_string = trim($array_in[$key]);
            if (empty($trimmed_string)) {
                $string_out = null; // @codeCoverageIgnore
            } else {
                $string_out = $trimmed_string;
            }
        }

        return $string_out;
    }
}
