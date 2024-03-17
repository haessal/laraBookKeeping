<?php

namespace App\Http\Controllers\api;

use App\Service\BookKeepingMigration;

class AuthenticatedBookKeepingMigrationActionApi extends AuthenticatedActionApi
{
    /**
     * BookKeeping migration service instance.
     *
     * @var \App\Service\BookKeepingMigration
     */
    protected $BookKeeping;

    /**
     * Create a new AuthenticatedBookKeepingMigrationActionApi instance.
     *
     * @param  \App\Service\BookKeepingMigration  $BookKeeping
     * @return void
     */
    public function __construct(BookKeepingMigration $BookKeeping)
    {
        parent::__construct();
        $this->BookKeeping = $BookKeeping;
    }
}
