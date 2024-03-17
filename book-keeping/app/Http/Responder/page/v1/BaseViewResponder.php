<?php

namespace App\Http\Responder\page\v1;

use App\Http\Responder\AccountsListConverter;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Response;

class BaseViewResponder
{
    use AccountsListConverter;

    /**
     * Response instance.
     *
     * @var \Illuminate\Http\Response
     */
    protected $response;

    /**
     * View Factory instance.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;

    /**
     * Create a new BaseV1ViewResponder instance.
     *
     * @param  \Illuminate\Http\Response  $response
     * @param  \Illuminate\Contracts\View\Factory  $view
     * @return void
     */
    public function __construct(Response $response, ViewFactory $view)
    {
        $this->response = $response;
        $this->view = $view;
    }

    /**
     * List of Navigation links for version 1.
     *
     * @return array{
     *   link: string,
     *   caption: string,
     * }[]
     */
    public function navigationList(): array
    {
        return [
            ['link' => 'v1_top', 'caption' => strval(__('Top'))],
            ['link' => 'v1_findslips', 'caption' => strval(__('Find Slips'))],
            ['link' => 'v1_slip', 'caption' => strval(__('Slip'))],
            ['link' => 'v1_statements', 'caption' => strval(__('Statements'))],
            ['link' => 'v1_accountslist', 'caption' => strval(__('Accounts List'))],
        ];
    }

    /**
     * Translate account list format for view.
     *
     * @param  array{
     *   asset: array{
     *     groups: array<string, array{
     *       title: string,
     *       isCurrent: bool,
     *       bk_code: int,
     *       createdAt: string,
     *       items: array<string, array{
     *         title: string,
     *         description: string,
     *         selectable: bool,
     *         bk_code: int,
     *         createdAt: string,
     *       }>,
     *     }>,
     *   },
     *   liability: array{
     *     groups: array<string, array{
     *       title: string,
     *       isCurrent: bool,
     *       bk_code: int,
     *       createdAt: string,
     *       items: array<string, array{
     *         title: string,
     *         description: string,
     *         selectable: bool,
     *         bk_code: int,
     *         createdAt: string,
     *       }>,
     *     }>,
     *   },
     *   expense: array{
     *     groups: array<string, array{
     *       title: string,
     *       isCurrent: bool,
     *       bk_code: int,
     *       createdAt: string,
     *       items: array<string, array{
     *         title: string,
     *         description: string,
     *         selectable: bool,
     *         bk_code: int,
     *         createdAt: string,
     *       }>,
     *     }>,
     *   },
     *   revenue: array{
     *     groups: array<string, array{
     *       title: string,
     *       isCurrent: bool,
     *       bk_code: int,
     *       createdAt: string,
     *       items: array<string, array{
     *         title: string,
     *         description: string,
     *         selectable: bool,
     *         bk_code: int,
     *         createdAt: string,
     *       }>,
     *     }>,
     *   },
     * }  $accounts
     * @return array<string, array{
     *   code: string|int,
     *   type: string,
     *   group_title: string,
     *   title: string,
     *   description: string,
     *   evenOdd: string,
     * }>
     */
    public function translateAccountsListFormat(array $accounts): array
    {
        $account_list = [];
        $accountTypeTitle = [
            'asset' => __('Assets'),
            'liability' => __('Liabilities'),
            'expense' => __('Expense'),
            'revenue' => __('Revenue'),
        ];
        $tableRowEvenOrOdd = 'evn';
        foreach ($accounts as $accountTypeKey => $accountType) {
            foreach ($accountType['groups'] as $accountGroupItem) {
                foreach ($accountGroupItem['items'] as $accountId => $accountItem) {
                    if ($accountItem['bk_code'] == 0) {
                        $code = '-';
                    } else {
                        $code = intval($accountItem['bk_code']);
                    }
                    $account_list[$accountId] = [
                        'code' => $code,
                        'type' => strval($accountTypeTitle[$accountTypeKey]),
                        'group_title' => strval($accountGroupItem['title']),
                        'title' => strval($accountItem['title']),
                        'description' => strval($accountItem['description']),
                        'evenOdd' => $tableRowEvenOrOdd,
                    ];
                    if ($tableRowEvenOrOdd == 'evn') {
                        $tableRowEvenOrOdd = 'odd';
                    } else {
                        $tableRowEvenOrOdd = 'evn';
                    }
                }
            }
        }

        return $account_list;
    }

    /**
     * Translate balance sheet format for view.
     *
     * @param  array{
     *   asset: array{
     *     amount: int,
     *     groups: array<string, array{
     *       title: string,
     *       isCurrent: bool,
     *       amount: int,
     *       bk_code: int,
     *       createdAt: string,
     *       items: array<string, array{
     *         title: string,
     *         amount: int,
     *         description: string,
     *         selectable: bool,
     *         bk_code: int,
     *         createdAt: string,
     *       }>,
     *     }>,
     *   },
     *   liability: array{
     *     amount: int,
     *     groups: array<string, array{
     *       title: string,
     *       isCurrent: bool,
     *       amount: int,
     *       bk_code: int,
     *       createdAt: string,
     *       items: array<string, array{
     *         title: string,
     *         amount: int,
     *         description: string,
     *         selectable: bool,
     *         bk_code: int,
     *         createdAt: string,
     *       }>,
     *     }>,
     *   },
     *   current_net_asset?: array{
     *     amount: int,
     *   },
     *   net_asset: array{
     *     amount: int,
     *   },
     * }  $statements
     * @return array{
     *   debit: array{
     *     title: string,
     *     amount: string,
     *     bold: bool,
     *     italic: bool,
     *   },
     *   credit: array{
     *     title: string,
     *     amount: string,
     *     bold: bool,
     *     italic: bool,
     *   },
     * }[]
     */
    public function translateBalanceSheetFormat(array $statements): array
    {
        return $this->translateStatementsFormat($statements, [
            'debitTitle' => 'Assets',
            'debitGroup' => 'asset',
            'creditTitle' => 'Liabilities',
            'creditGroup' => 'liability',
            'displayCurrentNetAsset' => true,
        ]);
    }

    /**
     * Translate draft slips format for view.
     *
     * @param  array<string, array{
     *   date: string,
     *   slip_outline: string,
     *   slip_memo: string,
     *   items: array<string, array{
     *     debit: array{
     *       account_id: string,
     *       account_title: string,
     *     },
     *     credit: array{
     *       account_id: string,
     *       account_title: string,
     *     },
     *     amount: int,
     *     client: string,
     *     outline: string,
     *   }>,
     * }>  $slip
     * @return array<string, array{
     *   no: non-falsy-string,
     *   debit: string,
     *   client: string,
     *   outline: string,
     *   credit: string,
     *   amount: int,
     *   evenOdd: string,
     * }>|array{}
     */
    public function translateDraftSlipFormat(array $slip): array
    {
        $formatted = [];
        $slipId = key($slip);
        $tableRowEvenOrOdd = 'evn';
        foreach ($slip[$slipId]['items'] as $slipEntryId => $slipEntryItem) {
            $formatted[$slipEntryId] = [
                'no' => substr($slipEntryId, 0, 6).'..',
                'debit' => $slipEntryItem['debit']['account_title'],
                'client' => $slipEntryItem['client'],
                'outline' => $slipEntryItem['outline'],
                'credit' => $slipEntryItem['credit']['account_title'],
                'amount' => $slipEntryItem['amount'],
                'evenOdd' => $tableRowEvenOrOdd,
            ];
            if ($tableRowEvenOrOdd == 'evn') {
                $tableRowEvenOrOdd = 'odd';
            } else {
                $tableRowEvenOrOdd = 'evn';
            }
        }

        return $formatted;
    }

    /**
     * Translate income statement format for view.
     *
     * @param  array{
     *   expense: array{
     *     amount: int,
     *     groups: array<string, array{
     *       title: string,
     *       isCurrent: bool,
     *       amount: int,
     *       bk_code: int,
     *       createdAt: string,
     *       items: array<string, array{
     *         title: string,
     *         amount: int,
     *         description: string,
     *         selectable: bool,
     *         bk_code: int,
     *         createdAt: string,
     *       }>,
     *     }>,
     *   },
     *   revenue: array{
     *     amount: int,
     *     groups: array<string, array{
     *       title: string,
     *       isCurrent: bool,
     *       amount: int,
     *       bk_code: int,
     *       createdAt: string,
     *       items: array<string, array{
     *         title: string,
     *         amount: int,
     *         description: string,
     *         selectable: bool,
     *         bk_code: int,
     *         createdAt: string,
     *       }>,
     *     }>,
     *   },
     *   net_income: array{
     *     amount: int,
     *   },
     * }  $statements
     * @return array{
     *   debit: array{
     *     title: string,
     *     amount: string,
     *     bold: bool,
     *     italic: bool,
     *   },
     *   credit: array{
     *     title: string,
     *     amount: string,
     *     bold: bool,
     *     italic: bool,
     *   },
     * }[]
     */
    public function translateIncomeStatementFormat(array $statements): array
    {
        return $this->translateStatementsFormat($statements, [
            'debitTitle' => 'Expense',
            'debitGroup' => 'expense',
            'creditTitle' => 'Revenue',
            'creditGroup' => 'revenue',
            'displayCurrentNetAsset' => false,
        ]);
    }

    /**
     * Translate slips format for view.
     *
     * @param  array<string, array{
     *   date: string,
     *   slip_outline: string,
     *   slip_memo: string,
     *   items: array<string, array{
     *     debit: array{
     *       account_id: string,
     *       account_title: string,
     *     },
     *     credit: array{
     *       account_id: string,
     *       account_title: string,
     *     },
     *     amount: int,
     *     client: string,
     *     outline: string,
     *   }>,
     * }>  $slips
     * @return array<string, array{
     *   no: non-falsy-string,
     *   slipNo: non-falsy-string,
     *   date: string,
     *   debit: string,
     *   credit: string,
     *   amount: string,
     *   client: string,
     *   outline: string,
     * }>
     */
    public function translateSlipsFormat(array $slips): array
    {
        $slipentryline = [];
        foreach ($slips as $slipId => $slip) {
            foreach ($slip['items'] as $slipEntryId => $slipEntry) {
                $slipentryline[$slipEntryId] = [
                    'no' => substr($slipEntryId, 0, 6).'..',
                    'slipNo' => substr($slipId, 0, 6).'..',
                    'date' => $slip['date'],
                    'debit' => $slipEntry['debit']['account_title'],
                    'credit' => $slipEntry['credit']['account_title'],
                    'amount' => number_format($slipEntry['amount']),
                    'client' => $slipEntry['client'],
                    'outline' => $slipEntry['outline'],
                ];
            }
        }

        return $slipentryline;
    }

    /**
     * Translate statements format for view.
     *
     * @param  array{
     *   asset?: array{
     *     amount: int,
     *     groups: array<string, array{
     *       title: string,
     *       isCurrent: bool,
     *       amount: int,
     *       bk_code: int,
     *       createdAt: string,
     *       items: array<string, array{
     *         title: string,
     *         amount: int,
     *         description: string,
     *         selectable: bool,
     *         bk_code: int,
     *         createdAt: string,
     *       }>,
     *     }>,
     *   },
     *   liability?: array{
     *     amount: int,
     *     groups: array<string, array{
     *       title: string,
     *       isCurrent: bool,
     *       amount: int,
     *       bk_code: int,
     *       createdAt: string,
     *       items: array<string, array{
     *         title: string,
     *         amount: int,
     *         description: string,
     *         selectable: bool,
     *         bk_code: int,
     *         createdAt: string,
     *       }>,
     *     }>,
     *   },
     *   expense?: array{
     *     amount: int,
     *     groups: array<string, array{
     *       title: string,
     *       isCurrent: bool,
     *       amount: int,
     *       bk_code: int,
     *       createdAt: string,
     *       items: array<string, array{
     *         title: string,
     *         amount: int,
     *         description: string,
     *         selectable: bool,
     *         bk_code: int,
     *         createdAt: string,
     *       }>,
     *     }>,
     *   },
     *   revenue?: array{
     *     amount: int,
     *     groups: array<string, array{
     *       title: string,
     *       isCurrent: bool,
     *       amount: int,
     *       bk_code: int,
     *       createdAt: string,
     *       items: array<string, array{
     *         title: string,
     *         amount: int,
     *         description: string,
     *         selectable: bool,
     *         bk_code: int,
     *         createdAt: string,
     *       }>,
     *     }>,
     *   },
     *   current_net_asset?: array{
     *     amount: int,
     *   },
     *   net_asset?: array{
     *     amount: int,
     *   },
     *   net_income?: array{
     *     amount: int,
     *   },
     * }  $statements
     * @param  array{
     *   debitTitle: 'Assets'|'Expense',
     *   debitGroup: 'asset'|'expense',
     *   creditTitle: 'Liabilities'|'Revenue',
     *   creditGroup:'liability'|'revenue',
     *   displayCurrentNetAsset: bool,
     * }  $parameters
     * @return array{
     *   debit: array{
     *     title: string,
     *     amount: string,
     *     bold: bool,
     *     italic: bool,
     *   },
     *   credit: array{
     *     title: string,
     *     amount: string,
     *     bold: bool,
     *     italic: bool,
     *   },
     * }[]
     */
    private function translateStatementsFormat(array $statements, array $parameters): array
    {
        $debitTitle = $parameters['debitTitle'];
        $debitGroup = $parameters['debitGroup'];
        $creditTitle = $parameters['creditTitle'];
        $creditGroup = $parameters['creditGroup'];
        $displayCurrentNetAsset = $parameters['displayCurrentNetAsset'];

        $debitcreditline = [];
        $debit_count = 0;
        $credit_count = 0;

        if (array_key_exists($debitGroup, $statements)) {
            $debitcreditline[$debit_count++]['debit'] = [
                'title' => strval(__($debitTitle)),
                'amount' => number_format($statements[$debitGroup]['amount']),
                'bold' => true,
                'italic' => true,
            ];
            foreach ($statements[$debitGroup]['groups'] as $key => $group) {
                $debitcreditline[$debit_count++]['debit'] = [
                    'title' => strval($group['title']),
                    'amount' => number_format($group['amount']),
                    'bold' => false,
                    'italic' => true,
                ];
                foreach ($group['items'] as $key => $item) {
                    $debitcreditline[$debit_count++]['debit'] = [
                        'title' => strval($item['title']),
                        'amount' => number_format($item['amount']),
                        'bold' => false,
                        'italic' => false,
                    ];
                }
            }
        }

        if ($debitTitle == 'Expense') {
            $debitcreditline[$debit_count++]['debit']
                = ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false];
            if (array_key_exists('net_income', $statements)) {
                $debitcreditline[$debit_count++]['debit'] = [
                    'title' => strval(__('Net Income')),
                    'amount' => number_format($statements['net_income']['amount']),
                    'bold' => true,
                    'italic' => true,
                ];
            }
        }

        if (array_key_exists($creditGroup, $statements)) {
            $debitcreditline[$credit_count++]['credit'] = [
                'title' => strval(__($creditTitle)),
                'amount' => number_format($statements[$creditGroup]['amount']),
                'bold' => true,
                'italic' => true,
            ];
            foreach ($statements[$creditGroup]['groups'] as $key => $group) {
                $debitcreditline[$credit_count++]['credit'] = [
                    'title' => strval($group['title']),
                    'amount' => number_format($group['amount']),
                    'bold' => false,
                    'italic' => true,
                ];
                foreach ($group['items'] as $key => $item) {
                    $debitcreditline[$credit_count++]['credit'] = [
                        'title' => strval($item['title']),
                        'amount' => number_format($item['amount']),
                        'bold' => false,
                        'italic' => false,
                    ];
                }
            }
        }

        if ($creditTitle == 'Liabilities') {
            $debitcreditline[$credit_count++]['credit']
                = ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false];
            if ($displayCurrentNetAsset && array_key_exists('current_net_asset', $statements)) {
                $debitcreditline[$credit_count++]['credit'] = [
                    'title' => strval(__('Current Net Asset')),
                    'amount' => number_format($statements['current_net_asset']['amount']),
                    'bold' => true,
                    'italic' => true,
                ];
            }
            if (array_key_exists('net_asset', $statements)) {
                $debitcreditline[$credit_count++]['credit'] = [
                    'title' => strval(__('Net Asset')),
                    'amount' => number_format($statements['net_asset']['amount']),
                    'bold' => true,
                    'italic' => true,
                ];
            }
        }

        $returnDebitCreditLine = [];
        foreach ($debitcreditline as $line) {
            $debit = ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false];
            if (array_key_exists('debit', $line)) {
                $debit = $line['debit'];
            }
            $credit = ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false];
            if (array_key_exists('credit', $line)) {
                $credit = $line['credit'];
            }
            $returnDebitCreditLine[] = ['debit' => $debit, 'credit' => $credit];
        }

        return $returnDebitCreditLine;
    }
}
