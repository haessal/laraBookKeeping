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

        if (! $this->BookKeeping->validateUuid($slipId)) {
            $response = new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        } else {
            $slips = $this->BookKeeping->retrieveSlip($slipId);
            if (empty($slips)) {
                $response = new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
            } else {
                $result = $this->validateAndTrimSlipContents($request->all());
                if (! $result['success']) {
                    $response = new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
                } else {
                    $this->BookKeeping->updateSlip($slipId, $result['slipContents']);
                    $updatedSlips = $this->BookKeeping->retrieveSlip($slipId);
                    $context['slip_id'] = $slipId;
                    $context['slip'] = $updatedSlips[$slipId];
                    $response = $this->responder->response($context);
                }
            }
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
        $trimmed_slipContents = [];

        foreach ($slipContents as $contentsKey => $contentsItem) {
            switch ($contentsKey) {
                case 'date':
                    $trimmed_slipContents['date'] = trim(strval($contentsItem));
                    break;
                case 'outline':
                    $trimmed_slipContents['outline'] = trim(strval($contentsItem));
                    break;
                case 'memo':
                    $trimmed_slipContents['memo'] = trim(strval($contentsItem));
                    break;
                default:
                    $success = false;
                    break;
            }
        }
        if (empty($trimmed_slipContents)) {
            $success = false;
        }
        if (array_key_exists('date', $trimmed_slipContents)) {
            if (! $this->BookKeeping->validateDateFormat($trimmed_slipContents['date'])) {
                $success = false;
            }
        }
        if (array_key_exists('outline', $trimmed_slipContents)) {
            if (empty($trimmed_slipContents['outline'])) {
                $success = false;
            }
        }
        if (array_key_exists('memo', $trimmed_slipContents)) {
            if (empty($trimmed_slipContents['memo'])) {
                $trimmed_slipContents['memo'] = null;
            }
        }
        if (! $success) {
            $trimmed_slipContents = [];
        }

        return [
            'success'      => $success,
            'slipContents' => [
                'date'    => $success ? strval($trimmed_slipContents['date']) : '',
                'outline' => $success ? strval($trimmed_slipContents['outline']) : '',
                'memo'    => $success ? strval($trimmed_slipContents['memo']) : '',
            ],
        ];
    }
}
