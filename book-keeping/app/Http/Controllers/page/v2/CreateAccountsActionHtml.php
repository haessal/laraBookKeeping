<?php

namespace App\Http\Controllers\page\v2;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\page\v2\CreateAccountsViewResponder;
use App\Service\AccountService;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CreateAccountsActionHtml extends AuthenticatedBookKeepingAction
{
    /**
     * CreateAccountsView responder instance.
     *
     * @var \App\Http\Responder\page\v2\CreateAccountsViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\page\v2\CreateAccountsViewResponder  $responder
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
     * @param  string  $bookId
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, string $bookId): Response
    {
        $context = [];

        if (! $this->BookKeeping->validateUuid($bookId)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        [$status, $information] = $this->BookKeeping->retrieveBookInformation($bookId);
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                if (isset($information)) {
                    $context['bookId'] = $bookId;
                    $context['book'] = $information;
                } else {
                    abort(Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                break;
            case BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE:
                abort(Response::HTTP_NOT_FOUND);
            default:
                abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $context['accounttype'] = null;
        $context['accountcreate'] = [
            'grouptitle' => null,
            'groupId' => null,
            'itemtitle' => null,
            'description' => null,
        ];
        $context['messages'] = [
            'group' => null,
            'item' => null,
        ];
        if ($request->isMethod('post')) {
            $button_action = $request->input('create');
            switch ($button_action) {
                case 'group':
                    $result = $this->validateAndTrimForCreateAccountGroup($request->all());
                    $accountGroup = $result['accountGroup'];
                    $context['accounttype'] = strval($accountGroup['accounttype']);
                    $context['accountcreate']['grouptitle'] = strval($accountGroup['title']);
                    if ($result['success']) {
                        [$status, $_] = $this->BookKeeping->createAccountGroup(
                            $accountGroup['accounttype'], $accountGroup['title'], $bookId
                        );
                        switch ($status) {
                            case BookKeepingService::STATUS_NORMAL:
                                $context['accounttype'] = null;
                                $context['accountcreate']['grouptitle'] = null;
                                break;
                            case BookKeepingService::STATUS_ERROR_AUTH_FORBIDDEN:
                                $message = __('You are not permitted to write in this book.');
                                $context['messages']['group'] = strval($message);
                                break;
                            default:
                                abort(Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                    } else {
                        $message = __('Please select the type and enter a valid name.');
                        $context['messages']['group'] = strval($message);
                    }
                    break;
                case 'item':
                    $result = $this->validateAndTrimForCreateAccount($request->all());
                    $account = $result['account'];
                    $context['accountcreate']['groupId'] = strval($account['accountgroup']);
                    $context['accountcreate']['itemtitle'] = strval($account['title']);
                    $context['accountcreate']['description'] = strval($account['description']);
                    if ($result['success']) {
                        [$status, $_] = $this->BookKeeping->createAccount(
                            $account['accountgroup'], $account['title'], $account['description'], $bookId
                        );
                        switch ($status) {
                            case BookKeepingService::STATUS_NORMAL:
                                $context['accountcreate']['groupId'] = null;
                                $context['accountcreate']['itemtitle'] = null;
                                $context['accountcreate']['description'] = null;
                                break;
                            case BookKeepingService::STATUS_ERROR_AUTH_FORBIDDEN:
                                $context['messages']['item']
                                     = __('You are not permitted to write in this book.');
                                break;
                            case BookKeepingService::STATUS_ERROR_BAD_CONDITION:
                                abort(Response::HTTP_NOT_FOUND);
                            default:
                                abort(Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                    } else {
                        $message = __('Please select the group and enter a valid name and description.');
                        $context['messages']['item'] = strval($message);
                    }
                    break;
                default:
                    abort(Response::HTTP_NOT_FOUND);
            }
        }

        [$status, $categorizedAccounts] = $this->BookKeeping->retrieveCategorizedAccounts(false, $bookId);
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                if (isset($categorizedAccounts)) {
                    $context['accounts'] = $categorizedAccounts;
                } else {
                    abort(Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                break;
            default:
                abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->responder->response($context);
    }

    /**
     * Validate arguments and trim string data for create Account.
     *
     * @param  array<string, mixed>  $account_in
     * @return array{success: bool, account: array{
     *   accountgroup: string,
     *   title:  string,
     *   description: string,
     * }}
     */
    private function validateAndTrimForCreateAccount(array $account_in): array
    {
        $success = true;
        $accountGroupId = '';
        $title = '';
        $description = '';

        if (array_key_exists('accountgroup', $account_in)) {
            $accountGroupId = trim(strval($account_in['accountgroup']));
            if (empty($accountGroupId)) {
                $success = false;
            }
        } else {
            $success = false;
        }
        if (array_key_exists('title', $account_in)) {
            $title = trim(strval($account_in['title']));
            if (empty($title)) {
                $success = false;
            }
        } else {
            $success = false;
        }
        if (array_key_exists('description', $account_in)) {
            $description = trim(strval($account_in['description']));
            if (empty($description)) {
                $success = false;
            }
        } else {
            $success = false;
        }
        if ($success) {
            $trimmed_account = [
                'accountgroup' => $accountGroupId,
                'title' => $title,
                'description' => $description,
            ];
        } else {
            $trimmed_account = [
                'accountgroup' => '',
                'title' => '',
                'description' => '',
            ];
        }

        return ['success' => $success, 'account' => $trimmed_account];
    }

    /**
     * Validate arguments and trim string data for create AccountGroup.
     *
     * @param  array<string, mixed>  $accountGroup_in
     * @return array{success: bool, accountGroup: array{
     *   accounttype: string,
     *   title:  string,
     * }}
     */
    private function validateAndTrimForCreateAccountGroup(array $accountGroup_in): array
    {
        $success = true;
        $accountType = '';
        $title = '';

        if (array_key_exists('accounttype', $accountGroup_in)) {
            $accountType = trim(strval($accountGroup_in['accounttype']));
            switch ($accountType) {
                case AccountService::ACCOUNT_TYPE_ASSET:
                case AccountService::ACCOUNT_TYPE_LIABILITY:
                case AccountService::ACCOUNT_TYPE_EXPENSE:
                case AccountService::ACCOUNT_TYPE_REVENUE:
                    break;
                default:
                    $success = false;
                    break;
            }
        } else {
            $success = false;
        }
        if (array_key_exists('title', $accountGroup_in)) {
            $title = trim(strval($accountGroup_in['title']));
            if (empty($title)) {
                $success = false;
            }
        } else {
            $success = false;
        }
        if ($success) {
            $trimmed_accountGroup = [
                'accounttype' => $accountType,
                'title' => $title,
            ];
        } else {
            $trimmed_accountGroup = [
                'accounttype' => '',
                'title' => '',
            ];
        }

        return ['success' => $success, 'accountGroup' => $trimmed_accountGroup];
    }
}
