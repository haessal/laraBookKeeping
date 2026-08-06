<?php

namespace App\Service;

use App\Repositories\AccountGroupRepositoryInterface;
use App\Repositories\AccountRepositoryInterface;
use Illuminate\Support\Facades\Log;

class AccountMigrationLoaderService extends AccountMigrationService
{
    /**
     * Validator for loading.
     *
     * @var \App\Service\BookKeepingMigrationValidator
     */
    private $validator;

    /**
     * Create a new AccountMigrationService instance.
     *
     * @param  \App\Repositories\AccountRepositoryInterface  $account
     * @param  \App\Repositories\AccountGroupRepositoryInterface  $accountGroup
     * @param  \App\Service\BookKeepingMigrationTools  $tools
     * @param  \App\Service\BookKeepingMigrationValidator  $validator
     */
    public function __construct(AccountRepositoryInterface $account, AccountGroupRepositoryInterface $accountGroup, BookKeepingMigrationTools $tools, BookKeepingMigrationValidator $validator)
    {
        parent::__construct($account, $accountGroup, $tools);
        $this->validator = $validator;
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

        $newAccountGroup = $this->validator->validateAccountGroup($accountGroup);
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
     * @param  \App\Service\BookKeepingMigrationVersion  $version
     * @param  array<string, mixed>  $accountItem
     * @param array<string, array{
     *   account_id: string,
     *   updated_at: string|null,
     * }>  $destinationAccountItems
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function loadAccountItem(BookKeepingMigrationVersion $version, array $accountItem, array $destinationAccountItems): array
    {
        $mode = null;
        $result = null;
        $error = null;

        $newAccountItem = $this->validator->validateAccountItem($version, $accountItem);
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
     * @param  \App\Service\BookKeepingMigrationVersion  $version
     * @param  string  $bookId
     * @param  string  $accountGroupId
     * @param  array<string, array<string, mixed>>  $accountItems
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function loadAccountItems(BookKeepingMigrationVersion $version, $bookId, $accountGroupId, array $accountItems): array
    {
        $result = [];
        $error = null;

        $destinationAccountItems = $this->exportAccountItems($bookId, $accountGroupId);
        if (! key_exists($accountGroupId, $destinationAccountItems)) {
            $error = 'The account group that the items are bound to does not exist. '.$accountGroupId;

            return [$result, $error];
        }
        $accountItemNumber = count($accountItems);
        $accountItemCount = 0;
        foreach ($accountItems as $accountIndex => $accountItem) {
            if (key_exists('account_id', $accountItem)) {
                /** @var string $accountId */
                $accountId = $accountItem['account_id'];
            } else {
                $error = 'invalid data format: account_id';
                break;
            }
            if (key_exists('account', $accountItem) && is_array($accountItem['account'])) {
                /** @var array<string, mixed> $account */
                $account = $accountItem['account'];
                [$result[$accountIndex], $error] = $this->loadAccountItem(
                    $version, $account, $destinationAccountItems[$accountGroupId]['items']
                );
                if (isset($error)) {
                    break;
                }
                /** @var string $result_for_log */
                $result_for_log = key_exists('result', $result[$accountIndex])
                    ? $result[$accountIndex]['result']
                    : 'null';
                Log::debug('load: account item     '
                    .sprintf('%2d', $accountItemCount)
                    .'/'
                    .sprintf('%2d', $accountItemNumber)
                    .' '
                    .$accountId
                    .' '
                    .$result_for_log
                );
            }
            $accountItemCount++;
        }

        return [$result, $error];
    }

    /**
     * Load the accounts of the book.
     *
     * @param  \App\Service\BookKeepingMigrationVersion  $version
     * @param  string  $bookId
     * @param  array<string, array<string, mixed>>  $accounts
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function loadAccounts(BookKeepingMigrationVersion $version, $bookId, array $accounts): array
    {
        $result = [];
        $error = null;

        $destinationAccountGroups = $this->exportAccounts($bookId);
        $accountGroupNumber = count($accounts);
        $accountGroupCount = 0;
        foreach ($accounts as $accountGroupIndex => $accountGroup) {
            if (key_exists('account_group_id', $accountGroup) && is_string($accountGroup['account_group_id'])) {
                /** @var string $accountGroupId */
                $accountGroupId = $accountGroup['account_group_id'];
            } else {
                $error = 'invalid data format: account_group_id';
                break;
            }
            if (key_exists('account_group', $accountGroup) && is_array($accountGroup['account_group'])) {
                /** @var array<string, mixed> $accountGroupData */
                $accountGroupData = $accountGroup['account_group'];
                [$result[$accountGroupIndex], $error] = $this->loadAccountGroup(
                    $accountGroupData, $destinationAccountGroups
                );
                if (isset($error)) {
                    break;
                }
                /** @var string $result_for_log */
                $result_for_log = key_exists('result', $result[$accountGroupIndex])
                    ? $result[$accountGroupIndex]['result']
                    : 'null';
                Log::debug('load: account group    '
                    .sprintf('%2d', $accountGroupCount)
                    .'/'
                    .sprintf('%2d', $accountGroupNumber)
                    .' '
                    .$accountGroupId
                    .' '
                    .$result_for_log
                );
            }
            $accountGroupCount++;
            if (key_exists('items', $accountGroup)) {
                if (is_array($accountGroup['items'])) {
                    /** @var array<string, array<string, mixed>> $accountItems */
                    $accountItems = $accountGroup['items'];
                    [$result[$accountGroupIndex]['items'], $error] = $this->loadAccountItems(
                        $version, $bookId, $accountGroupId, $accountItems
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
}
