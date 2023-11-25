<?php

namespace App\Service;

use App\DataProvider\SlipEntryRepositoryInterface;
use App\DataProvider\SlipRepositoryInterface;
use Illuminate\Support\Facades\Log;

class SlipMigrationService extends SlipService
{
    /**
     * BookKeeping migration tools instance.
     *
     * @var \App\Service\BookKeepingMigrationTools
     */
    private $tools;

    /**
     * Create a new SlipMigrationService instance.
     *
     * @param  \App\DataProvider\SlipRepositoryInterface  $slip
     * @param  \App\DataProvider\SlipEntryRepositoryInterface  $slipEntry
     * @param  \App\Service\BookKeepingMigrationTools  $tools
     */
    public function __construct(SlipRepositoryInterface $slip, SlipEntryRepositoryInterface $slipEntry, BookKeepingMigrationTools $tools)
    {
        parent::__construct($slip, $slipEntry);
        $this->tools = $tools;
    }

    /**
     * Dump slips of the book.
     *
     * @param  string  $bookId
     * @return array{
     *   slip_id: string,
     *   slip: array{
     *     slip_id: string,
     *     book_id: string,
     *     slip_outline: string,
     *     slip_memo: string|null,
     *     date: string,
     *     is_draft: bool,
     *     display_order: int|null,
     *     updated_at: string|null,
     *     deleted: bool,
     *   },
     *   entries: array{
     *     slip_entry_id: string,
     *     slip_entry: array{
     *       slip_entry_id: string,
     *       slip_id: string,
     *       debit: string,
     *       credit: string,
     *       amount: int,
     *       client: string,
     *       outline: string,
     *       display_order: int|null,
     *       updated_at: string|null,
     *       deleted: bool,
     *     }
     *   }[],
     * }[]
     */
    public function dumpSlips($bookId): array
    {
        $slips = [];

        /** @var array{
         *   slip_id: string,
         *   book_id: string,
         *   slip_outline: string,
         *   slip_memo: string|null,
         *   date: string,
         *   is_draft: bool,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }[] $slipList
         */
        $slipList = $this->slip->searchBookForExporting($bookId);
        foreach ($slipList as $slip) {
            $slipId = $slip['slip_id'];
            /** @var array{
             *   slip_id: string,
             *   book_id: string,
             *   slip_outline: string,
             *   slip_memo: string|null,
             *   date: string,
             *   is_draft: bool,
             *   display_order: int|null,
             *   updated_at: string|null,
             *   deleted: bool,
             * } $convertedSlip
             */
            $convertedSlip = $this->tools->convertExportedTimestamps($slip);
            $entries = [];
            /** @var array{
             *   slip_entry_id: string,
             *   slip_id: string,
             *   debit: string,
             *   credit: string,
             *   amount: int,
             *   client: string,
             *   outline: string,
             *   display_order: int|null,
             *   created_at: string|null,
             *   updated_at: string|null,
             *   deleted_at: string|null,
             * }[] $slipEntryList
             */
            $slipEntryList = $this->slipEntry->searchSlipForExporting($slipId);
            foreach ($slipEntryList as $slipEntry) {
                /** @var array{
                 *   slip_entry_id: string,
                 *   slip_id: string,
                 *   debit: string,
                 *   credit: string,
                 *   amount: int,
                 *   client: string,
                 *   outline: string,
                 *   display_order: int|null,
                 *   updated_at: string|null,
                 *   deleted: bool,
                 * } $convertedSlipEntry
                 */
                $convertedSlipEntry = $this->tools->convertExportedTimestamps($slipEntry);
                $entries[] = [
                    'slip_entry_id' => $slipEntry['slip_entry_id'],
                    'slip_entry'    => $convertedSlipEntry,
                ];
            }
            $slips[] = [
                'slip_id' => $slipId,
                'slip'    => $convertedSlip,
                'entries' => $entries,
            ];
        }

        return $slips;
    }

    /**
     * Export the slip.
     *
     * @param  string  $bookId
     * @param  string  $slipId
     * @return array<string, array{
     *   slip_id: string,
     *   book_id: string,
     *   slip_outline: string,
     *   slip_memo: string|null,
     *   date: string,
     *   is_draft: bool,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }>
     */
    public function exportSlip($bookId, $slipId): array
    {
        $slips = [];

        /** @var array{
         *   slip_id: string,
         *   book_id: string,
         *   slip_outline: string,
         *   slip_memo: string|null,
         *   date: string,
         *   is_draft: bool,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }[] $slipList
         */
        $slipList = $this->slip->searchBookForExporting($bookId, $slipId);
        foreach ($slipList as $slip) {
            /** @var array{
             *   slip_id: string,
             *   book_id: string,
             *   slip_outline: string,
             *   slip_memo: string|null,
             *   date: string,
             *   is_draft: bool,
             *   display_order: int|null,
             *   updated_at: string|null,
             *   deleted: bool,
             * } $convertedSlip
             */
            $convertedSlip = $this->tools->convertExportedTimestamps($slip);
            $slips[$slip['slip_id']] = $convertedSlip;
        }

        return $slips;
    }

    /**
     * Export a list of entries on the slip.
     *
     * @param  string  $bookId
     * @param  string  $slipId
     * @return array<string, array{
     *   entries: array<string, array{
     *     slip_entry_id: string,
     *     updated_at: string|null,
     *   }>,
     * }>
     */
    public function exportSlipEntries($bookId, $slipId): array
    {
        $slips = [];

        /** @var array{
         *   slip_id: string,
         *   book_id: string,
         *   slip_outline: string,
         *   slip_memo: string|null,
         *   date: string,
         *   is_draft: bool,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }[] $slipList
         */
        $slipList = $this->slip->searchBookForExporting($bookId, $slipId);
        foreach ($slipList as $slip) {
            $entries = [];
            /** @var array{
             *   slip_entry_id: string,
             *   slip_id: string,
             *   debit: string,
             *   credit: string,
             *   amount: int,
             *   client: string,
             *   outline: string,
             *   display_order: int|null,
             *   created_at: string|null,
             *   updated_at: string|null,
             *   deleted_at: string|null,
             * }[] $slipEntryList
             */
            $slipEntryList = $this->slipEntry->searchSlipForExporting(strval($slip['slip_id']));
            foreach ($slipEntryList as $slipEntry) {
                $entries[$slipEntry['slip_entry_id']] = [
                    'slip_entry_id' => $slipEntry['slip_entry_id'],
                    'updated_at'    => $slipEntry['updated_at'],
                ];
            }
            $slips[$slip['slip_id']] = ['entries' => $entries];
        }

        return $slips;
    }

    /**
     * Export the slip entry.
     *
     * @param  string  $bookId
     * @param  string  $slipId
     * @param  string  $slipEntryId
     * @return array<string, array{
     *   entries: array<string, array{
     *     slip_entry_id: string,
     *     slip_id: string,
     *     debit: string,
     *     credit: string,
     *     amount: int,
     *     client: string,
     *     outline: string,
     *     display_order: int|null,
     *     updated_at: string|null,
     *     deleted: bool,
     *   }>,
     * }>
     */
    public function exportSlipEntry($bookId, $slipId, $slipEntryId): array
    {
        $slips = [];

        /** @var array{
         *   slip_id: string,
         *   book_id: string,
         *   slip_outline: string,
         *   slip_memo: string|null,
         *   date: string,
         *   is_draft: bool,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }[] $slipList
         */
        $slipList = $this->slip->searchBookForExporting($bookId, $slipId);
        foreach ($slipList as $slip) {
            $entries = [];
            /** @var array{
             *   slip_entry_id: string,
             *   slip_id: string,
             *   debit: string,
             *   credit: string,
             *   amount: int,
             *   client: string,
             *   outline: string,
             *   display_order: int|null,
             *   created_at: string|null,
             *   updated_at: string|null,
             *   deleted_at: string|null,
             * }[] $slipEntryList
             */
            $slipEntryList = $this->slipEntry->searchSlipForExporting(strval($slip['slip_id']), $slipEntryId);
            foreach ($slipEntryList as $slipEntry) {
                /** @var array{
                 *   slip_entry_id: string,
                 *   slip_id: string,
                 *   debit: string,
                 *   credit: string,
                 *   amount: int,
                 *   client: string,
                 *   outline: string,
                 *   display_order: int|null,
                 *   updated_at: string|null,
                 *   deleted: bool,
                 * } $convertedSlipEntry
                 */
                $convertedSlipEntry = $this->tools->convertExportedTimestamps($slipEntry);
                $entries[$slipEntry['slip_entry_id']] = $convertedSlipEntry;
            }
            $slips[$slip['slip_id']] = ['entries' => $entries];
        }

        return $slips;
    }

    /**
     * Export slips of the book.
     *
     * @param  string  $bookId
     * @return array<string, array{
     *   slip_id: string,
     *   updated_at: string|null,
     * }>
     */
    public function exportSlips($bookId): array
    {
        $slips = [];

        /** @var array{
         *   slip_id: string,
         *   book_id: string,
         *   slip_outline: string,
         *   slip_memo: string|null,
         *   date: string,
         *   is_draft: bool,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }[] $slipList
         */
        $slipList = $this->slip->searchBookForExporting($bookId);
        foreach ($slipList as $slip) {
            $slipId = $slip['slip_id'];
            $slips[$slipId] = [
                'slip_id'    => $slip['slip_id'],
                'updated_at' => $slip['updated_at'],
            ];
        }

        return $slips;
    }

    /**
     * Import the slip.
     *
     * @param  string  $sourceUrl
     * @param  string  $accessToken
     * @param  string  $bookId
     * @param  array{
     *   slip_id: string,
     *   updated_at: string|null,
     * }  $slipHead
     * @param  array<string, array{
     *   slip_id: string,
     *   updated_at: string|null,
     * }>  $destinationSlips
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function importSlip($sourceUrl, $accessToken, $bookId, $slipHead, $destinationSlips): array
    {
        $slipId = $slipHead['slip_id'];
        $mode = null;
        $result = null;
        $error = null;

        if (key_exists($slipId, $destinationSlips)) {
            $sourceUpdateAt = $slipHead['updated_at'];
            $destinationUpdateAt = $destinationSlips[$slipId]['updated_at'];
            if ($this->tools->isSourceLater($sourceUpdateAt, $destinationUpdateAt)) {
                $mode = 'update';
            }
        } else {
            $mode = 'create';
        }
        if (isset($mode)) {
            $url = $sourceUrl.'/'.$bookId.'/slips/'.$slipId;
            $response = $this->tools->getFromExporter($url, $accessToken);
            if ($response->ok()) {
                /** @var array{
                 *   version: string,
                 *   books: array<string, array{
                 *     slips: array<string, array{
                 *       slip_id: string,
                 *       book_id: string,
                 *       slip_outline: string,
                 *       slip_memo: string|null,
                 *       date: string,
                 *       is_draft: bool,
                 *       display_order: int|null,
                 *       updated_at: string|null,
                 *       deleted: bool,
                 *     }>,
                 *   }>,
                 * }|null $responseBody
                 */
                $responseBody = $response->json();
                if (isset($responseBody)) {
                    $slip = $responseBody['books'][$bookId]['slips'][$slipId];
                    switch($mode) {
                        case 'update':
                            $this->slip->updateForImporting($slip);
                            $result = 'updated';
                            break;
                        case 'create':
                            $this->slip->createForImporting($slip);
                            $result = 'created';
                            break;
                        default:
                            break;
                    }
                } else {
                    $error = 'No response data. '.$url;
                }
            } else {
                $error = 'Response error('.$response->status().'). '.$url;
            }
        } else {
            $result = 'already up-to-date';
        }

        return [['slip_id' => $slipId, 'result' => $result], $error];
    }

    /**
     * Import a list of slip entries belonging to the slip.
     *
     * @param  string  $sourceUrl
     * @param  string  $accessToken
     * @param  string  $bookId
     * @param  string  $slipId
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function importSlipEntries($sourceUrl, $accessToken, $bookId, $slipId): array
    {
        $result = [];
        $error = null;

        $destinationSlipEntries = $this->exportSlipEntries($bookId, $slipId);
        if (! key_exists($slipId, $destinationSlipEntries)) {
            $error = 'The slip that the entries are bound to is not exist. '.$slipId;

            return [$result, $error];
        }

        $url = $sourceUrl.'/'.$bookId.'/slips/'.$slipId.'/entries';
        $response = $this->tools->getFromExporter($url, $accessToken);
        if ($response->ok()) {
            /** @var array{
             *   version: string,
             *   books: array<string, array{
             *     slips: array<string, array{
             *       entries: array<string, array{
             *         slip_entry_id: string,
             *         updated_at: string|null,
             *       }>,
             *     }>,
             *   }>,
             * }|null $responseBody
             */
            $responseBody = $response->json();
            if (isset($responseBody)) {
                $sourceSlipEntries = $responseBody['books'][$bookId]['slips'][$slipId]['entries'];
                $slipEntryNumber = count($sourceSlipEntries);
                $slipEntryCount = 0;
                foreach ($sourceSlipEntries as $slipEntryId => $slipEntry) {
                    [$result[$slipEntryId], $error] = $this->importSlipEntry(
                        $sourceUrl,
                        $accessToken,
                        $bookId,
                        $slipId,
                        $slipEntry,
                        $destinationSlipEntries[$slipId]['entries']
                    );
                    if (isset($error)) {
                        break;
                    }
                    Log::debug('import: skip entry       '.sprintf('%5d', $slipEntryCount).'/'.sprintf('%5d', $slipEntryNumber).' '.$slipEntryId.' '.$result[$slipEntryId]['result']);
                    $slipEntryCount++;
                }
            } else {
                $error = 'No response data. '.$url;
            }
        } else {
            $error = 'Response error('.$response->status().'). '.$url;
        }

        return [$result, $error];
    }

    /**
     * Import the slip entry.
     *
     * @param  string  $sourceUrl
     * @param  string  $accessToken
     * @param  string  $bookId
     * @param  string  $slipId
     * @param  array{
     *   slip_entry_id: string,
     *   updated_at: string|null,
     * }  $slipEntryHead
     * @param  array<string, array{
     *   slip_entry_id: string,
     *   updated_at: string|null,
     * }>  $destinationSlipEntries
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function importSlipEntry($sourceUrl, $accessToken, $bookId, $slipId, array $slipEntryHead, array $destinationSlipEntries): array
    {
        $slipEntryId = $slipEntryHead['slip_entry_id'];
        $mode = null;
        $result = null;
        $error = null;

        if (key_exists($slipEntryId, $destinationSlipEntries)) {
            $sourceUpdateAt = $slipEntryHead['updated_at'];
            $destinationUpdateAt = $destinationSlipEntries[$slipEntryId]['updated_at'];
            if ($this->tools->isSourceLater($sourceUpdateAt, $destinationUpdateAt)) {
                $mode = 'update';
            }
        } else {
            $mode = 'create';
        }
        if (isset($mode)) {
            $url = $sourceUrl.'/'.$bookId.'/slips/'.$slipId.'/entries/'.$slipEntryId;
            $response = $this->tools->getFromExporter($url, $accessToken);
            if ($response->ok()) {
                /** @var array{
                 *   version: string,
                 *   books: array<string, array{
                 *     slips: array<string, array{
                 *       entries: array<string, array{
                 *         slip_entry_id: string,
                 *         slip_id: string,
                 *         debit: string,
                 *         credit: string,
                 *         amount: int,
                 *         client: string,
                 *         outline: string,
                 *         display_order: int|null,
                 *         updated_at: string|null,
                 *         deleted: bool,
                 *       }>,
                 *     }>,
                 *   }>,
                 * }|null $responseBody
                 */
                $responseBody = $response->json();
                if (isset($responseBody)) {
                    $slipEntry = $responseBody['books'][$bookId]['slips'][$slipId]['entries'][$slipEntryId];
                    switch($mode) {
                        case 'update':
                            $this->slipEntry->updateForImporting($slipEntry);
                            $result = 'updated';
                            break;
                        case 'create':
                            $this->slipEntry->createForImporting($slipEntry);
                            $result = 'created';
                            break;
                        default:
                            break;
                    }
                } else {
                    $error = 'No response data. '.$url;
                }
            } else {
                $error = 'Response error('.$response->status().'). '.$url;
            }
        } else {
            $result = 'already up-to-date';
        }

        return [['slip_entry_id' => $slipEntryId, 'result' => $result], $error];
    }

    /**
     * Import slips of the book.
     *
     * @param  string  $sourceUrl
     * @param  string  $accessToken
     * @param  string  $bookId
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function importSlips($sourceUrl, $accessToken, $bookId): array
    {
        $result = [];
        $error = null;

        $destinationSlips = $this->exportSlips($bookId);
        $url = $sourceUrl.'/'.$bookId.'/slips';
        $response = $this->tools->getFromExporter($url, $accessToken);
        if ($response->ok()) {
            /** @var array{
             *   version: string,
             *   books: array<string, array{
             *     slips: array<string, array{
             *       slip_id: string,
             *       updated_at: string|null,
             *     }>,
             *   }>,
             * }|null $responseBody
             */
            $responseBody = $response->json();
            if (isset($responseBody)) {
                $sourceSlips = $responseBody['books'][$bookId]['slips'];
                $slipNumber = count($sourceSlips);
                $slipCount = 0;
                foreach ($sourceSlips as $slipId => $slip) {
                    [$result[$slipId], $error] = $this->importSlip(
                        $sourceUrl, $accessToken, $bookId, $slip, $destinationSlips
                    );
                    if (isset($error)) {
                        break;
                    }
                    Log::debug('import: skip             '.sprintf('%5d', $slipCount).'/'.sprintf('%5d', $slipNumber).' '.$slipId.' '.$result[$slipId]['result']);
                    $slipCount++;
                    [$result[$slipId]['entries'], $error] = $this->importSlipEntries(
                        $sourceUrl, $accessToken, $bookId, $slipId
                    );
                    if (isset($error)) {
                        break;
                    }
                }
            } else {
                $error = 'No response data. '.$url;
            }
        } else {
            $error = 'Response error('.$response->status().'). '.$url;
        }

        return [$result, $error];
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

        $newSlip = $this->validateSlip($slip);
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
    public function loadSlipEntries($bookId, $slipId, $slipEntries): array
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

        $newSlipEntry = $this->validateSlipEntry($slipEntry);
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
    public function loadSlips($bookId, $slips): array
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

    /**
     * Validate the slip.
     *
     * @param  array<string, mixed>  $slip
     * @return array{
     *   slip_id: string,
     *   book_id: string,
     *   slip_outline: string,
     *   slip_memo: string|null,
     *   date: string,
     *   is_draft: bool,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }|null
     */
    private function validateSlip(array $slip): ?array
    {
        if (! key_exists('slip_id', $slip) || ! is_string($slip['slip_id'])) {
            return null;
        }
        if (! key_exists('book_id', $slip) || ! is_string($slip['book_id'])) {
            return null;
        }
        if (! key_exists('slip_outline', $slip) || ! is_string($slip['slip_outline'])) {
            return null;
        }
        if (! key_exists('slip_memo', $slip) ||
                (! is_string($slip['slip_memo']) && ! is_null($slip['slip_memo']))) {
            return null;
        }
        if (! key_exists('date', $slip) || ! is_string($slip['date'])) {
            return null;
        }
        if (! key_exists('is_draft', $slip) || ! is_int($slip['is_draft'])) {
            return null;
        }
        if (! key_exists('display_order', $slip) ||
                (! is_int($slip['display_order']) && ! is_null($slip['display_order']))) {
            return null;
        }
        if (! key_exists('updated_at', $slip) ||
                (! is_string($slip['updated_at']) && ! is_null($slip['updated_at']))) {
            return null;
        }
        if (! key_exists('deleted', $slip) || ! is_bool($slip['deleted'])) {
            return null;
        }

        return [
            'slip_id'       => $slip['slip_id'],
            'book_id'       => $slip['book_id'],
            'slip_outline'  => $slip['slip_outline'],
            'slip_memo'     => $slip['slip_memo'],
            'date'          => $slip['date'],
            'is_draft'      => boolval($slip['is_draft']),
            'display_order' => $slip['display_order'],
            'updated_at'    => $slip['updated_at'],
            'deleted'       => $slip['deleted'],
        ];
    }

    /**
     * Validate the slip entry.
     *
     * @param  array<string, mixed>  $slipEntry
     * @return array{
     *   slip_entry_id: string,
     *   slip_id: string,
     *   debit: string,
     *   credit: string,
     *   amount: int,
     *   client: string,
     *   outline: string,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }|null
     */
    private function validateSlipEntry(array $slipEntry): ?array
    {
        if (! key_exists('slip_entry_id', $slipEntry) || ! is_string($slipEntry['slip_entry_id'])) {
            return null;
        }
        if (! key_exists('slip_id', $slipEntry) || ! is_string($slipEntry['slip_id'])) {
            return null;
        }
        if (! key_exists('debit', $slipEntry) || ! is_string($slipEntry['debit'])) {
            return null;
        }
        if (! key_exists('credit', $slipEntry) || ! is_string($slipEntry['credit'])) {
            return null;
        }
        if (! key_exists('amount', $slipEntry) || ! is_int($slipEntry['amount'])) {
            return null;
        }
        if (! key_exists('client', $slipEntry) || ! is_string($slipEntry['client'])) {
            return null;
        }
        if (! key_exists('outline', $slipEntry) || ! is_string($slipEntry['outline'])) {
            return null;
        }
        if (! key_exists('display_order', $slipEntry) ||
                (! is_int($slipEntry['display_order']) && ! is_null($slipEntry['display_order']))) {
            return null;
        }
        if (! key_exists('updated_at', $slipEntry) ||
                (! is_string($slipEntry['updated_at']) && ! is_null($slipEntry['updated_at']))) {
            return null;
        }
        if (! key_exists('deleted', $slipEntry) || ! is_bool($slipEntry['deleted'])) {
            return null;
        }

        return [
            'slip_entry_id' => $slipEntry['slip_entry_id'],
            'slip_id'       => $slipEntry['slip_id'],
            'debit'         => $slipEntry['debit'],
            'credit'        => $slipEntry['credit'],
            'amount'        => $slipEntry['amount'],
            'client'        => $slipEntry['client'],
            'outline'       => $slipEntry['outline'],
            'display_order' => $slipEntry['display_order'],
            'updated_at'    => $slipEntry['updated_at'],
            'deleted'       => $slipEntry['deleted'],
        ];
    }
}
