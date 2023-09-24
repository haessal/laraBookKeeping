<?php

namespace App\Http\Responder\page\v1;

use Illuminate\Http\Response;

class ShowStatementsViewResponder extends BaseViewResponder
{
    /**
     * Respond the ShowStatementsView.
     *
     * @param  array  $context
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
            'navigation'                 => $this->navigationList(),
            'beginning_date'             => $context['beginning_date'],
            'end_date'                   => $context['end_date'],
            'display_statements'         => $context['display_statements'],
            'income_statement'           => $income_statement,
            'trial_balance_of_real_flow' => $trial_balance_of_real_flow,
            'previous_balance_sheet'     => $previous_balance_sheet,
            'balance_sheet'              => $balance_sheet,
            'slips'                      => $formatted_slips,
            'message'                    => $context['message'],
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}
