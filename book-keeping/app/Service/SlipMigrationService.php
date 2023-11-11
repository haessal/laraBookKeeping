<?php

namespace App\Service;

use App\DataProvider\SlipEntryRepositoryInterface;
use App\DataProvider\SlipRepositoryInterface;

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
            $slips[$slipId] = $convertedSlip;
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
                $entries[$slipEntry['slip_entry_id']] = $convertedSlipEntry;
            }
            $slips[$slipId]['entries'] = $entries;
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
                foreach ($sourceSlips as $slipId => $slip) {
                    [$result[$slipId], $error] = $this->importSlip(
                        $sourceUrl, $accessToken, $bookId, $slip, $destinationSlips
                    );
                    if (isset($error)) {
                        break;
                    }
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
}
