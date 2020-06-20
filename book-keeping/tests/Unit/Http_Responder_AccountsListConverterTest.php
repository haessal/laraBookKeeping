<?php

namespace Tests\Unit;

use App\Http\Responder\AccountsListConverter;
use Illuminate\Support\Str;
use Tests\TestCase;

class Http_Responder_AccountsListConverterTest extends TestCase
{
    /**
     * @test
     */
    public function sortAccountInAscendingCodeOrder_ReorderedListIsReturned()
    {
        $groupedList = [
            '844db4d1-bdc2-4e1c-a56e-82a166b13afe' => [
                'title' => 'GroupTitle1', 'isCurrent' => 0, 'amount' => 148112, 'bk_code' => 4300, 'createdAt' => '2020-03-01 12:43:00',
                'items' => [
                    '2bfd6973-2622-49ea-884a-8e18dc15d05e' => ['title' => 'ItemTitle11', 'amount' => 79692, 'bk_code' => null, 'createdAt' => '2020-02-01 12:00:14'],
                    '9a453a41-ba5b-45cc-a29a-d6e12cc08fc0' => ['title' => 'ItemTitle12', 'amount' => 57000, 'bk_code' => 4301, 'createdAt' => '2020-03-01 12:43:01'],
                    'a4adb10e-ecfc-4497-9501-72813c5e4b54' => ['title' => 'ItemTitle13', 'amount' => 11420, 'bk_code' => 4303, 'createdAt' => '2020-03-01 12:43:03'],
                    'fbfa988b-6e74-4a47-b6a0-9068aafcbacb' => ['title' => 'ItemTitle14', 'amount' => 29697, 'bk_code' => null, 'createdAt' => '2020-02-01 12:00:11'],
                    'e7971328-7c62-4117-b82f-75b273ec32c1' => ['title' => 'ItemTitle15', 'amount' => 29292, 'bk_code' => null, 'createdAt' => '2020-01-01 12:00:11'],
                ],
            ],
            '1bd92f3a-19d3-4ea2-bc7b-3e791446ddac' => [
                'title' => 'GroupTitle2', 'isCurrent' => 0, 'amount' => 3610, 'bk_code' => 4400, 'createdAt' => '2020-01-01 12:44:00',
                'items' => [
                    '4bc0d8d9-e8ec-4258-a812-b53c8f7babd7' => ['title' => 'ItemTitle21', 'amount' => 57, 'bk_code' => 4403, 'createdAt' => '2019-12-31 12:44:03'],
                    '57d3c146-0205-470a-9c3a-409e72432681' => ['title' => 'ItemTitle22', 'amount' => 2457, 'bk_code' => 4404, 'createdAt' => '2019-12-30 12:44:04'],
                    '6eb9015e-039f-4028-8b2b-3f3279c8849c' => ['title' => 'ItemTitle23', 'amount' => 346, 'bk_code' => 4402, 'createdAt' => '2019-12-29 12:44:02'],
                    'f4c7b564-79e4-4037-9814-ec4bb040c58e' => ['title' => 'ItemTitle24', 'amount' => 750, 'bk_code' => 4407, 'createdAt' => '2019-12-28 12:44:07'],
                ],
            ],
            '9b0c599e-28f8-4974-9ae1-4145f3770f52' => [
                'title' => 'GroupTitle3', 'isCurrent' => 0, 'amount' => 13446, 'bk_code' => 4100, 'createdAt' => '2019-12-27 12:41:00',
                'items' => [
                    '515a27c0-ad28-4f08-9ac9-624b5f45c3d6' => ['title' => 'ItemTitle31', 'amount' => 13246, 'bk_code' => 4104, 'createdAt' => '2019-12-26 12:41:04'],
                    '7c707a00-0e95-4ae4-886c-b46678aeb5b5' => ['title' => 'ItemTitle32', 'amount' => 200, 'bk_code' => 4103, 'createdAt' => '2019-12-25 12:41:03'],
                ],
            ],
            'ed88bc2c-8b82-45cf-b276-f2046b7b22e9' => [
                'title' => 'GroupTitle4', 'isCurrent' => 1, 'amount' => 394151, 'bk_code' => 4200, 'createdAt' => '2019-12-24 12:42:00',
                'items' => [
                    '944ddb3d-6cc9-43ad-9ba1-2dffd2ee5b58' => ['title' => 'ItemTitle33', 'amount' => 394151, 'bk_code' => 4213, 'createdAt' => '2019-12-23 12:42:13'],
                ],
            ],
        ];
        $reordered_expected = [
            'ed88bc2c-8b82-45cf-b276-f2046b7b22e9' => [
                'title' => 'GroupTitle4', 'isCurrent' => 1, 'amount' => 394151, 'bk_code' => 4200, 'createdAt' => '2019-12-24 12:42:00',
                'items' => [
                    '944ddb3d-6cc9-43ad-9ba1-2dffd2ee5b58' => ['title' => 'ItemTitle33', 'amount' => 394151, 'bk_code' => 4213, 'createdAt' => '2019-12-23 12:42:13'],
                ],
            ],
            '9b0c599e-28f8-4974-9ae1-4145f3770f52' => [
                'title' => 'GroupTitle3', 'isCurrent' => 0, 'amount' => 13446, 'bk_code' => 4100, 'createdAt' => '2019-12-27 12:41:00',
                'items' => [
                    '7c707a00-0e95-4ae4-886c-b46678aeb5b5' => ['title' => 'ItemTitle32', 'amount' => 200, 'bk_code' => 4103, 'createdAt' => '2019-12-25 12:41:03'],
                    '515a27c0-ad28-4f08-9ac9-624b5f45c3d6' => ['title' => 'ItemTitle31', 'amount' => 13246, 'bk_code' => 4104, 'createdAt' => '2019-12-26 12:41:04'],
                ],
            ],
            '844db4d1-bdc2-4e1c-a56e-82a166b13afe' => [
                'title' => 'GroupTitle1', 'isCurrent' => 0, 'amount' => 148112, 'bk_code' => 4300, 'createdAt' => '2020-03-01 12:43:00',
                'items' => [
                    '9a453a41-ba5b-45cc-a29a-d6e12cc08fc0' => ['title' => 'ItemTitle12', 'amount' => 57000, 'bk_code' => 4301, 'createdAt' => '2020-03-01 12:43:01'],
                    'a4adb10e-ecfc-4497-9501-72813c5e4b54' => ['title' => 'ItemTitle13', 'amount' => 11420, 'bk_code' => 4303, 'createdAt' => '2020-03-01 12:43:03'],
                    'e7971328-7c62-4117-b82f-75b273ec32c1' => ['title' => 'ItemTitle15', 'amount' => 29292, 'bk_code' => null, 'createdAt' => '2020-01-01 12:00:11'],
                    'fbfa988b-6e74-4a47-b6a0-9068aafcbacb' => ['title' => 'ItemTitle14', 'amount' => 29697, 'bk_code' => null, 'createdAt' => '2020-02-01 12:00:11'],
                    '2bfd6973-2622-49ea-884a-8e18dc15d05e' => ['title' => 'ItemTitle11', 'amount' => 79692, 'bk_code' => null, 'createdAt' => '2020-02-01 12:00:14'],
                ],
            ],
            '1bd92f3a-19d3-4ea2-bc7b-3e791446ddac' => [
                'title' => 'GroupTitle2', 'isCurrent' => 0, 'amount' => 3610, 'bk_code' => 4400, 'createdAt' => '2020-01-01 12:44:00',
                'items' => [
                    '6eb9015e-039f-4028-8b2b-3f3279c8849c' => ['title' => 'ItemTitle23', 'amount' => 346, 'bk_code' => 4402, 'createdAt' => '2019-12-29 12:44:02'],
                    '4bc0d8d9-e8ec-4258-a812-b53c8f7babd7' => ['title' => 'ItemTitle21', 'amount' => 57, 'bk_code' => 4403, 'createdAt' => '2019-12-31 12:44:03'],
                    '57d3c146-0205-470a-9c3a-409e72432681' => ['title' => 'ItemTitle22', 'amount' => 2457, 'bk_code' => 4404, 'createdAt' => '2019-12-30 12:44:04'],
                    'f4c7b564-79e4-4037-9814-ec4bb040c58e' => ['title' => 'ItemTitle24', 'amount' => 750, 'bk_code' => 4407, 'createdAt' => '2019-12-28 12:44:07'],
                ],
            ],
        ];

        $responder = new class { use AccountsListConverter; };
        $reordered_actual = $responder->sortAccountInAscendingCodeOrder($groupedList);

        $this->assertSame($reordered_expected, $reordered_actual);
    }

    /**
     * @test
     */
    public function translateAccountListToTitleList_FormattedAccountListIsReturned()
    {
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $accountId_3 = (string) Str::uuid();
        $accountId_4 = (string) Str::uuid();
        $accountId_5 = (string) Str::uuid();
        $accountId_6 = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accountGroupId_2 = (string) Str::uuid();
        $accountGroupId_3 = (string) Str::uuid();
        $accountGroupId_4 = (string) Str::uuid();
        $accountGroupId_5 = (string) Str::uuid();
        $accounts = [
            'asset' => [
                'groups' => [
                    $accountGroupId_1 => [
                        'title'        => 'accountGroupTitle_1',
                        'isCurrent'    => 0,
                        'bk_code'      => 1200,
                        'createdAt'    => '2019-12-01 12:00:12',
                        'items'        => [
                            $accountId_1 => [
                                'title'    => 'accountTitle_1',
                                'bk_code'  => 1201,
                                'createdAt'=> '2019-12-02 12:00:01',
                            ],
                        ],
                    ],
                ],
            ],
            'liability' => [
                'groups' => [
                    $accountGroupId_2 => [
                        'title'        => 'accountGroupTitle_2',
                        'isCurrent'    => 0,
                        'bk_code'      => 2300,
                        'createdAt'    => '2019-12-01 12:00:23',
                        'items'        => [
                            $accountId_2 => [
                                'title'    => 'accountTitle_2',
                                'bk_code'  => 2302,
                                'createdAt'=> '2019-12-02 12:00:02',
                            ],
                            $accountId_3 => [
                                'title'    => 'accountTitle_3',
                                'bk_code'  => 2303,
                                'createdAt'=> '2019-12-02 12:00:03',
                            ],
                        ],
                    ],
                    $accountGroupId_3 => [
                        'title'        => 'accountGroupTitle_3',
                        'isCurrent'    => 0,
                        'bk_code'      => 2400,
                        'createdAt'    => '2019-12-01 12:00:24',
                        'items'        => [
                            $accountId_4 => [
                                'title'    => 'accountTitle_4',
                                'bk_code'  => 2404,
                                'createdAt'=> '2019-12-02 12:00:04',
                            ],
                        ],
                    ],
                ],
            ],
            'expense' => [
                'groups' => [],
            ],
            'revenue' => [
                'groups' => [
                    $accountGroupId_4 => [
                        'title'        => 'accountGroupTitle_4',
                        'isCurrent'    => 1,
                        'bk_code'      => 5100,
                        'createdAt'    => '2019-12-01 12:00:51',
                        'items'        => [
                            $accountId_5 => [
                                'title'    => 'accountTitle_5',
                                'bk_code'  => 5105,
                                'createdAt'=> '2019-12-02 12:00:06',
                            ],
                        ],
                    ],
                    $accountGroupId_5 => [
                        'title'        => 'accountGroupTitle_5',
                        'isCurrent'    => 1,
                        'bk_code'      => 5200,
                        'createdAt'    => '2019-12-01 12:00:51',
                        'items'        => [
                            $accountId_6 => [
                                'title'    => 'accountTitle_6',
                                'bk_code'  => 5206,
                                'createdAt'=> '2019-12-02 12:00:06',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $formattedAccountList_expected = [
            $accountId_1 => 'accountTitle_1',
            $accountId_2 => 'accountTitle_2',
            $accountId_3 => 'accountTitle_3',
            $accountId_4 => 'accountTitle_4',
            $accountId_5 => 'accountTitle_5',
            $accountId_6 => 'accountTitle_6',
        ];

        $responder = new class { use AccountsListConverter; };
        $formattedAccountList_actual = $responder->translateAccountListToTitleList($accounts);

        $this->assertSame($formattedAccountList_expected, $formattedAccountList_actual);
    }
}
