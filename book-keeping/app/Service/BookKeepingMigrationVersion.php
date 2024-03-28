<?php

namespace App\Service;

use Composer\Semver\Comparator;

class BookKeepingMigrationVersion
{
    /**
     * Format version of books json exported by this program.
     *
     * @var string
     */
    const CURRENT = '2.1.0';

    /**
     * Format version of books json supporting credit card statement.
     *
     * @var string
     */
    const CREDIT_CARD_STATEMENT = '2.1.0';

    /**
     * Format version of books json for migration. 
     *
     * @var string
     */
    private $version;

    /**
     * Create a new BookKeepingMigrationVersion instance.
     *
     * @param  string  $version
     */
    public function __construct($version = '0.0.0')
    {
        $this->version = $version;
    }
}