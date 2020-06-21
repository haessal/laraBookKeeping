<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;

class RedirectAccountsSettingsActionHtml extends AuthenticatedBookKeepingAction
{
    /**
     * Create a new controller instance.
     *
     * @param \App\Service\BookKeepingService $BookKeeping
     *
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping)
    {
        parent::__construct($BookKeeping);
    }

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request, string $bookId): RedirectResponse
    {
        $book = $this->BookKeeping->retrieveBookInfomation($bookId);
        $redirect = redirect()->route('v2_accounts_settings', ['bookId' => $book['id']], Response::HTTP_SEE_OTHER);
        $accountsgroup = $request->input('accountsgroup');
        if (!is_null($accountsgroup)) {
            if (strcmp($accountsgroup, "0") != 0) {
                $redirect = redirect()->route('v2_accounts_groups', ['bookId' => $book['id'], 'accountsGroupId' => $accountsgroup], Response::HTTP_SEE_OTHER);
            }
        }
        $accountsitem = $request->input('accountsitem');
        if (!is_null($accountsitem)) {
            if (strcmp($accountsitem, "0") != 0) {
                $redirect = redirect()->route('v2_accounts_items', ['bookId' => $book['id'], 'accountsItemId' => $accountsitem], Response::HTTP_SEE_OTHER);
            }
        }

        return $redirect;
    }
}
