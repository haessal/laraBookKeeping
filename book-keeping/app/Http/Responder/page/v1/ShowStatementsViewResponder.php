<?php

namespace App\Http\Responder\page\v1;

use Illuminate\Http\Response;

class ShowStatementsViewResponder extends BaseViewResponder
{
    /**
     * Respond the ShowStatementsView.
     *
     * @param array{
     *   beginning_date: string,
     *   end_date: string,
     *   profit_loss: array{
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
     *   trial_balance: array{
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
     *   previous_balance_sheet: array{
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
     *   message: null,
     *   display_statements: true,
     * }|array{
     *   beginning_date: string,
     *   end_date: string,
     *   profit_loss: null,
     *   trial_balance: null,
     *   previous_balance_sheet: null,
     *   balance_sheet: null,
     *   slips: null,
     *   message: string,
     *   display_statements: false,
     * }  $context
     * @return \Illuminate\Http\Response
     */
    public function response(array $context): Response
    {
        $income_statement = [];
        $trial_balance_of_real_flow = [];
        $previous_balance_sheet = [];
        $balance_sheet = [];
        $formatted_slips = [];
        if ($context['display_statements']) {
            $profitLoss = $context['profit_loss'];
            $trialBalance = $context['trial_balance'];
            $PreviousBalanceSheet = $context['previous_balance_sheet'];
            $balanceSheet = $context['balance_sheet'];
            $slips = $context['slips'];
            $income_statement = $this->translateIncomeStatementFormat([
                'expense' => [
                    'amount' => $profitLoss['expense']['amount'],
                    'groups' => $this->sortAccountInAscendingCodeOrder($profitLoss['expense']['groups']),
                ],
                'revenue' => [
                    'amount' => $profitLoss['revenue']['amount'],
                    'groups' => $this->sortAccountInAscendingCodeOrder($profitLoss['revenue']['groups']),
                ],
                'net_income' => $profitLoss['net_income'],
            ]);
            $trial_balance_of_real_flow = $this->translateBalanceSheetFormat([
                'asset' => [
                    'amount' => $trialBalance['asset']['amount'],
                    'groups' => $this->sortAccountInAscendingCodeOrder($trialBalance['asset']['groups']),
                ],
                'liability' => [
                    'amount' => $trialBalance['liability']['amount'],
                    'groups' => $this->sortAccountInAscendingCodeOrder($trialBalance['liability']['groups']),
                ],
                'net_asset' => $trialBalance['net_asset'],
            ]);
            $previous_balance_sheet = $this->translateBalanceSheetFormat([
                'asset' => [
                    'amount' => $PreviousBalanceSheet['asset']['amount'],
                    'groups' => $this->sortAccountInAscendingCodeOrder($PreviousBalanceSheet['asset']['groups']),
                ],
                'liability' => [
                    'amount' => $PreviousBalanceSheet['liability']['amount'],
                    'groups' => $this->sortAccountInAscendingCodeOrder($PreviousBalanceSheet['liability']['groups']),
                ],
                'net_asset' => $PreviousBalanceSheet['net_asset'],
            ]);
            $balance_sheet = $this->translateBalanceSheetFormat([
                'asset' => [
                    'amount' => $balanceSheet['asset']['amount'],
                    'groups' => $this->sortAccountInAscendingCodeOrder($balanceSheet['asset']['groups']),
                ],
                'liability' => [
                    'amount' => $balanceSheet['liability']['amount'],
                    'groups' => $this->sortAccountInAscendingCodeOrder($balanceSheet['liability']['groups']),
                ],
                'net_asset' => $balanceSheet['net_asset'],
            ]);
            $formatted_slips = $this->translateSlipsFormat($slips);
        }
        $this->response->setContent($this->view->make('bookkeeping.v1.pagestatements', [
            'navigation' => $this->navigationList(),
            'beginning_date' => $context['beginning_date'],
            'end_date' => $context['end_date'],
            'display_statements' => $context['display_statements'],
            'income_statement' => $income_statement,
            'trial_balance_of_real_flow' => $trial_balance_of_real_flow,
            'previous_balance_sheet' => $previous_balance_sheet,
            'balance_sheet' => $balance_sheet,
            'slips' => $formatted_slips,
            'message' => $context['message'],
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}
