<?php

namespace App\Http\Responder\page\v2;

use App\Http\Responder\BaseLayoutViewResponder;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Response;

class BaseViewResponder extends BaseLayoutViewResponder
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
     * Create a new BaseViewResponder instance.
     *
     * @param  \Illuminate\Http\Response  $response
     * @param  \Illuminate\Contracts\View\Factory  $view
     */
    public function __construct(Response $response, ViewFactory $view)
    {
        $this->response = $response;
        $this->view = $view;
    }

    /**
     * List of Navigation links for version 2 pages.
     *
     * @return array
     */
    public function navilinks(): array
    {
        return [
            ['link' => 'v2_home', 'caption' => __('Home'), 'icon' => 'fa fa-home'],
            ['link' => 'v2_accounts', 'caption' => __('Accounts'), 'icon' => 'fa fa-shopping-cart'],
            ['link' => null],
            ['link' => null],
            ['link' => null],
            ['link' => null],
        ];
    }
}
