<?php

namespace Tests\Unit;

use App\Http\Responder\v1\BaseViewResponder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

class Http_Responder_v1_BaseViewResponderTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function navilinks_NaviListIsReturned()
    {
        $ResponseMock = Mockery::mock(Response::class);
        $ViewFactoryMock = Mockery::mock(Factory::class);

        $responder = new BaseViewResponder($ResponseMock, $ViewFactoryMock);
        $navilinks = $responder->navilinks();

        $this->assertIsArray($navilinks);
    }

    /**
     * @test
     */
    public function sortAccountInAscendingCodeOrder_ReorderedListIsReturned()
    {
        $groupedList = [
            '844db4d1-bdc2-4e1c-a56e-82a166b13afe' => [
                'title' => 'GroupTitle1', 'isCurrent' => 0, 'amount' => 148112, 'account_group_bk_code' => 4300,
                'items' => [
                    '2bfd6973-2622-49ea-884a-8e18dc15d05e' => ['title' => 'ItemTitle11', 'amount' => 79692, 'account_bk_code' => null],
                    '9a453a41-ba5b-45cc-a29a-d6e12cc08fc0' => ['title' => 'ItemTitle12', 'amount' => 57000, 'account_bk_code' => 4301],
                    'a4adb10e-ecfc-4497-9501-72813c5e4b54' => ['title' => 'ItemTitle13', 'amount' => 11420, 'account_bk_code' => 4303],
                ],
            ],
            '1bd92f3a-19d3-4ea2-bc7b-3e791446ddac' => [
                'title' => 'GroupTitle2', 'isCurrent' => 0, 'amount' => 3610, 'account_group_bk_code' => 4400,
                'items' => [
                    '4bc0d8d9-e8ec-4258-a812-b53c8f7babd7' => ['title' => 'ItemTitle21', 'amount' => 57, 'account_bk_code' => 4403],
                    '57d3c146-0205-470a-9c3a-409e72432681' => ['title' => 'ItemTitle22', 'amount' => 2457, 'account_bk_code' => 4404],
                    '6eb9015e-039f-4028-8b2b-3f3279c8849c' => ['title' => 'ItemTitle23', 'amount' => 346, 'account_bk_code' => 4402],
                    'f4c7b564-79e4-4037-9814-ec4bb040c58e' => ['title' => 'ItemTitle24', 'amount' => 750, 'account_bk_code' => 4407],
                ],
            ],
            '9b0c599e-28f8-4974-9ae1-4145f3770f52' => [
                'title' => 'GroupTitle3', 'isCurrent' => 0, 'amount' => 13446, 'account_group_bk_code' => 4100,
                'items' => [
                    '515a27c0-ad28-4f08-9ac9-624b5f45c3d6' => ['title' => 'ItemTitle31', 'amount' => 13246, 'account_bk_code' => 4104],
                    '7c707a00-0e95-4ae4-886c-b46678aeb5b5' => ['title' => 'ItemTitle32', 'amount' => 200, 'account_bk_code' => 4103],
                ],
            ],
            'ed88bc2c-8b82-45cf-b276-f2046b7b22e9' => [
                'title' => 'GroupTitle4', 'isCurrent' => 1, 'amount' => 394151, 'account_group_bk_code' => 4200,
                'items' => [
                    '944ddb3d-6cc9-43ad-9ba1-2dffd2ee5b58' => ['title' => 'ItemTitle33', 'amount' => 394151, 'account_bk_code' => 4213],
                ],
            ],
        ];
        $reordered_expected = [
            'ed88bc2c-8b82-45cf-b276-f2046b7b22e9' => [
                'title' => 'GroupTitle4', 'isCurrent' => 1, 'amount' => 394151,
                'items' => [
                    '944ddb3d-6cc9-43ad-9ba1-2dffd2ee5b58' => ['title' => 'ItemTitle33', 'amount' => 394151],
                ],
            ],
            '9b0c599e-28f8-4974-9ae1-4145f3770f52' => [
                'title' => 'GroupTitle3', 'isCurrent' => 0, 'amount' => 13446,
                'items' => [
                    '7c707a00-0e95-4ae4-886c-b46678aeb5b5' => ['title' => 'ItemTitle32', 'amount' => 200],
                    '515a27c0-ad28-4f08-9ac9-624b5f45c3d6' => ['title' => 'ItemTitle31', 'amount' => 13246],
                ],
            ],
            '844db4d1-bdc2-4e1c-a56e-82a166b13afe' => [
                'title' => 'GroupTitle1', 'isCurrent' => 0, 'amount' => 148112,
                'items' => [
                    '9a453a41-ba5b-45cc-a29a-d6e12cc08fc0' => ['title' => 'ItemTitle12', 'amount' => 57000],
                    'a4adb10e-ecfc-4497-9501-72813c5e4b54' => ['title' => 'ItemTitle13', 'amount' => 11420],
                    '2bfd6973-2622-49ea-884a-8e18dc15d05e' => ['title' => 'ItemTitle11', 'amount' => 79692],
                ],
            ],
            '1bd92f3a-19d3-4ea2-bc7b-3e791446ddac' => [
                'title' => 'GroupTitle2', 'isCurrent' => 0, 'amount' => 3610,
                'items' => [
                    '6eb9015e-039f-4028-8b2b-3f3279c8849c' => ['title' => 'ItemTitle23', 'amount' => 346],
                    '4bc0d8d9-e8ec-4258-a812-b53c8f7babd7' => ['title' => 'ItemTitle21', 'amount' => 57],
                    '57d3c146-0205-470a-9c3a-409e72432681' => ['title' => 'ItemTitle22', 'amount' => 2457],
                    'f4c7b564-79e4-4037-9814-ec4bb040c58e' => ['title' => 'ItemTitle24', 'amount' => 750],
                ],
            ],
        ];
        $ResponseMock = Mockery::mock(Response::class);
        $ViewFactoryMock = Mockery::mock(Factory::class);

        $responder = new BaseViewResponder($ResponseMock, $ViewFactoryMock);
        $reordered_actual = $responder->sortAccountInAscendingCodeOrder($groupedList);

        $this->assertSame($reordered_expected, $reordered_actual);
    }

    /**
     * @test
     */
    public function translateBalanceSheetFormat_FormattedBalanceSheetIsReturned()
    {
        $statements = [
            'asset' => [
                'amount' => 24863926,
                'groups' => [
                    'c46db1a8-daf3-4cd0-befd-2539885d8406' => [
                        'title' => 'GroupTitle1', 'isCurrent' => 1, 'amount' => 267830,
                        'items' => [
                            '12c52f0f-3fd1-4076-bab0-521c6903ebe9' => ['title' => 'ItemTitle11', 'amount' => 218842],
                            '26934102-c58a-42d1-b884-d0cc729d1eeb' => ['title' => 'ItemTitle12', 'amount' => 27000],
                        ],
                    ],
                ],
            ],
            'liability' => [
                'amount' => 357386,
                'groups' => [
                    'e0c81e5c-3b81-4237-837e-e004c2695ee1' => [
                        'title' => 'GroupTitle2', 'isCurrent' => 1, 'amount' => 33235,
                        'items' => [
                            '62cb01ca-fc2f-44f9-a846-43a5a67f7491' => ['title' => 'ItemTitle21', 'amount' => 11187],
                        ],
                    ],
                    'f4d64c3f-991e-4d52-bd10-946587589cac' => [
                        'title' => 'GroupTitle3', 'isCurrent' => 1, 'amount' => 324159,
                        'items' => [
                            'fcadddd5-c5b3-4296-a2cd-81af02db5016' => ['title' => 'ItemTitle31', 'amount' => 22048],
                            'bf9fbc93-f496-4b5c-ab6f-6e0b09f7b20a' => ['title' => 'ItemTitle32', 'amount' => 324151],
                        ],
                    ],
                ],
            ],
            'current_net_asset' => ['amount' => 14379340],
            'net_asset'         => ['amount' => 24506540],
        ];
        $formattedBalanceSheet_expected = [
            [
                'debit'  => ['title' => __('Assets'), 'amount' => '24,863,926', 'bold' => true, 'italic' => true],
                'credit' => ['title' => __('Liabilities'), 'amount' => '357,386', 'bold' => true, 'italic' => true],
            ],
            [
                'debit'  => ['title' => 'GroupTitle1', 'amount' => '267,830', 'bold' => false, 'italic' => true],
                'credit' => ['title' => 'GroupTitle2', 'amount' => '33,235', 'bold' => false, 'italic' => true],
            ],
            [
                'debit'  => ['title' => 'ItemTitle11', 'amount' => '218,842', 'bold' => false, 'italic' => false],
                'credit' => ['title' => 'ItemTitle21', 'amount' => '11,187', 'bold' => false, 'italic' => false],
            ],
            [
                'debit'  => ['title' => 'ItemTitle12', 'amount' => '27,000', 'bold' => false, 'italic' => false],
                'credit' => ['title' => 'GroupTitle3', 'amount' => '324,159', 'bold' => false, 'italic' => true],
            ],
            [
                'credit' => ['title' => 'ItemTitle31', 'amount' => '22,048', 'bold' => false, 'italic' => false],
                'debit'  => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'credit' => ['title' => 'ItemTitle32', 'amount' => '324,151', 'bold' => false, 'italic' => false],
                'debit'  => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'credit' => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
                'debit'  => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'credit' => ['title' => __('Current Net Asset'), 'amount' => '14,379,340', 'bold' => true, 'italic' => true],
                'debit'  => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'credit' => ['title' => __('Net Asset'), 'amount' => '24,506,540', 'bold' => true, 'italic' => true],
                'debit'  => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
        ];
        $ResponseMock = Mockery::mock(Response::class);
        $ViewFactoryMock = Mockery::mock(Factory::class);

        $responder = new BaseViewResponder($ResponseMock, $ViewFactoryMock);
        $formattedBalanceSheet_actual = $responder->translateBalanceSheetFormat($statements);

        $this->assertSame($formattedBalanceSheet_expected, $formattedBalanceSheet_actual);
    }

    /**
     * @test
     */
    public function translateBalanceSheetFormat_FormattedBalanceSheetIsReturnedWithoutCurrentNetAsset()
    {
        $statements = [
            'asset' => [
                'amount' => 24863926,
                'groups' => [
                    'c46db1a8-daf3-4cd0-befd-2539885d8406' => [
                        'title' => 'GroupTitle1', 'isCurrent' => 1, 'amount' => 267830,
                        'items' => [
                            '12c52f0f-3fd1-4076-bab0-521c6903ebe9' => ['title' => 'ItemTitle11', 'amount' => 218842],
                            '26934102-c58a-42d1-b884-d0cc729d1eeb' => ['title' => 'ItemTitle12', 'amount' => 27000],
                        ],
                    ],
                ],
            ],
            'liability' => [
                'amount' => 357386,
                'groups' => [
                    'e0c81e5c-3b81-4237-837e-e004c2695ee1' => [
                        'title' => 'GroupTitle2', 'isCurrent' => 1, 'amount' => 33235,
                        'items' => [
                            '62cb01ca-fc2f-44f9-a846-43a5a67f7491' => ['title' => 'ItemTitle21', 'amount' => 11187],
                        ],
                    ],
                    'f4d64c3f-991e-4d52-bd10-946587589cac' => [
                        'title' => 'GroupTitle3', 'isCurrent' => 1, 'amount' => 324159,
                        'items' => [
                            'fcadddd5-c5b3-4296-a2cd-81af02db5016' => ['title' => 'ItemTitle31', 'amount' => 22048],
                            'bf9fbc93-f496-4b5c-ab6f-6e0b09f7b20a' => ['title' => 'ItemTitle32', 'amount' => 324151],
                        ],
                    ],
                ],
            ],
            'net_asset'         => ['amount' => 24506540],
        ];
        $formattedBalanceSheet_expected = [
            [
                'debit'  => ['title' => __('Assets'), 'amount' => '24,863,926', 'bold' => true, 'italic' => true],
                'credit' => ['title' => __('Liabilities'), 'amount' => '357,386', 'bold' => true, 'italic' => true],
            ],
            [
                'debit'  => ['title' => 'GroupTitle1', 'amount' => '267,830', 'bold' => false, 'italic' => true],
                'credit' => ['title' => 'GroupTitle2', 'amount' => '33,235', 'bold' => false, 'italic' => true],
            ],
            [
                'debit'  => ['title' => 'ItemTitle11', 'amount' => '218,842', 'bold' => false, 'italic' => false],
                'credit' => ['title' => 'ItemTitle21', 'amount' => '11,187', 'bold' => false, 'italic' => false],
            ],
            [
                'debit'  => ['title' => 'ItemTitle12', 'amount' => '27,000', 'bold' => false, 'italic' => false],
                'credit' => ['title' => 'GroupTitle3', 'amount' => '324,159', 'bold' => false, 'italic' => true],
            ],
            [
                'credit' => ['title' => 'ItemTitle31', 'amount' => '22,048', 'bold' => false, 'italic' => false],
                'debit'  => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'credit' => ['title' => 'ItemTitle32', 'amount' => '324,151', 'bold' => false, 'italic' => false],
                'debit'  => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'credit' => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
                'debit'  => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'credit' => ['title' => __('Net Asset'), 'amount' => '24,506,540', 'bold' => true, 'italic' => true],
                'debit'  => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
        ];
        $ResponseMock = Mockery::mock(Response::class);
        $ViewFactoryMock = Mockery::mock(Factory::class);

        $responder = new BaseViewResponder($ResponseMock, $ViewFactoryMock);
        $formattedBalanceSheet_actual = $responder->translateBalanceSheetFormat($statements);

        $this->assertSame($formattedBalanceSheet_expected, $formattedBalanceSheet_actual);
    }

    /**
     * @test
     */
    public function translateIncomeStatementFormat_FormattedIncomeStatementIsReturned()
    {
        $statements = [
            'expense' => [
                'amount' => 559319,
                'groups' => [
                    '844db4d1-bdc2-4e1c-a56e-82a166b13afe' => [
                        'title' => 'GroupTitle1', 'isCurrent' => 0, 'amount' => 148112,
                        'items' => [
                            '2bfd6973-2622-49ea-884a-8e18dc15d05e' => ['title' => 'ItemTitle11', 'amount' => 79692],
                            '9a453a41-ba5b-45cc-a29a-d6e12cc08fc0' => ['title' => 'ItemTitle12', 'amount' => 57000],
                            'a4adb10e-ecfc-4497-9501-72813c5e4b54' => ['title' => 'ItemTitle13', 'amount' => 11420],
                        ],
                    ],
                    '1bd92f3a-19d3-4ea2-bc7b-3e791446ddac' => [
                        'title' => 'GroupTitle2', 'isCurrent' => 0, 'amount' => 3610,
                        'items' => [
                            '4bc0d8d9-e8ec-4258-a812-b53c8f7babd7' => ['title' => 'ItemTitle21', 'amount' => 57],
                            '57d3c146-0205-470a-9c3a-409e72432681' => ['title' => 'ItemTitle22', 'amount' => 2457],
                        ],
                    ],
                ],
            ],
            'revenue' => [
                'amount' => 546070,
                'groups' => [
                    'a6b22699-2459-4b62-87d4-2ad422aba824' => [
                        'title' => 'GroupTitle3', 'isCurrent' => 0, 'amount' => 3461,
                        'items' => [
                            '22803ae3-ee3b-4bab-9574-c250bf822c43' => ['title' => 'ItemTitle31', 'amount' => 3457],
                            '8dc19eb8-4707-4d3e-844d-4dd570f768d8' => ['title' => 'ItemTitle32', 'amount' => 4],
                        ],
                    ],
                ],
            ],
            'net_income' => ['amount' => -13249],
        ];
        $formattedIncomeStatement_expected = [
            [
                'debit'  => ['title' => __('Expense'), 'amount' => '559,319', 'bold' => true, 'italic' => true],
                'credit' => ['title' => __('Revenue'), 'amount' => '546,070', 'bold' => true, 'italic' => true],
            ],
            [
                'debit'  => ['title' => 'GroupTitle1', 'amount' => '148,112', 'bold' => false, 'italic' => true],
                'credit' => ['title' => 'GroupTitle3', 'amount' => '3,461', 'bold' => false, 'italic' => true],
            ],
            [
                'debit'  => ['title' => 'ItemTitle11', 'amount' => '79,692', 'bold' => false, 'italic' => false],
                'credit' => ['title' => 'ItemTitle31', 'amount' => '3,457', 'bold' => false, 'italic' => false],
            ],
            [
                'debit'  => ['title' => 'ItemTitle12', 'amount' => '57,000', 'bold' => false, 'italic' => false],
                'credit' => ['title' => 'ItemTitle32', 'amount' => '4', 'bold' => false, 'italic' => false],
            ],
            [
                'debit'  => ['title' => 'ItemTitle13', 'amount' => '11,420', 'bold' => false, 'italic' => false],
                'credit' => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'debit'  => ['title' => 'GroupTitle2', 'amount' => '3,610', 'bold' => false, 'italic' => true],
                'credit' => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'debit'  => ['title' => 'ItemTitle21', 'amount' => '57', 'bold' => false, 'italic' => false],
                'credit' => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'debit'  => ['title' => 'ItemTitle22', 'amount' => '2,457', 'bold' => false, 'italic' => false],
                'credit' => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'debit'  => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
                'credit' => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'debit'  => ['title' => __('Net Income'), 'amount' => '-13,249', 'bold' => true, 'italic' => true],
                'credit' => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
        ];
        $ResponseMock = Mockery::mock(Response::class);
        $ViewFactoryMock = Mockery::mock(Factory::class);

        $responder = new BaseViewResponder($ResponseMock, $ViewFactoryMock);
        $formattedIncomeStatement_actual = $responder->translateIncomeStatementFormat($statements);

        $this->assertSame($formattedIncomeStatement_expected, $formattedIncomeStatement_actual);
    }

    /**
     * @test
     */
    public function translateSlipsFormat_FormattedSlipsAreReturned()
    {
        $slips = [
            '3238ed0a-265a-408d-8e7f-03b5c138abc2' => [
                'date'         => '2019-09-01',
                'slip_outline' => 'slip_outline1',
                'slip_memo'    => null,
                'items'        => [
                    '9d7d0222-ef76-487c-8319-220b2e6911a7' => [
                        'debit' => [
                            'account_id'    => 'dd406a58-d95e-43b6-8d86-b07690ee8983',
                            'account_title' => 'account_title1',
                        ],
                        'credit' => [
                            'account_id'    => 'fcadddd5-c5b3-4296-a2cd-81af02db5016',
                            'account_title' => 'account_title2',
                        ],
                        'amount'  => 1000,
                        'client'  => 'client1',
                        'outline' => 'outline1',
                    ],
                ],
            ],
            'bd9ca022-8d49-4589-9eba-f05ee1af1856' => [
                'date'         => '2019-09-05',
                'slip_outline' => 'slip_outline2',
                'slip_memo'    => null,
                'items'        => [
                    '2847e922-8a45-48f4-97f8-cdeee40ed8f9' => [
                        'debit' => [
                            'account_id'    => '96e7f7d0-e5fa-4946-850d-d3b1138db320',
                            'account_title' => 'account_title3',
                        ],
                        'credit' => [
                            'account_id'    => 'f94c0efc-b82f-4852-b59a-eca5e30fba3c',
                            'account_title' => 'account_title4',
                        ],
                        'amount'  => 50000,
                        'client'  => 'client2',
                        'outline' => 'outline2',
                    ],
                    'e5cade29-f4e4-477b-8e3c-e4ab6ca7caa8' => [
                        'debit' => [
                            'account_id'    => 'ff65f247-427d-4484-a964-6c81588b9a12',
                            'account_title' => 'account_title5',
                        ],
                        'credit' => [
                            'account_id'    => 'f94c0efc-b82f-4852-b59a-eca5e30fba3c',
                            'account_title' => 'account_title4',
                        ],
                        'amount'  => 750,
                        'client'  => 'client3',
                        'outline' => 'outline3',
                    ],
                ],
            ],
        ];
        $slipentryline_expected = [
            [
                'no'      => '9d7d02..',
                'slipno'  => '3238ed..',
                'date'    => '2019-09-01',
                'debit'   => 'account_title1',
                'credit'  => 'account_title2',
                'amount'  => '1,000',
                'client'  => 'client1',
                'outline' => 'outline1',
            ],
            [
                'no'      => '2847e9..',
                'slipno'  => 'bd9ca0..',
                'date'    => '2019-09-05',
                'debit'   => 'account_title3',
                'credit'  => 'account_title4',
                'amount'  => '50,000',
                'client'  => 'client2',
                'outline' => 'outline2',
            ],
            [
                'no'      => 'e5cade..',
                'slipno'  => 'bd9ca0..',
                'date'    => '2019-09-05',
                'debit'   => 'account_title5',
                'credit'  => 'account_title4',
                'amount'  => '750',
                'client'  => 'client3',
                'outline' => 'outline3',
            ],
        ];
        $ResponseMock = Mockery::mock(Response::class);
        $ViewFactoryMock = Mockery::mock(Factory::class);

        $responder = new BaseViewResponder($ResponseMock, $ViewFactoryMock);
        $slipentryline_actual = $responder->translateSlipsFormat($slips);

        $this->assertSame($slipentryline_expected, $slipentryline_actual);
    }
}
