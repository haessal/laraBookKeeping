<?php

namespace App\Service;

class BookKeepingMigrationTools
{
    /**
     * Convert exported timestamps.
     *
     * @param  array<string, mixed>  $exported
     * @return array<string, mixed>
     */
    public function convertExportedTimestamps(array $exported)
    {
        $converted = [];
        foreach ($exported as $key => $value) {
            switch ($key) {
                case 'created_at':
                    break;
                case 'deleted_at':
                    $converted['deleted'] = ! is_null($value);
                    break;
                default:
                    $converted[$key] = $value;
                    break;
            }
        }

        return $converted;
    }
}
