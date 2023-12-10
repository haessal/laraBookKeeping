<?php

namespace App\Service;

use App\DataProvider\SlipEntryRepositoryInterface;
use App\DataProvider\SlipRepositoryInterface;
use Illuminate\Support\Facades\Log;

class SlipMigrationLoaderService extends SlipMigrationService
{
    /**
     * Validator for loading.
     *
     * @var \App\Service\BookKeepingMigrationValidator
     */
    private $validator;

    /**
     * Create a new SlipMigrationService instance.
     *
     * @param  \App\DataProvider\SlipRepositoryInterface  $slip
     * @param  \App\DataProvider\SlipEntryRepositoryInterface  $slipEntry
     * @param  \App\Service\BookKeepingMigrationTools  $tools
     * @param  \App\Service\BookKeepingMigrationValidator  $validator
     */
    public function __construct(SlipRepositoryInterface $slip, SlipEntryRepositoryInterface $slipEntry, BookKeepingMigrationTools $tools, BookKeepingMigrationValidator $validator)
    {
        parent::__construct($slip, $slipEntry, $tools);
        $this->validator = $validator;
    }

    /**
     * Load the slip.
     *
     * @param  array<string, mixed>  $slip
     * @param array<string, array{
     *   slip_id: string,
     *   updated_at: string|null,
     * }> $destinationSlips
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function loadSlip(array $slip, array $destinationSlips): array
    {
        $mode = null;
        $result = null;
        $error = null;

        $newSlip = $this->validator->validateSlip($slip);
        if (is_null($newSlip)) {
            $error = 'invalid data format: slip';

            return [['slip_id' => null, 'result' => $result], $error];
        }
        $slipId = $newSlip['slip_id'];
        if (key_exists($slipId, $destinationSlips)) {
            $sourceUpdateAt = $newSlip['updated_at'];
            $destinationUpdateAt = $destinationSlips[$slipId]['updated_at'];
            if ($this->tools->isSourceLater($sourceUpdateAt, $destinationUpdateAt)) {
                $mode = 'update';
            }
        } else {
            $mode = 'create';
        }
        if (isset($mode)) {
            switch($mode) {
                case 'update':
                    $this->slip->updateForImporting($newSlip);
                    $result = 'updated';
                    break;
                case 'create':
                    $this->slip->createForImporting($newSlip);
                    $result = 'created';
                    break;
                default:
                    break;
            }
        } else {
            $result = 'already up-to-date';
        }

        return [['slip_id' => $slipId, 'result' => $result], $error];
    }

    /**
     * Load the slip entries belonging to the slip.
     *
     * @param  string  $bookId
     * @param  string  $slipId
     * @param  array<string, array<string, mixed>>  $slipEntries
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function loadSlipEntries($bookId, $slipId, array $slipEntries): array
    {
        $result = [];
        $error = null;

        $destinationSlipEntries = $this->exportSlipEntries($bookId, $slipId);
        if (! key_exists($slipId, $destinationSlipEntries)) {
            $error = 'The slip that the entires are bound to is not exist. '.$slipId;

            return [$result, $error];
        }
        $slipEntryNumber = count($slipEntries);
        $slipEntryCount = 0;
        foreach ($slipEntries as $slipEntryIndex => $slipEntry) {
            if (key_exists('slip_entry_id', $slipEntry)) {
                $slipEntryId = $slipEntry['slip_entry_id'];
            } else {
                $error = 'invalid data format: slip_entry_id';
                break;
            }
            if (key_exists('slip_entry', $slipEntry) && is_array($slipEntry['slip_entry'])) {
                [$result[$slipEntryIndex], $error] = $this->loadSlipEntry(
                    $slipEntry['slip_entry'], $destinationSlipEntries[$slipId]['entries']
                );
                if (isset($error)) {
                    break;
                }
                Log::debug('load: slip entry '.sprintf('%5d', $slipEntryCount).'/'.sprintf('%5d', $slipEntryNumber).' '.$slipEntryId.' '.$result[$slipEntryIndex]['result']);
            }
            $slipEntryCount++;
        }

        return [$result, $error];
    }

    /**
     * Load the slip entry.
     *
     * @param  array<string, mixed>  $slipEntry
     * @param array<string, array{
     *   slip_entry_id: string,
     *   updated_at: string|null,
     * }>  $destinationSlipEntries
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function loadSlipEntry(array $slipEntry, array $destinationSlipEntries): array
    {
        $mode = null;
        $result = null;
        $error = null;

        $newSlipEntry = $this->validator->validateSlipEntry($slipEntry);
        if (is_null($newSlipEntry)) {
            $error = 'invalid data format: slip entry';

            return [['slip_entry_id' => null, 'result' => $result], $error];
        }
        $slipEntryId = $newSlipEntry['slip_entry_id'];
        if (key_exists($slipEntryId, $destinationSlipEntries)) {
            $sourceUpdateAt = $newSlipEntry['updated_at'];
            $destinationUpdateAt = $destinationSlipEntries[$slipEntryId]['updated_at'];
            if ($this->tools->isSourceLater($sourceUpdateAt, $destinationUpdateAt)) {
                $mode = 'update';
            }
        } else {
            $mode = 'create';
        }
        if (isset($mode)) {
            switch($mode) {
                case 'update':
                    $this->slipEntry->updateForImporting($newSlipEntry);
                    $result = 'updated';
                    break;
                case 'create':
                    $this->slipEntry->createForImporting($newSlipEntry);
                    $result = 'created';
                    break;
                default:
                    break;
            }
        } else {
            $result = 'already up-to-date';
        }

        return [['slip_entry_id' => $slipEntryId, 'result' => $result], $error];
    }

    /**
     * Load the slips of the book.
     *
     * @param  string  $bookId
     * @param  array<string, array<string, mixed>>  $slips
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function loadSlips($bookId, array $slips): array
    {
        $result = [];
        $error = null;

        $destinationSlips = $this->exportSlips($bookId);
        $slipNumber = count($slips);
        $slipCount = 0;
        foreach ($slips as $slipIndex => $slip) {
            if (key_exists('slip_id', $slip) && is_string($slip['slip_id'])) {
                $slipId = $slip['slip_id'];
            } else {
                $error = 'invalid data format: slip_id';
                break;
            }
            if (key_exists('slip', $slip) && is_array($slip['slip'])) {
                [$result[$slipIndex], $error] = $this->loadSlip($slip['slip'], $destinationSlips);
                if (isset($error)) {
                    break;
                }
                Log::debug('load: slip       '.sprintf('%5d', $slipCount).'/'.sprintf('%5d', $slipNumber).' '.$slipId.' '.$result[$slipIndex]['result']);
            }
            $slipCount++;
            if (key_exists('entries', $slip)) {
                if (is_array($slip['entries'])) {
                    [$result[$slipIndex]['entries'], $error] = $this->loadSlipEntries(
                        $bookId, $slipId, $slip['entries']
                    );
                    if (isset($error)) {
                        break;
                    }
                } else {
                    $error = 'invalid data format: slip entries';
                }
            }
        }

        return [$result, $error];
    }
}
