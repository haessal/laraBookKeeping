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
    public function __construct($version)
    {
        $this->version = $version;
    }

    /**
     * Indicate whether the specified feature is supported in this version.
     *
     * @param  string  $feature
     * @return bool
     */
    public function isSupported($feature)
    {
        return Comparator::greaterThanOrEqualTo($this->version, $feature);
    }

    /**
     * Return a string representation.
     *
     * @return string
     */
    public function toString()
    {
        return $this->version;
    }
}
