<?php

namespace App\Http\Responder\v2;

use App\Http\Responder\AccountsListConverter;

class BaseAccountsViewResponder extends BaseViewResponder
{
    use AccountsListConverter;

    /**
     * List of Sub-navigation links for accounts.
     *
     * @return array
     */
    public function accountsnavilinks(): array
    {
        return [
            ['name' => 'dummy_head', 'link' => null, 'caption' => ''],
            ['name' => 'list', 'link' => 'v2_accounts', 'caption' => __('List')],
            ['name' => 'create', 'link' => 'v2_accounts_new', 'caption' => __('Add')],
            ['name' => 'settings', 'link' => 'v2_accounts_settings', 'caption' => __('Advanced Setting')],
            ['name' => 'dummy_tail', 'link' => null, 'caption' => ''],
        ];
    }
}
