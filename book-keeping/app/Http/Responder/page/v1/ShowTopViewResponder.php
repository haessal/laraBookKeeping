<?php

namespace App\Http\Responder\page\v1;

use Illuminate\Http\Response;

class ShowTopViewResponder extends BaseViewResponder
{
    /**
     * Response the Books list and Form to create new Book.
     *
     * @param  array{
     *   date: string,
     *   income_statement: array{
     *     expense: array{
     *       amount: int,
     *       groups: array<string, array{
     *         title: string,
     *         isCurrent: bool,
     *         amount: int,
     *         bk_code: int,
     *         createdAt: string,
     *         items: array<string, array{
     *           title: string,
     *           amount: int,
     *           bk_code: int,
     *           createdAt: string,
     *         }>,
     *       }>|array{},
     *     },
     *     revenue: array{
     *       amount: int,
     *       groups: array<string, array{
     *         title: string,
     *         isCurrent: bool,
     *         amount: int,
     *         bk_code: int,
     *         createdAt: string,
     *         items: array<string, array{
     *           title: string,
     *           amount: int,
     *           bk_code: int,
     *           createdAt: string,
     *         }>,
     *       }>|array{},
     *     },
     *     net_income: array{
     *       amount: int,
     *     },
     *   },
     *   balance_sheet: array{
     *     asset: array{
     *       amount: int,
     *       groups: array<string, array{
     *         title: string,
     *         isCurrent: bool,
     *         amount: int,
     *         bk_code: int,
     *         createdAt: string,
     *         items: array<string, array{
     *           title: string,
     *           amount: int,
     *           bk_code: int,
     *           createdAt: string,
     *         }>,
     *       }>|array{},
     *     },
     *     liability: array{
     *       amount: int,
     *       groups: array<string, array{
     *         title: string,
     *         isCurrent: bool,
     *         amount: int,
     *         bk_code: int,
     *         createdAt: string,
     *         items: array<string, array{
     *           title: string,
     *           amount: int,
     *           bk_code: int,
     *           createdAt: string,
     *         }>,
     *       }>|array{},
     *     },
     *     current_net_asset: array{
     *       amount: int,
     *     },
     *     net_asset: array{
     *       amount: int,
     *     },
     *   },
     *   slips: array<string, array{
     *     date: string,
     *     slip_outline: string,
     *     slip_memo: string,
     *     items: array<string, array{
     *       debit: array{
     *         account_id: string,
     *         account_title: string,
     *       },
     *       credit: array{
     *         account_id: string,
     *         account_title: string,
     *       },
     *       amount: int,
     *       client: string,
     *       outline: string,
     *     }>,
     *   }>|array{},
     * }  $context
     * @return \Illuminate\Http\Response
     */
    public function response(array $context): Response
    {
        $incomeStatement = $context['income_statement'];
        $balanceSheet = $context['balance_sheet'];
        $slips = $context['slips'];
        $this->response->setContent($this->view->make('bookkeeping.v1.pagetop', [
            'navigation' => $this->navigationList(),
            'date' => $context['date'],
            'income_statement' => $this->translateIncomeStatementFormat([
                'expense' => [
                    'amount' => $incomeStatement['expense']['amount'],
                    'groups' => $this->sortAccountInAscendingCodeOrder($incomeStatement['expense']['groups']),
                ],
                'revenue' => [
                    'amount' => $incomeStatement['revenue']['amount'],
                    'groups' => $this->sortAccountInAscendingCodeOrder($incomeStatement['revenue']['groups']),
                ],
                'net_income' => $incomeStatement['net_income'],
            ]),
            'balance_sheet' => $this->translateBalanceSheetFormat([
                'asset' => [
                    'amount' => $balanceSheet['asset']['amount'],
                    'groups' => $this->sortAccountInAscendingCodeOrder($balanceSheet['asset']['groups']),
                ],
                'liability' => [
                    'amount' => $balanceSheet['liability']['amount'],
                    'groups' => $this->sortAccountInAscendingCodeOrder($balanceSheet['liability']['groups']),
                ],
                'current_net_asset' => $balanceSheet['current_net_asset'],
                'net_asset' => $balanceSheet['net_asset'],
            ]),
            'slips' => $this->translateSlipsFormat($slips),
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}
