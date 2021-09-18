<?php

namespace Tests\Unit;

use App\Http\Responder\api\v1\SlipEntriesJsonResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Responder_api_v1_SlipEntriesJsonResponderTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function response_ReturnJsonResponse()
    {
        $slipId_1 = (string) Str::uuid();
        $slipId_2 = (string) Str::uuid();
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $accountId_3 = (string) Str::uuid();
        $accountId_4 = (string) Str::uuid();
        $accountId_5 = (string) Str::uuid();
        $accountId_6 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
        $slipEntryId_2 = (string) Str::uuid();
        $slipEntryId_3 = (string) Str::uuid();
        $date_1 = '2020-03-04';
        $date_2 = '2020-05-06';
        $slip_outline_1 = 'slipOutline_36';
        $slip_outline_2 = 'slipOutline_37';
        $slip_memo_1 = 'slipMemo_38';
        $slip_memo_2 = 'slipMemo_39';
        $context['slips'] = [
            $slipId_1 => [
                'date'         => $date_1,
                'slip_outline' => $slip_outline_1,
                'slip_memo'    => $slip_memo_1,
                'items'        => [
                    $slipEntryId_1 => [
                        'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                        'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                        'amount'  => 500,
                        'client'  => 'client_50',
                        'outline' => 'outline_51',
                    ],
                    $slipEntryId_2 => [
                        'debit'   => ['account_id' => $accountId_3, 'account_title' => 'accountTitle_3'],
                        'credit'  => ['account_id' => $accountId_4, 'account_title' => 'accountTitle_4'],
                        'amount'  => 600,
                        'client'  => 'client_57',
                        'outline' => 'outline_58',
                    ],
                ],
            ],
            $slipId_2 => [
                'date'         => $date_2,
                'slip_outline' => $slip_outline_2,
                'slip_memo'    => $slip_memo_2,
                'items'        => [
                    $slipEntryId_3 => [
                        'debit'   => ['account_id' => $accountId_5, 'account_title' => 'accountTitle_5'],
                        'credit'  => ['account_id' => $accountId_6, 'account_title' => 'accountTitle_6'],
                        'amount'  => 700,
                        'client'  => 'client_72',
                        'outline' => 'outline_73',
                    ],
                ],
            ],
        ];
        $response_body = [
            [
                'id'      => $slipEntryId_1,
                'debit'   => $accountId_1,
                'credit'  => $accountId_2,
                'amount'  => 500,
                'client'  => 'client_50',
                'outline' => 'outline_51',
                'slip'    => [
                    'id'      => $slipId_1,
                    'date'    => $date_1,
                    'outline' => $slip_outline_1,
                    'memo'    => $slip_memo_1,
                ],
            ],
            [
                'id'      => $slipEntryId_2,
                'debit'   => $accountId_3,
                'credit'  => $accountId_4,
                'amount'  => 600,
                'client'  => 'client_57',
                'outline' => 'outline_58',
                'slip'    => [
                    'id'      => $slipId_1,
                    'date'    => $date_1,
                    'outline' => $slip_outline_1,
                    'memo'    => $slip_memo_1,
                ],
            ],
            [
                'id'      => $slipEntryId_3,
                'debit'   => $accountId_5,
                'credit'  => $accountId_6,
                'amount'  => 700,
                'client'  => 'client_72',
                'outline' => 'outline_73',
                'slip'    => [
                    'id'      => $slipId_2,
                    'date'    => $date_2,
                    'outline' => $slip_outline_2,
                    'memo'    => $slip_memo_2,
                ],
            ],
        ];
        /** @var \Illuminate\Http\JsonResponse|\Mockery\MockInterface $JsonResponseMock */
        $JsonResponseMock = Mockery::mock(JsonResponse::class);
        $JsonResponseMock->shouldReceive('setData')
            ->once()
            ->with($response_body);
        $JsonResponseMock->shouldReceive('setStatusCode')
            ->once()
            ->with(JsonResponse::HTTP_OK);

        $responder = new SlipEntriesJsonResponder($JsonResponseMock);
        $response = $responder->response($context);

        $this->assertTrue(true);
    }
}
