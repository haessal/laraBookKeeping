<?php

namespace App\Http\Controllers\page\v2;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Service\BookKeepingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RedirectAccountsSettingsActionHtml extends AuthenticatedBookKeepingAction
{
    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping)
    {
        parent::__construct($BookKeeping);
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request, string $bookId): RedirectResponse
    {
        $redirect = redirect()->route('v2_accounts_settings', ['bookId' => $bookId], Response::HTTP_SEE_OTHER);
        $accountsgroup = $request->input('accountsgroup');
        if (isset($accountsgroup)) {
            if (strcmp(strval($accountsgroup), '0') != 0) {
                $redirect = redirect()->route('v2_accounts_groups', ['bookId' => $bookId, 'accountsGroupId' => $accountsgroup], Response::HTTP_SEE_OTHER);
            }
        }
        $accountsitem = $request->input('accountsitem');
        if (isset($accountsitem)) {
            if (strcmp(strval($accountsitem), '0') != 0) {
                $redirect = redirect()->route('v2_accounts_items', ['bookId' => $bookId, 'accountsItemId' => $accountsitem], Response::HTTP_SEE_OTHER);
            }
        }

        return $redirect;
    }
}
