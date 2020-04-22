<?php

namespace App\Http\Controllers;

use App\Service\BookKeepingService;
use Illuminate\Support\Carbon;

class AuthenticatedBookKeepingAction extends AuthenticatedAction
{
    /**
     * BookKeeping service instance.
     *
     * @var \App\Service\BookKeepingService
     */
    protected $BookKeeping;

    /**
     * Create a new AuthenticatedBookKeepingAction instance.
     *
     * @param \App\Service\BookKeepingService $BookKeeping
     */
    public function __construct(BookKeepingService $BookKeeping)
    {
        parent::__construct();
        $this->BookKeeping = $BookKeeping;
    }

    /**
     * Validate date format.
     *
     * @param string $date
     *
     * @return bool
     */
    protected function validateDateFormat(string $date): bool
    {
        $success = false;

        if (strptime($date, '%Y-%m-%d')) {
            $d = Carbon::createFromFormat('Y-m-d', $date);
            if ($d) {
                if ($d->format('Y-m-d') == $date) {
                    $success = true;
                }
            }
        }

        return $success;
    }
}
