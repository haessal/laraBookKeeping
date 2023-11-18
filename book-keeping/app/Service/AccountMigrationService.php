<?php

namespace App\Service;

use App\DataProvider\AccountGroupRepositoryInterface;
use App\DataProvider\AccountRepositoryInterface;
use Illuminate\Support\Facades\Log;

class AccountMigrationService extends AccountService
{
    /**
     * BookKeeping migration tools instance.
     *
     * @var \App\Service\BookKeepingMigrationTools
     */
    private $tools;

    /**
     * Create a new AccountMigrationService instance.
     *
     * @param  \App\DataProvider\AccountRepositoryInterface  $account
     * @param  \App\DataProvider\AccountGroupRepositoryInterface  $accountGroup
     * @param  \App\Service\BookKeepingMigrationTools  $tools
     */
    public function __construct(AccountRepositoryInterface $account, AccountGroupRepositoryInterface $accountGroup, BookKeepingMigrationTools $tools)
    {
        parent::__construct($account, $accountGroup);
        $this->tools = $tools;
    }

    /**
     * Dump accounts of the book.
     *
     * @param  string  $bookId
     * @return array<string, array{
     *   account_group_id: string,
     *   book_id: string,
     *   account_type: string,
     *   account_group_title: string,
     *   bk_uid: int|null,
     *   account_group_bk_code: int|null,
     *   is_current: bool,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     *   items: array<string, array{
     *     account_id: string,
     *     account_group_id: string,
     *     account_title: string,
     *     description: string,
     *     selectable: bool,
     *     bk_uid: int|null,
     *     account_bk_code: int|null,
     *     display_order: int|null,
     *     updated_at: string|null,
     *     deleted: bool,
     *   }>,
     * }>
     */
    public function dumpAccounts($bookId): array
    {
        $accountGroups = [];

        /** @var array{
         *   account_group_id: string,
         *   book_id: string,
         *   account_type: string,
         *   account_group_title: string,
         *   bk_uid: int|null,
         *   account_group_bk_code: int|null,
         *   is_current: bool,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }[] $accountGroupList
         */
        $accountGroupList = $this->accountGroup->searchBookForExporting($bookId);
        foreach ($accountGroupList as $accountGroup) {
            $accountGroupId = $accountGroup['account_group_id'];
            /** @var array{
             *   account_group_id: string,
             *   book_id: string,
             *   account_type: string,
             *   account_group_title: string,
             *   bk_uid: int|null,
             *   account_group_bk_code: int|null,
             *   is_current: bool,
             *   display_order: int|null,
             *   updated_at: string|null,
             *   deleted: bool,
             * } $convertedAccountGroup
             */
            $convertedAccountGroup = $this->tools->convertExportedTimestamps($accountGroup);
            $accountGroups[$accountGroupId] = $convertedAccountGroup;
            $accountItems = [];
            /** @var array{
             *   account_id: string,
             *   account_group_id: string,
             *   account_title: string,
             *   description: string,
             *   selectable: bool,
             *   bk_uid: int|null,
             *   account_bk_code: int|null,
             *   display_order: int|null,
             *   created_at: string|null,
             *   updated_at: string|null,
             *   deleted_at: string|null,
             * }[] $accountItemList
             */
            $accountItemList = $this->account->searchAccountGropupForExporting($accountGroupId);
            foreach ($accountItemList as $accountItem) {
                /** @var array{
                 *   account_id: string,
                 *   account_group_id: string,
                 *   account_title: string,
                 *   description: string,
                 *   selectable: bool,
                 *   bk_uid: int|null,
                 *   account_bk_code: int|null,
                 *   display_order: int|null,
                 *   updated_at: string|null,
                 *   deleted: bool,
                 * } $convertedAccountItem
                 */
                $convertedAccountItem = $this->tools->convertExportedTimestamps($accountItem);
                $accountItems[$accountItem['account_id']] = $convertedAccountItem;
            }
            $accountGroups[$accountGroupId]['items'] = $accountItems;
        }

        return $accountGroups;
    }

    /**
     * Export the account group.
     *
     * @param  string  $bookId
     * @param  string  $accountGroupId
     * @return array<string, array{
     *   account_group_id: string,
     *   book_id: string,
     *   account_type: string,
     *   account_group_title: string,
     *   bk_uid: int|null,
     *   account_group_bk_code: int|null,
     *   is_current: bool,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }>
     */
    public function exportAccountGroup($bookId, $accountGroupId): array
    {
        $accountGroups = [];

        /** @var array{
         *   account_group_id: string,
         *   book_id: string,
         *   account_type: string,
         *   account_group_title: string,
         *   bk_uid: int|null,
         *   account_group_bk_code: int|null,
         *   is_current: bool,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }[] $accountGroupList
         */
        $accountGroupList = $this->accountGroup->searchBookForExporting($bookId, $accountGroupId);
        foreach ($accountGroupList as $accountGroup) {
            /** @var array{
             *   account_group_id: string,
             *   book_id: string,
             *   account_type: string,
             *   account_group_title: string,
             *   bk_uid: int|null,
             *   account_group_bk_code: int|null,
             *   is_current: bool,
             *   display_order: int|null,
             *   updated_at: string|null,
             *   deleted: bool,
             * } $convertedAccountGroup
             */
            $convertedAccountGroup = $this->tools->convertExportedTimestamps($accountGroup);
            $accountGroups[$accountGroup['account_group_id']] = $convertedAccountGroup;
        }

        return $accountGroups;
    }

    /**
     * Export the account item.
     *
     * @param  string  $bookId
     * @param  string  $accountGroupId
     * @param  string  $accountId
     * @return array<string, array{
     *   items: array<string, array{
     *     account_id: string,
     *     account_group_id: string,
     *     account_title: string,
     *     description: string,
     *     selectable: bool,
     *     bk_uid: int|null,
     *     account_bk_code: int|null,
     *     display_order: int|null,
     *     updated_at: string|null,
     *     deleted: bool,
     *   }>,
     * }>
     */
    public function exportAccountItem($bookId, $accountGroupId, $accountId): array
    {
        $accountGroups = [];

        /** @var array{
         *   account_group_id: string,
         *   book_id: string,
         *   account_type: string,
         *   account_group_title: string,
         *   bk_uid: int|null,
         *   account_group_bk_code: int|null,
         *   is_current: bool,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }[] $accountGroupList
         */
        $accountGroupList = $this->accountGroup->searchBookForExporting($bookId, $accountGroupId);
        foreach ($accountGroupList as $accountGroup) {
            $accountItems = [];
            /** @var array{
             *   account_id: string,
             *   account_group_id: string,
             *   account_title: string,
             *   description: string,
             *   selectable: bool,
             *   bk_uid: int|null,
             *   account_bk_code: int|null,
             *   display_order: int|null,
             *   created_at: string|null,
             *   updated_at: string|null,
             *   deleted_at: string|null,
             * }[] $accountItemList
             */
            $accountItemList = $this->account->searchAccountGropupForExporting($accountGroupId, $accountId);
            foreach ($accountItemList as $accountItem) {
                /** @var array{
                 *   account_id: string,
                 *   account_group_id: string,
                 *   account_title: string,
                 *   description: string,
                 *   selectable: bool,
                 *   bk_uid: int|null,
                 *   account_bk_code: int|null,
                 *   display_order: int|null,
                 *   updated_at: string|null,
                 *   deleted: bool,
                 * } $convertedAccountItem
                 */
                $convertedAccountItem = $this->tools->convertExportedTimestamps($accountItem);
                $accountItems[$accountItem['account_id']] = $convertedAccountItem;
            }
            $accountGroups[$accountGroup['account_group_id']] = ['items' => $accountItems];
        }

        return $accountGroups;
    }

    /**
     * Export a list of account items belonging to the account group.
     *
     * @param  string  $bookId
     * @param  string  $accountGroupId
     * @return array<string, array{
     *   items: array<string, array{
     *     account_id: string,
     *     updated_at: string|null,
     *   }>,
     * }>
     */
    public function exportAccountItems($bookId, $accountGroupId): array
    {
        $accountGroups = [];

        /** @var array{
         *   account_group_id: string,
         *   book_id: string,
         *   account_type: string,
         *   account_group_title: string,
         *   bk_uid: int|null,
         *   account_group_bk_code: int|null,
         *   is_current: bool,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }[] $accountGroupList
         */
        $accountGroupList = $this->accountGroup->searchBookForExporting($bookId, $accountGroupId);
        foreach ($accountGroupList as $accountGroup) {
            $accountItems = [];
            /** @var array{
             *   account_id: string,
             *   account_group_id: string,
             *   account_title: string,
             *   description: string,
             *   selectable: bool,
             *   bk_uid: int|null,
             *   account_bk_code: int|null,
             *   display_order: int|null,
             *   created_at: string|null,
             *   updated_at: string|null,
             *   deleted_at: string|null,
             * }[] $accountItemList
             */
            $accountItemList = $this->account->searchAccountGropupForExporting(strval($accountGroup['account_group_id']));
            foreach ($accountItemList as $accountItem) {
                $accountItems[$accountItem['account_id']] = [
                    'account_id' => $accountItem['account_id'],
                    'updated_at' => $accountItem['updated_at'],
                ];
            }
            $accountGroups[$accountGroup['account_group_id']] = ['items' => $accountItems];
        }

        return $accountGroups;
    }

    /**
     * Export accounts of the book.
     *
     * @param  string  $bookId
     * @return array<string, array{
     *   account_group_id: string,
     *   updated_at: string|null,
     * }>
     */
    public function exportAccounts($bookId): array
    {
        $accountGroups = [];

        /** @var array{
         *   account_group_id: string,
         *   book_id: string,
         *   account_type: string,
         *   account_group_title: string,
         *   bk_uid: int|null,
         *   account_group_bk_code: int|null,
         *   is_current: bool,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }[] $accountGroupList
         */
        $accountGroupList = $this->accountGroup->searchBookForExporting($bookId);
        foreach ($accountGroupList as $accountGroup) {
            $accountGroupId = $accountGroup['account_group_id'];
            $accountGroups[$accountGroupId] = [
                'account_group_id' => $accountGroup['account_group_id'],
                'updated_at'       => $accountGroup['updated_at'],
            ];
        }

        return $accountGroups;
    }

    /**
     * Import the account group.
     *
     * @param  string  $sourceUrl
     * @param  string  $accessToken
     * @param  string  $bookId
     * @param  array{
     *   account_group_id: string,
     *   updated_at: string|null,
     * }  $accountGroupHead
     * @param  array<string, array{
     *   account_group_id: string,
     *   updated_at: string|null,
     * }>  $destinationAccountGroups
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function importAccountGroup($sourceUrl, $accessToken, $bookId, array $accountGroupHead, array $destinationAccountGroups): array
    {
        $accountGroupId = $accountGroupHead['account_group_id'];
        $mode = null;
        $result = null;
        $error = null;

        if (key_exists($accountGroupId, $destinationAccountGroups)) {
            $sourceUpdateAt = $accountGroupHead['updated_at'];
            $destinationUpdateAt = $destinationAccountGroups[$accountGroupId]['updated_at'];
            if ($this->tools->isSourceLater($sourceUpdateAt, $destinationUpdateAt)) {
                $mode = 'update';
            }
        } else {
            $mode = 'create';
        }
        if (isset($mode)) {
            $url = $sourceUrl.'/'.$bookId.'/accounts/'.$accountGroupId;
            $response = $this->tools->getFromExporter($url, $accessToken);
            if ($response->ok()) {
                /** @var array{
                 *   version: string,
                 *   books: array<string, array{
                 *     accounts: array<string, array{
                 *       account_group_id: string,
                 *       book_id: string,
                 *       account_type: string,
                 *       account_group_title: string,
                 *       bk_uid: int|null,
                 *       account_group_bk_code: int|null,
                 *       is_current: bool,
                 *       display_order: int|null,
                 *       updated_at: string|null,
                 *       deleted: bool,
                 *     }>,
                 *   }>,
                 * }|null $responseBody
                 */
                $responseBody = $response->json();
                if (isset($responseBody)) {
                    $accountGroup = $responseBody['books'][$bookId]['accounts'][$accountGroupId];
                    switch($mode) {
                        case 'update':
                            $this->accountGroup->updateForImporting($accountGroup);
                            $result = 'updated';
                            break;
                        case 'create':
                            $this->accountGroup->createForImporting($accountGroup);
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

        return [['account_group_id' => $accountGroupId, 'result' => $result], $error];
    }

    /**
     * Import the account item.
     *
     * @param  string  $sourceUrl
     * @param  string  $accessToken
     * @param  string  $bookId
     * @param  string  $accountGroupId
     * @param  array{
     *   account_id: string,
     *   updated_at: string|null,
     * }  $accountItemHead
     * @param  array<string, array{
     *   account_id: string,
     *   updated_at: string|null,
     * }>  $destinationAccountItems
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function importAccountItem($sourceUrl, $accessToken, $bookId, $accountGroupId, array $accountItemHead, array $destinationAccountItems): array
    {
        $accountId = $accountItemHead['account_id'];
        $mode = null;
        $result = null;
        $error = null;

        if (key_exists($accountId, $destinationAccountItems)) {
            $sourceUpdateAt = $accountItemHead['updated_at'];
            $destinationUpdateAt = $destinationAccountItems[$accountId]['updated_at'];
            if ($this->tools->isSourceLater($sourceUpdateAt, $destinationUpdateAt)) {
                $mode = 'update';
            }
        } else {
            $mode = 'create';
        }
        if (isset($mode)) {
            $url = $sourceUrl.'/'.$bookId.'/accounts/'.$accountGroupId.'/items/'.$accountId;
            $response = $this->tools->getFromExporter($url, $accessToken);
            if ($response->ok()) {
                /** @var array{
                 *   version: string,
                 *   books: array<string, array{
                 *     accounts: array<string, array{
                 *       items: array<string, array{
                 *         account_id: string,
                 *         account_group_id: string,
                 *         account_title: string,
                 *         description: string,
                 *         selectable: bool,
                 *         bk_uid: int|null,
                 *         account_bk_code: int|null,
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
                    $accountItem = $responseBody['books'][$bookId]['accounts'][$accountGroupId]['items'][$accountId];
                    switch($mode) {
                        case 'update':
                            $this->account->updateForImporting($accountItem);
                            $result = 'updated';
                            break;
                        case 'create':
                            $this->account->createForImporting($accountItem);
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

        return [['account_id' => $accountId, 'result' => $result], $error];
    }

    /**
     * Import a list of account items belonging to the account group.
     *
     * @param  string  $sourceUrl
     * @param  string  $accessToken
     * @param  string  $bookId
     * @param  string  $accountGroupId
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function importAccountItems($sourceUrl, $accessToken, $bookId, $accountGroupId): array
    {
        $result = [];
        $error = null;

        $destinationAccountItems = $this->exportAccountItems($bookId, $accountGroupId);
        if (! key_exists($accountGroupId, $destinationAccountItems)) {
            $error = 'The account group that the items are bound to is not exist. '.$accountGroupId;

            return [$result, $error];
        }

        $url = $sourceUrl.'/'.$bookId.'/accounts/'.$accountGroupId.'/items';
        $response = $this->tools->getFromExporter($url, $accessToken);
        if ($response->ok()) {
            /** @var array{
             *   version: string,
             *   books: array<string, array{
             *     accounts: array<string, array{
             *       items: array<string, array{
             *         account_id: string,
             *         updated_at: string|null,
             *       }>,
             *     }>,
             *   }>,
             * }|null $responseBody
             */
            $responseBody = $response->json();
            if (isset($responseBody)) {
                $sourceAccountItems = $responseBody['books'][$bookId]['accounts'][$accountGroupId]['items'];
                $accountItemNumber = count($sourceAccountItems);
                $accountItemCount = 0;
                foreach ($sourceAccountItems as $accountId => $accountItem) {
                    [$result[$accountId], $error] = $this->importAccountItem(
                        $sourceUrl,
                        $accessToken,
                        $bookId,
                        $accountGroupId,
                        $accountItem,
                        $destinationAccountItems[$accountGroupId]['items']
                    );
                    if (isset($error)) {
                        break;
                    }
                    Log::debug('import: account item     '.sprintf('%2d', $accountItemCount).'/'.sprintf('%2d', $accountItemNumber).' '.$accountId.' '.$result[$accountId]['result']);
                    $accountItemCount++;
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
     * Import accounts of the book.
     *
     * @param  string  $sourceUrl
     * @param  string  $accessToken
     * @param  string  $bookId
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function importAccounts($sourceUrl, $accessToken, $bookId): array
    {
        $result = [];
        $error = null;

        $destinationAccountGroups = $this->exportAccounts($bookId);
        $url = $sourceUrl.'/'.$bookId.'/accounts';
        $response = $this->tools->getFromExporter($url, $accessToken);
        if ($response->ok()) {
            /** @var array{
             *   version: string,
             *   books: array<string, array{
             *     accounts: array<string, array{
             *       account_group_id: string,
             *       updated_at: string|null,
             *     }>,
             *   }>,
             * }|null $responseBody
             */
            $responseBody = $response->json();
            if (isset($responseBody)) {
                $sourceAccountGropus = $responseBody['books'][$bookId]['accounts'];
                $accountGroupNumber = count($sourceAccountGropus);
                $accountGroupCount = 0;
                foreach ($sourceAccountGropus as $accountGroupId => $accountGroup) {
                    [$result[$accountGroupId], $error] = $this->importAccountGroup(
                        $sourceUrl, $accessToken, $bookId, $accountGroup, $destinationAccountGroups
                    );
                    if (isset($error)) {
                        break;
                    }
                    Log::debug('import: account group    '.sprintf('%2d', $accountGroupCount).'/'.sprintf('%2d', $accountGroupNumber).' '.$accountGroupId.' '.$result[$accountGroupId]['result']);
                    $accountGroupCount++;
                    [$result[$accountGroupId]['items'], $error] = $this->importAccountItems(
                        $sourceUrl, $accessToken, $bookId, $accountGroupId
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
     * Load the account group.
     *
     * @param  array<string, mixed>  $accountGroup
     * @param array<string, array{
     *   account_group_id: string,
     *   updated_at: string|null,
     * }> $destinationAccountGroups
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function loadAccountGroup(array $accountGroup, array $destinationAccountGroups): array
    {
        $mode = null;
        $result = null;
        $error = null;

        $newAccountGroup = $this->validateAccountGroup($accountGroup);
        if (is_null($newAccountGroup)) {
            $error = 'invalid data format: account group';

            return [['account_group_id' => null, 'result' => $result], $error];
        }
        $accountGroupId = $newAccountGroup['account_group_id'];
        if (key_exists($accountGroupId, $destinationAccountGroups)) {
            $sourceUpdateAt = $newAccountGroup['updated_at'];
            $destinationUpdateAt = $destinationAccountGroups[$accountGroupId]['updated_at'];
            if ($this->tools->isSourceLater($sourceUpdateAt, $destinationUpdateAt)) {
                $mode = 'update';
            }
        } else {
            $mode = 'create';
        }
        if (isset($mode)) {
            switch($mode) {
                case 'update':
                    $this->accountGroup->updateForImporting($newAccountGroup);
                    $result = 'updated';
                    break;
                case 'create':
                    $this->accountGroup->createForImporting($newAccountGroup);
                    $result = 'created';
                    break;
                default:
                    break;
            }
        } else {
            $result = 'already up-to-date';
        }

        return [['account_group_id' => $accountGroupId, 'result' => $result], $error];
    }

    /**
     * Load the account item.
     *
     * @param  array<string, mixed>  $accountItem
     * @param array<string, array{
     *   account_id: string,
     *   updated_at: string|null,
     * }>  $destinationAccountItems
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function loadAccountItem(array $accountItem, array $destinationAccountItems): array
    {
        $mode = null;
        $result = null;
        $error = null;

        $newAccountItem = $this->validateAccountItem($accountItem);
        if (is_null($newAccountItem)) {
            $error = 'invalid data format: account item';

            return [['account_id' => null, 'result' => $result], $error];
        }
        $accountId = $newAccountItem['account_id'];
        if (key_exists($accountId, $destinationAccountItems)) {
            $sourceUpdateAt = $newAccountItem['updated_at'];
            $destinationUpdateAt = $destinationAccountItems[$accountId]['updated_at'];
            if ($this->tools->isSourceLater($sourceUpdateAt, $destinationUpdateAt)) {
                $mode = 'update';
            }
        } else {
            $mode = 'create';
        }
        if (isset($mode)) {
            switch($mode) {
                case 'update':
                    $this->account->updateForImporting($newAccountItem);
                    $result = 'updated';
                    break;
                case 'create':
                    $this->account->createForImporting($newAccountItem);
                    $result = 'created';
                    break;
                default:
                    break;
            }
        } else {
            $result = 'already up-to-date';
        }

        return [['account_id' => $accountId, 'result' => $result], $error];
    }

    /**
     * Load the account items belonging to the account group.
     *
     * @param  string  $bookId
     * @param  string  $accountGroupId
     * @param  array<string, array<string, mixed>>  $accountItems
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function loadAccountItems($bookId, $accountGroupId, $accountItems): array
    {
        $result = [];
        $error = null;

        $destinationAccountItems = $this->exportAccountItems($bookId, $accountGroupId);
        if (! key_exists($accountGroupId, $destinationAccountItems)) {
            $error = 'The account group that the items are bound to is not exist. '.$accountGroupId;

            return [$result, $error];
        }
        $accountItemNumber = count($accountItems);
        $accountItemCount = 0;
        foreach ($accountItems as $accountId => $accountItem) {
            [$result[$accountId], $error] = $this->loadAccountItem(
                $accountItem, $destinationAccountItems[$accountGroupId]['items']
            );
            if (isset($error)) {
                break;
            }
            Log::debug('load: account item     '.sprintf('%2d', $accountItemCount).'/'.sprintf('%2d', $accountItemNumber).' '.$accountId.' '.$result[$accountId]['result']);
            $accountItemCount++;
        }

        return [$result, $error];
    }

    /**
     * Load the accounts of the book.
     *
     * @param  string  $bookId
     * @param  array<string, array<string, mixed>>  $accounts
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function loadAccounts($bookId, $accounts): array
    {
        $result = [];
        $error = null;

        $destinationAccountGroups = $this->exportAccounts($bookId);
        $accountGroupNumber = count($accounts);
        $accountGroupCount = 0;
        foreach ($accounts as $accountGroupId => $accountGroup) {
            if (key_exists('account_group_id', $accountGroup)) {
                [$result[$accountGroupId], $error] = $this->loadAccountGroup($accountGroup, $destinationAccountGroups);
                if (isset($error)) {
                    break;
                }
                Log::debug('load: account group    '.sprintf('%2d', $accountGroupCount).'/'.sprintf('%2d', $accountGroupNumber).' '.$accountGroupId.' '.$result[$accountGroupId]['result']);
            }
            $accountGroupCount++;
            if (key_exists('items', $accountGroup)) {
                if (is_array($accountGroup['items'])) {
                    [$result[$accountGroupId]['items'], $error] = $this->loadAccountItems(
                        $bookId, $accountGroupId, $accountGroup['items']
                    );
                    if (isset($error)) {
                        break;
                    }
                } else {
                    $error = 'invalid data format: account items';
                }
            }
        }

        return [$result, $error];
    }

    /**
     * Validate the account group.
     *
     * @param  array<string, mixed>  $accountGroup
     * @return array{
     *   account_group_id: string,
     *   book_id: string,
     *   account_type: string,
     *   account_group_title: string,
     *   bk_uid: int|null,
     *   account_group_bk_code: int|null,
     *   is_current: bool,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }|null
     */
    private function validateAccountGroup(array $accountGroup): ?array
    {
        if (! key_exists('account_group_id', $accountGroup) || ! is_string($accountGroup['account_group_id'])) {
            return null;
        }
        if (! key_exists('book_id', $accountGroup) || ! is_string($accountGroup['book_id'])) {
            return null;
        }
        if (! key_exists('account_type', $accountGroup) || ! is_string($accountGroup['account_type'])) {
            return null;
        }
        if (! key_exists('account_group_title', $accountGroup) || ! is_string($accountGroup['account_group_title'])) {
            return null;
        }
        if (! key_exists('bk_uid', $accountGroup) ||
                (! is_int($accountGroup['bk_uid']) && ! is_null($accountGroup['bk_uid']))) {
            return null;
        }
        if (! key_exists('account_group_bk_code', $accountGroup) ||
                (! is_int($accountGroup['account_group_bk_code']) && ! is_null($accountGroup['account_group_bk_code']))) {
            return null;
        }
        if (! key_exists('is_current', $accountGroup) || ! is_int($accountGroup['is_current'])) {
            return null;
        }
        if (! key_exists('display_order', $accountGroup) ||
                (! is_int($accountGroup['display_order']) && ! is_null($accountGroup['display_order']))) {
            return null;
        }
        if (! key_exists('updated_at', $accountGroup) ||
                (! is_string($accountGroup['updated_at']) && ! is_null($accountGroup['updated_at']))) {
            return null;
        }
        if (! key_exists('deleted', $accountGroup) || ! is_bool($accountGroup['deleted'])) {
            return null;
        }

        return [
            'account_group_id'      => $accountGroup['account_group_id'],
            'book_id'               => $accountGroup['book_id'],
            'account_type'          => $accountGroup['account_type'],
            'account_group_title'   => $accountGroup['account_group_title'],
            'bk_uid'                => $accountGroup['bk_uid'],
            'account_group_bk_code' => $accountGroup['account_group_bk_code'],
            'is_current'            => boolval($accountGroup['is_current']),
            'display_order'         => $accountGroup['display_order'],
            'updated_at'            => $accountGroup['updated_at'],
            'deleted'               => $accountGroup['deleted'],
        ];
    }

    /**
     * Validate the account item.
     *
     * @param  array<string, mixed>  $accountItem
     * @return array{
     *   account_id: string,
     *   account_group_id: string,
     *   account_title: string,
     *   description: string,
     *   selectable: bool,
     *   bk_uid: int|null,
     *   account_bk_code: int|null,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }|null
     */
    private function validateAccountItem(array $accountItem): ?array
    {
        if (! key_exists('account_id', $accountItem) || ! is_string($accountItem['account_id'])) {
            return null;
        }
        if (! key_exists('account_group_id', $accountItem) || ! is_string($accountItem['account_group_id'])) {
            return null;
        }
        if (! key_exists('account_title', $accountItem) || ! is_string($accountItem['account_title'])) {
            return null;
        }
        if (! key_exists('description', $accountItem) || ! is_string($accountItem['description'])) {
            return null;
        }
        if (! key_exists('selectable', $accountItem) || ! is_int($accountItem['selectable'])) {
            return null;
        }
        if (! key_exists('bk_uid', $accountItem) ||
                (! is_int($accountItem['bk_uid']) && ! is_null($accountItem['bk_uid']))) {
            return null;
        }
        if (! key_exists('account_bk_code', $accountItem) ||
                (! is_int($accountItem['account_bk_code']) && ! is_null($accountItem['account_bk_code']))) {
            return null;
        }
        if (! key_exists('display_order', $accountItem) ||
                (! is_int($accountItem['display_order']) && ! is_null($accountItem['display_order']))) {
            return null;
        }
        if (! key_exists('updated_at', $accountItem) ||
                (! is_string($accountItem['updated_at']) && ! is_null($accountItem['updated_at']))) {
            return null;
        }
        if (! key_exists('deleted', $accountItem) || ! is_bool($accountItem['deleted'])) {
            return null;
        }

        return [
            'account_id'       => $accountItem['account_id'],
            'account_group_id' => $accountItem['account_group_id'],
            'account_title'    => $accountItem['account_title'],
            'description'      => $accountItem['description'],
            'selectable'       => boolval($accountItem['selectable']),
            'bk_uid'           => $accountItem['bk_uid'],
            'account_bk_code'  => $accountItem['account_bk_code'],
            'display_order'    => $accountItem['display_order'],
            'updated_at'       => $accountItem['updated_at'],
            'deleted'          => $accountItem['deleted'],
        ];
    }
}
