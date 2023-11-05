<?php

namespace App\Service;

use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

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

    /**
     * Pull data to import from exporter.
     *
     * @param string $exporterUrl
     * @param string $accessToken
     * @return \Illuminate\Http\Client\Response
     */
    public function getFromExporter($exporterUrl, $accessToken): ClientResponse
    {
        $accessComplete = false;

        while(!$accessComplete) {
            $response = Http::withToken($accessToken)->get($exporterUrl);
            if ($response->status() != Response::HTTP_TOO_MANY_REQUESTS) {
                $accessComplete = true;
            } else {
                sleep(5);
            }
        }
    
        return $response;
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