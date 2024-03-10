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
     * Check if the date format is valid and source is later than the destination.
     *
     * @param  string|null  $sourceUpdateAt
     * @param  string|null  $destinationUpdateAt
     * @return bool
     */
    public function isSourceLater($sourceUpdateAt, $destinationUpdateAt)
    {
        if (isset($sourceUpdateAt)) {
            if (Carbon::canBeCreatedFromFormat($sourceUpdateAt, Carbon::ATOM)) {
                /** @var \Illuminate\Support\Carbon $source */
                $source = Carbon::createFromFormat(Carbon::ATOM, $sourceUpdateAt);
                if (isset($destinationUpdateAt)) {
                    if (Carbon::canBeCreatedFromFormat($destinationUpdateAt, Carbon::ATOM)) {
                        /** @var \Illuminate\Support\Carbon $destination */
                        $destination = Carbon::createFromFormat(Carbon::ATOM, $destinationUpdateAt);

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
