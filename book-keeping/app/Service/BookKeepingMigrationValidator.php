<?php

namespace App\Service;

use Illuminate\Support\Carbon;

class BookKeepingMigrationValidator
{
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
    public function validateAccountGroup(array $accountGroup): ?array
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
            'account_group_id' => $accountGroup['account_group_id'],
            'book_id' => $accountGroup['book_id'],
            'account_type' => $accountGroup['account_type'],
            'account_group_title' => $accountGroup['account_group_title'],
            'bk_uid' => $accountGroup['bk_uid'],
            'account_group_bk_code' => $accountGroup['account_group_bk_code'],
            'is_current' => boolval($accountGroup['is_current']),
            'display_order' => $accountGroup['display_order'],
            'updated_at' => $accountGroup['updated_at'],
            'deleted' => $accountGroup['deleted'],
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
    public function validateAccountItem(array $accountItem): ?array
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
            'account_id' => $accountItem['account_id'],
            'account_group_id' => $accountItem['account_group_id'],
            'account_title' => $accountItem['account_title'],
            'description' => $accountItem['description'],
            'selectable' => boolval($accountItem['selectable']),
            'bk_uid' => $accountItem['bk_uid'],
            'account_bk_code' => $accountItem['account_bk_code'],
            'display_order' => $accountItem['display_order'],
            'updated_at' => $accountItem['updated_at'],
            'deleted' => $accountItem['deleted'],
        ];
    }

    /**
     * Validate the book information.
     *
     * @param  array<string, mixed>  $bookInformation
     * @return array{
     *   book_id: string,
     *   book_name: string,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }|null
     */
    public function validateBookInformation(array $bookInformation): ?array
    {
        if (! key_exists('book_id', $bookInformation) || ! is_string($bookInformation['book_id'])) {
            return null;
        }
        if (! key_exists('book_name', $bookInformation) || ! is_string($bookInformation['book_name'])) {
            return null;
        }
        if (! key_exists('display_order', $bookInformation) ||
                (! is_int($bookInformation['display_order']) && ! is_null($bookInformation['display_order']))) {
            return null;
        }
        if (! key_exists('updated_at', $bookInformation) ||
                (! is_string($bookInformation['updated_at']) && ! is_null($bookInformation['updated_at']))) {
            return null;
        }
        if (! key_exists('deleted', $bookInformation) || ! is_bool($bookInformation['deleted'])) {
            return null;
        }

        return [
            'book_id' => $bookInformation['book_id'],
            'book_name' => $bookInformation['book_name'],
            'display_order' => $bookInformation['display_order'],
            'updated_at' => $bookInformation['updated_at'],
            'deleted' => $bookInformation['deleted'],
        ];
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
    public function validateSlip(array $slip): ?array
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
            'slip_id' => $slip['slip_id'],
            'book_id' => $slip['book_id'],
            'slip_outline' => $slip['slip_outline'],
            'slip_memo' => $slip['slip_memo'],
            'date' => $slip['date'],
            'is_draft' => boolval($slip['is_draft']),
            'display_order' => $slip['display_order'],
            'updated_at' => $slip['updated_at'],
            'deleted' => $slip['deleted'],
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
    public function validateSlipEntry(array $slipEntry): ?array
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
            'slip_id' => $slipEntry['slip_id'],
            'debit' => $slipEntry['debit'],
            'credit' => $slipEntry['credit'],
            'amount' => $slipEntry['amount'],
            'client' => $slipEntry['client'],
            'outline' => $slipEntry['outline'],
            'display_order' => $slipEntry['display_order'],
            'updated_at' => $slipEntry['updated_at'],
            'deleted' => $slipEntry['deleted'],
        ];
    }
}
