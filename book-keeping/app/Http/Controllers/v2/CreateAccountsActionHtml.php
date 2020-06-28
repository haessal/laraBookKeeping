<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\v2\CreateAccountsViewResponder;
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
     * @param \App\Service\BookKeepingService                    $BookKeeping
     * @param \App\Http\Responder\v2\CreateAccountsViewResponder $responder
     *
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
     * @param \Illuminate\Http\Request $request
     *
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
                    $context['accounttype'] = $request->input('accounttype');
                    $context['accountcreate']['grouptitle'] = trim($request->input('title'));
                    break;
                case 'item':
                    $context['accountcreate']['groupid'] = $request->input('accountgroup');
                    $context['accountcreate']['itemtitle'] = trim($request->input('title'));
                    $context['accountcreate']['description'] = trim($request->input('description'));
                    break;
                default:
                    break;
            }


            var_dump($request->all());
        }

        return $this->responder->response($context);
    }
}
