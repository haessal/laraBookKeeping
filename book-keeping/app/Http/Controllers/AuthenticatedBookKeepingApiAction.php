<?php

namespace App\Http\Controllers;

use App\Service\BookKeepingService;

class AuthenticatedBookKeepingAPIAction extends AuthenticatedApiAction
{
    /**
     * BookKeeping service instance.
     *
     * @var \App\Service\BookKeepingService
     */
    protected $BookKeeping;

    /**
     * Create a new AuthenticatedBookKeepingAPIAction instance.
     *
     * @param \App\Service\BookKeepingService $BookKeeping
     */
    public function __construct(BookKeepingService $BookKeeping)
    {
        parent::__construct();
        $this->BookKeeping = $BookKeeping;
    }
}
