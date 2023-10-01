<?php

namespace App\Http\Responder\Settings;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Response;

class UpdateAccessTokenViewResponder
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
     * Respond the UpdateAccessTokenView.
     *
     * @param  array<string, mixed>  $context
     * @return \Illuminate\Http\Response
     */
    public function response(array $context): Response
    {
        $message_for_new_token = null;
        $message_for_no_token = null;

        if ($context['token'] != null) {
            $message_for_new_token = strval(__('Make sure to copy your new personal access token now.')).strval(__('You won\'t be able to see it again!'));
        }
        if ($context['timestamp'] == null) {
            $message_for_no_token = strval(__('There is no token available.'));
        }
        $this->response->setContent($this->view->make('settings.accesstokens', [
            'message_for_new_token' => $message_for_new_token,
            'message_for_no_token'  => $message_for_no_token,
            'token'                 => $context['token'],
            'timestamp'             => strval(__('The token was generated at ')).strval($context['timestamp']),
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}
