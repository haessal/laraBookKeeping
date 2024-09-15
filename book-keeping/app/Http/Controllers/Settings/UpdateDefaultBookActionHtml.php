<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\Settings\UpdateDefaultBookViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UpdateDefaultBookActionHtml extends AuthenticatedBookKeepingAction
{
    /**
     * UpdateDefaultBookView responder instance.
     *
     * @var \App\Http\Responder\Settings\UpdateDefaultBookViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Http\Responder\Settings\UpdateDefaultBookViewResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, UpdateDefaultBookViewResponder $responder)
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
    public function __invoke(Request $request): Response
    {
        $context = [];

        if ($request->isMethod('post')) {
            $selectedBook = $request->input('selectedBook');
            if (isset($selectedBook)) {
                $this->BookKeeping->setBookAsDefault($selectedBook);
            }
        }
        if ($request->isMethod('delete')) {
            [$status, $book] = $this->BookKeeping->retrieveDefaultBook();
            switch ($status) {
                case BookKeepingService::STATUS_NORMAL:
                    if (isset($book)) {
                        $this->BookKeeping->unsetBookAsDefault($book['id']);
                    }
                    break;
                default:
                    break;
            }
        }
        $context['defaultBook'] = null;
        [$status, $book] = $this->BookKeeping->retrieveDefaultBook();
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                if (isset($book)) {
                    $context['defaultBook'] = $book;
                }
                break;
            default:
                break;
        }
        $booksUserOwn = [];
        if (is_null($context['defaultBook'])) {
            $availableBooks = $this->BookKeeping->retrieveAvailableBooks();
            foreach ($availableBooks as $book) {
                if ($book['is_owner']) {
                    $booksUserOwn[] = [
                        'bookId' => $book['id'],
                        'bookName' => $book['name']
                    ];
                }
            }
        }
        $context['candidates'] = $booksUserOwn;

        return $this->responder->response($context);
    }
}
