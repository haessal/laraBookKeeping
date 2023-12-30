<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingActionApi;
use App\Http\Responder\api\v1\SlipJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatchBooksSlipsActionApi extends AuthenticatedBookKeepingActionApi
{
    /**
     * SlipJson responder instance.
     *
     * @var \App\Http\Responder\api\v1\SlipJsonResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\api\v1\SlipJsonResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, SlipJsonResponder $responder)
    {
        parent::__construct($BookKeeping);
        $this->responder = $responder;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slipId
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, string $bookId, string $slipId): JsonResponse
    {
        $context = [];
        $response = null;

        if (! $this->BookKeeping->validateUuid($bookId)) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }
        if (! $this->BookKeeping->validateUuid($slipId)) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }
        $result = $this->validateAndTrimSlipContents($request->all());
        if (! $result['success']) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        $slipContents = [];
        if (array_key_exists('date', $request->all())) {
            $slipContents['date'] = $result['slipContents']['date'];
        }
        if (array_key_exists('outline', $request->all())) {
            $slipContents['outline'] = $result['slipContents']['outline'];
        }
        if (array_key_exists('memo', $request->all())) {
            $slipContents['memo'] = $result['slipContents']['memo'];
        }
        [$status, $_] = $this->BookKeeping->updateSlip($slipId, $slipContents, $bookId);
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                [$retrievalStatus, $updatedSlips] = $this->BookKeeping->retrieveSlip($slipId, $bookId);
                if ($retrievalStatus == BookKeepingService::STATUS_NORMAL) {
                    if (isset($updatedSlips)) {
                        $context['slip_id'] = $slipId;
                        $context['slip'] = $updatedSlips[$slipId];
                        $response = $this->responder->response($context);
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
     * Validate the slip contents and trim string data.
     *
     * @param  array<string, mixed>  $slipContents
     * @return array{success: bool, slipContents: array{
     *   date: string,
     *   outline: string,
     *   memo: string,
     * }}
     */
    private function validateAndTrimSlipContents(array $slipContents): array
    {
        $success = true;
        $trimmed = [];

        foreach ($slipContents as $contentsKey => $contentsItem) {
            switch ($contentsKey) {
                case 'date':
                    $trimmed['date'] = trim(strval($contentsItem));
                    break;
                case 'outline':
                    $trimmed['outline'] = trim(strval($contentsItem));
                    break;
                case 'memo':
                    $trimmed['memo'] = trim(strval($contentsItem));
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
            'slipContents' => [
                'date' => array_key_exists('date', $trimmed) ? strval($trimmed['date']) : '',
                'outline' => array_key_exists('outline', $trimmed) ? strval($trimmed['outline']) : '',
                'memo' => array_key_exists('memo', $trimmed) ? strval($trimmed['memo']) : '',
            ],
        ];
    }
}
