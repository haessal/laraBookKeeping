<?php

namespace App\Http\Responder\Settings;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Response;

class UpdateDefaultBookViewResponder
{
    /**
     * Response instance.
     *
     * @var \Illuminate\Http\Response
     */
    protected $response;

    /**
     * View Factory instance.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;

    /**
     * Create a new responder instance.
     *
     * @param  \Illuminate\Http\Response  $response
     * @param  \Illuminate\Contracts\View\Factory  $view
     * @return void
     */
    public function __construct(Response $response, ViewFactory $view)
    {
        $this->response = $response;
        $this->view = $view;
    }

    /**
     * Respond the UpdateDefaultBookView.
     *
     * @param  array<string, mixed>  $context
     * @return \Illuminate\Http\Response
     */
    public function response(array $context): Response
    {
        $message = null;

        if (is_null($context['defaultBook'])) {
            $message = strval(__('Select the book to set as the default.'));
        }
        $this->response->setContent($this->view->make('settings.defaultbook', [
            'defaultBook' => $context['defaultBook'],
            'candidates' => $context['candidates'],
            'message' => $message,
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}
