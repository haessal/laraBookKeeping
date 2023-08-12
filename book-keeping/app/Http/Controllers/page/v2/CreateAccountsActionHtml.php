<?php

namespace App\Http\Controllers\page\v2;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\page\v2\CreateAccountsViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CreateAccountsActionHtml extends AuthenticatedBookKeepingAction
{
    /**
     * CreateAccountsView responder instance.
     *
     * @var \App\Http\Responder\v2\CreateAccountsGroupViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\v2\CreateAccountsViewResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, CreateAccountsViewResponder $responder)
    {
        parent::__construct($BookKeeping);
        $this->responder = $responder;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, string $bookId): Response
    {
        $context = [];

        $context['book'] = $this->BookKeeping->retrieveBookInfomation($bookId);
        $context['accounts'] = $this->BookKeeping->retrieveAccounts(false, $bookId);
        $context['accounttype'] = null;
        $context['accountcreate'] = [
            'grouptitle'  => null,
            'groupid'     => null,
            'itemtitle'   => null,
            'description' => null,
        ];
        if ($request->isMethod('post')) {
            $button_action = $request->input('create');
            switch ($button_action) {
                case 'group':
                    $result = $this->validateAndTrimForCreateAccountGroup($request->all(), $bookId);
                    $accountGroup = $result['accountGroup'];
                    if ($result['success']) {
                        $this->BookKeeping->createAccountGroup($accountGroup['accounttype'], $accountGroup['title'], $bookId);
                    } else {
                        $context['accounttype'] = $accountGroup['accounttype'];
                        $context['accountcreate']['grouptitle'] = $accountGroup['title'];
                    }
                    break;
                case 'item':
                    $result = $this->validateAndTrimForCreateAccount($request->all(), $bookId);
                    $account = $result['account'];
                    if ($result['success']) {
                        $this->BookKeeping->createAccount($account['accountgroup'], $account['title'], $account['description'], $bookId);
                    } else {
                        $context['accountcreate']['groupid'] = $account['accountgroup'];
                        $context['accountcreate']['itemtitle'] = $account['title'];
                        $context['accountcreate']['description'] = $account['description'];
                    }
                    break;
                default:
                    break;
            }
        }

        return $this->responder->response($context);
    }

    /**
     * Validate arguments and trim string data for create Account.
     *
     * @param  array  $account_in
     * @param  string  $bookId
     * @return array
     */
    private function validateAndTrimForCreateAccount(array $account_in, string $bookId): array
    {
        $success = true;
        $trimmed_account = [];

        $accountGroupId = trim($account_in['accountgroup']);
        if (! empty($accountGroupId)) {
            $trimmed_account['accountgroup'] = $accountGroupId;
        } else {
            $success = false;
            $trimmed_account['accountgroup'] = null;
        }
        $title = trim($account_in['title']);
        if (! empty($title)) {
            $trimmed_account['title'] = $title;
        } else {
            $success = false;
            $trimmed_account['title'] = null;
        }
        $description = trim($account_in['description']);
        if (! empty($description)) {
            $trimmed_account['description'] = $description;
        } else {
            $success = false;
            $trimmed_account['description'] = null;
        }

        return ['success' => $success, 'account' => $trimmed_account];
    }

    /**
     * Validate arguments and trim string data for create AccountGroup.
     *
     * @param  array  $accountGroup_in
     * @param  string  $bookId
     * @return array
     */
    private function validateAndTrimForCreateAccountGroup(array $accountGroup_in, string $bookId): array
    {
        $success = true;
        $trimmed_accountGroup = [];

        if (array_key_exists('accounttype', $accountGroup_in)) {
            $accountType = trim($accountGroup_in['accounttype']);
            switch ($accountType) {
                case 'asset':
                case 'liability':
                case 'expense':
                case 'revenue':
                    $trimmed_accountGroup['accounttype'] = $accountType;
                    break;
                default:
                    $success = false;
                    $trimmed_accountGroup['accounttype'] = null;
                    break;
            }
        } else {
            $success = false;
            $trimmed_accountGroup['accounttype'] = null;
        }
        $title = trim($accountGroup_in['title']);
        if (! empty($title)) {
            $trimmed_accountGroup['title'] = $title;
        } else {
            $success = false;
            $trimmed_accountGroup['title'] = null;
        }

        return ['success' => $success, 'accountGroup' => $trimmed_accountGroup];
    }
}
