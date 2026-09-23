<?php

namespace App\Http\Controllers\page;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\page\CreateBookPageResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateBookActionHtml extends AuthenticatedBookKeepingAction
{
    /**
     * ShowBookCreateView responder instance.
     *
     * @var \App\Http\Responder\page\CreateBookPageResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\page\CreateBookPageResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, CreateBookPageResponder $responder)
    {
        parent::__construct($BookKeeping);
        $this->responder = $responder;
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request): Response
    {
        return $this->responder->response($request);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $result = $this->validateAndTrimPostBooksParameter($request->all());
        if (! $result['success']) {
            return $this->responder->response($request);
        }

        $bookId = $this->BookKeeping->createBook($result['name']);

        return redirect()->route('v2', ['bookId' => $bookId]);
    }

    /**
     * Validate the parameter and trim string data.
     *
     * @param  array<string, mixed>  $parameter
     * @return array{success: bool, name: string}
     */
    private function validateAndTrimPostBooksParameter(array $parameter): array
    {
        $success = false;
        $trimmed_name = '';
        if (array_key_exists('name', $parameter) && is_string($parameter['name'])) {
            $name = trim($parameter['name']);
            if (! empty($name)) {
                $success = true;
                $trimmed_name = $name;
            }
        }

        return ['success' => $success, 'name' => $trimmed_name];
    }
}
