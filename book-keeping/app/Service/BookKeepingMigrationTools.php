<?php

namespace App\Service;

use Illuminate\Support\Carbon;

class BookKeepingMigrationTools
{
    /**
     * Convert exported timestamps.
     *
     * @param  array<string, mixed>  $exported
     * @return array<string, mixed>
     */
    public function convertExportedTimestamps(array $exported): array
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

    /**
     * Check if the source is later than the destination.
     *
     * @param  string|null  $sourceUpdateAt
     * @param  string|null  $destinationUpdateAt
     * @return bool
     */
    public function isSourceLater($sourceUpdateAt, $destinationUpdateAt)
    {
        if (isset($sourceUpdateAt)) {
            $source = Carbon::createFromFormat(Carbon::ATOM, $sourceUpdateAt);
            if (! is_bool($source)) {
                if (isset($destinationUpdateAt)) {
                    $destination = Carbon::createFromFormat(Carbon::ATOM, $destinationUpdateAt);
                    if (! is_bool($destination)) {
                        return $source->gt($destination);
                    } else {
                        return true;
                    }
                } else {
                    return true;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
