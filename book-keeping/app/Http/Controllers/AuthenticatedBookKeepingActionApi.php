<?php

namespace App\Http\Controllers;

use App\Service\BookKeepingService;

class AuthenticatedBookKeepingApiAction extends AuthenticatedApiAction
{
    /**
     * BookKeeping service instance.
     *
     * @var \App\Service\BookKeepingService
     */
    protected $BookKeeping;

    /**
     * Create a new AuthenticatedBookKeepingApiAction instance.
     *
     * @param \App\Service\BookKeepingService $BookKeeping
     */
    public function __construct(BookKeepingService $BookKeeping)
    {
        parent::__construct();
        $this->BookKeeping = $BookKeeping;
    }
}
