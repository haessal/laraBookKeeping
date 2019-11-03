<?php

namespace App\Http\Responder\v1;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Response;

class BaseViewResponder
{
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
     * @param \Illuminate\Http\Response          $response
     * @param \Illuminate\Contracts\View\Factory $view
     */
    public function __construct(Response $response, ViewFactory $view)
    {
        $this->response = $response;
        $this->view = $view;
    }

    /**
     * List of Navigation links for version 1.
     *
     * @return array
     */
    public function navilinks() : array
    {
        return [
            ['link' => 'v1_top', 'caption' => __('Top')],
        ];
    }

    public function sortAccountInAscendingCodeOrder(array $groupedList) : array
    {
        return $this->sortAccountGrouptListInAscendingCodeOrder($groupedList);
    }

    public function translateBalanceSheetFormat(array $statements) : array
    {
        return $this->translateStatementsFormat($statements, [
            'debitTitle'             => 'Assets',
            'debitGroup'             => 'asset',
            'creditTitle'            => 'Liabilities',
            'creditGroup'            => 'liability',
            'displayCurrentNetAsset' => true,
        ]);
    }

    public function translateIncomeStatementFormat(array $statements) : array
    {
        return $this->translateStatementsFormat($statements, [
            'debitTitle'             => 'Expense',
            'debitGroup'             => 'expense',
            'creditTitle'            => 'Revenue',
            'creditGroup'            => 'revenue',
            'displayCurrentNetAsset' => false,
        ]);
    }

    public function translateSlipsFormat(array $slips) : array
    {
        $slipentryline = [];
        foreach ($slips as $slipId => $slip) {
            foreach ($slip['items'] as $slipEntryId => $slipEntry) {
                $slipentryline[] = [
                    'no'      => substr($slipEntryId, 1, 6).'..',
                    'slipno'  => substr($slipId, 1, 6).'..',
                    'date'    => $slip['date'],
                    'debit'   => $slipEntry['debit']['account_title'],
                    'credit'  => $slipEntry['credit']['account_title'],
                    'amount'  => number_format($slipEntry['amount']),
                    'client'  => $slipEntry['client'],
                    'outline' => $slipEntry['outline'],
                ];
            }
        }

        return $slipentryline;
    }

    private function getIdsSortedInAscendingOrder(array $listWithKeyword, string $keyword, string $isCurrent = 'isCurrent') : array
    {
        $sortedIds1 = [];
        $sortedIds2 = [];
        foreach ($listWithKeyword as $Ids => $item) {
            if (($keyword == 'bk_code') && is_null($item[$keyword])) {
                $value = 9999;
            } else {
                $value = $item[$keyword];
            }

            if (array_key_exists($isCurrent, $item)) {
                if ($item[$isCurrent]) {
                    $sortedIds1[$Ids] = $value;
                } else {
                    $sortedIds2[$Ids] = $value;
                }
            } else {
                $sortedIds2[$Ids] = $value;
            }
        }
        asort($sortedIds1);
        asort($sortedIds2);

        return $sortedIds1 + $sortedIds2;
    }

    private function sortAccountGrouptListInAscendingCodeOrder(array $groupedList) : array
    {
        $reordered = [];
        $sortedKeys = $this->getIdsSortedInAscendingOrder($groupedList, 'bk_code');
        foreach ($sortedKeys as $groupId => $keyword) {
            $reordered[$groupId] = [
                'title'     => $groupedList[$groupId]['title'],
                'isCurrent' => $groupedList[$groupId]['isCurrent'],
                'amount'    => $groupedList[$groupId]['amount'],
                'items'     => $this->sortAccountListInAscendingCodeOrder($groupedList[$groupId]['items']),
            ];
        }

        return $reordered;
    }

    private function sortAccountListInAscendingCodeOrder(array $list) : array
    {
        $reordered = [];
        $sortedKeys = $this->getIdsSortedInAscendingOrder($list, 'bk_code');
        foreach ($sortedKeys as $id => $keyword) {
            $reordered[$id] = [
                'title'     => $list[$id]['title'],
                'amount'    => $list[$id]['amount'],
            ];
        }

        return $reordered;
    }

    private function translateStatementsFormat(array $statements, array $parameters) : array
    {
        $debitTitle = $parameters['debitTitle'];
        $debitGroup = $parameters['debitGroup'];
        $creditTitle = $parameters['creditTitle'];
        $creditGroup = $parameters['creditGroup'];
        $displayCurrentNetAsset = $parameters['displayCurrentNetAsset'];

        $debitcreditline = [];
        $debit_count = 0;
        $credit_count = 0;

        $debitcreditline[$debit_count++]['debit'] = [
            'title'  => __($debitTitle),
            'amount' => number_format($statements[$debitGroup]['amount']),
            'bold'   => true,
            'italic' => true,
        ];
        foreach ($statements[$debitGroup]['groups'] as $key => $group) {
            $debitcreditline[$debit_count++]['debit'] = [
                'title'  => $group['title'],
                'amount' => number_format($group['amount']),
                'bold'   => false,
                'italic' => true,
            ];
            foreach ($group['items'] as $key => $item) {
                $debitcreditline[$debit_count++]['debit'] = [
                    'title'  => $item['title'],
                    'amount' => number_format($item['amount']),
                    'bold'   => false,
                    'italic' => false,
                ];
            }
        }
        if ($debitTitle == 'Expense') {
            $debitcreditline[$debit_count++]['debit'] = ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false];
            $debitcreditline[$debit_count++]['debit'] = [
                'title'  => __('Net Income'),
                'amount' => number_format($statements['net_income']['amount']),
                'bold'   => true,
                'italic' => true,
            ];
        }

        $debitcreditline[$credit_count++]['credit'] = [
            'title'  => __($creditTitle),
            'amount' => number_format($statements[$creditGroup]['amount']),
            'bold'   => true,
            'italic' => true,
        ];
        foreach ($statements[$creditGroup]['groups'] as $key => $group) {
            $debitcreditline[$credit_count++]['credit'] = [
                'title'  => $group['title'],
                'amount' => number_format($group['amount']),
                'bold'   => false,
                'italic' => true,
            ];
            foreach ($group['items'] as $key => $item) {
                $debitcreditline[$credit_count++]['credit'] = [
                    'title'  => $item['title'],
                    'amount' => number_format($item['amount']),
                    'bold'   => false,
                    'italic' => false,
                ];
            }
        }
        if ($creditTitle == 'Liabilities') {
            $debitcreditline[$credit_count++]['credit'] = ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false];
            if ($displayCurrentNetAsset) {
                $debitcreditline[$credit_count++]['credit'] = [
                    'title'  => __('Current Net Asset'),
                    'amount' => number_format($statements['current_net_asset']['amount']),
                    'bold'   => true,
                    'italic' => true,
                ];
            }
            $debitcreditline[$credit_count++]['credit'] = [
                'title'  => __('Net Asset'),
                'amount' => number_format($statements['net_asset']['amount']),
                'bold'   => true,
                'italic' => true,
            ];
        }

        while ($debit_count != $credit_count) {
            if ($debit_count < $credit_count) {
                $debitcreditline[$debit_count++]['debit'] = ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false];
            } else {
                $debitcreditline[$credit_count++]['credit'] = ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false];
            }
        }

        return $debitcreditline;
    }
}
