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
        if (! key_exists('account_group_id', $accountGroup) || ! $this->validateUuid($accountGroup['account_group_id'])) {
            return null;
        }
        if (! key_exists('book_id', $accountGroup) || ! $this->validateUuid($accountGroup['book_id'])) {
            return null;
        }
        if (! key_exists('account_type', $accountGroup) || ! $this->validateAccountType($accountGroup['account_type'])) {
            return null;
        }
        if (! key_exists('account_group_title', $accountGroup) || ! is_string($accountGroup['account_group_title'])) {
            return null;
        }
        if (! key_exists('bk_uid', $accountGroup) || ! $this->isIntOrNull($accountGroup['bk_uid'])) {
            return null;
        }
        if (! key_exists('account_group_bk_code', $accountGroup) || ! $this->isIntOrNull($accountGroup['account_group_bk_code'])) {
            return null;
        }
        if (! key_exists('is_current', $accountGroup) || ! is_int($accountGroup['is_current'])) {
            return null;
        }
        if (! key_exists('display_order', $accountGroup) || ! $this->isIntOrNull($accountGroup['display_order'])) {
            return null;
        }
        if (! key_exists('updated_at', $accountGroup) || ! $this->validateUpdatedAt($accountGroup['updated_at'])) {
            return null;
        }
        if (! key_exists('deleted', $accountGroup) || ! is_bool($accountGroup['deleted'])) {
            return null;
        }

        return [
            'account_group_id' => strval($accountGroup['account_group_id']),
            'book_id' => strval($accountGroup['book_id']),
            'account_type' => strval($accountGroup['account_type']),
            'account_group_title' => $accountGroup['account_group_title'],
            'bk_uid' => is_null($accountGroup['bk_uid']) ? null : intval($accountGroup['bk_uid']),
            'account_group_bk_code' => is_null($accountGroup['account_group_bk_code']) ? null : intval($accountGroup['account_group_bk_code']),
            'is_current' => boolval($accountGroup['is_current']),
            'display_order' => is_null($accountGroup['display_order']) ? null : intval($accountGroup['display_order']),
            'updated_at' => is_null($accountGroup['updated_at']) ? null : strval($accountGroup['updated_at']),
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
        if (! key_exists('account_id', $accountItem) || ! $this->validateUuid($accountItem['account_id'])) {
            return null;
        }
        if (! key_exists('account_group_id', $accountItem) || ! $this->validateUuid($accountItem['account_group_id'])) {
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
        if (! key_exists('bk_uid', $accountItem) || ! $this->isIntOrNull($accountItem['bk_uid'])) {
            return null;
        }
        if (! key_exists('account_bk_code', $accountItem) || ! $this->isIntOrNull($accountItem['account_bk_code'])) {
            return null;
        }
        if (! key_exists('display_order', $accountItem) || ! $this->isIntOrNull($accountItem['display_order'])) {
            return null;
        }
        if (! key_exists('updated_at', $accountItem) || ! $this->validateUpdatedAt($accountItem['updated_at'])) {
            return null;
        }
        if (! key_exists('deleted', $accountItem) || ! is_bool($accountItem['deleted'])) {
            return null;
        }

        return [
            'account_id' => strval($accountItem['account_id']),
            'account_group_id' => strval($accountItem['account_group_id']),
            'account_title' => $accountItem['account_title'],
            'description' => $accountItem['description'],
            'selectable' => boolval($accountItem['selectable']),
            'bk_uid' => is_null($accountItem['bk_uid']) ? null : intval($accountItem['bk_uid']),
            'account_bk_code' => is_null($accountItem['account_bk_code']) ? null : intval($accountItem['account_bk_code']),
            'display_order' => is_null($accountItem['display_order']) ? null : intval($accountItem['display_order']),
            'updated_at' => is_null($accountItem['updated_at']) ? null : strval($accountItem['updated_at']),
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
        if (! key_exists('book_id', $bookInformation) || ! $this->validateUuid($bookInformation['book_id'])) {
            return null;
        }
        if (! key_exists('book_name', $bookInformation) || ! is_string($bookInformation['book_name'])) {
            return null;
        }
        if (! key_exists('display_order', $bookInformation) || ! $this->isIntOrNull($bookInformation['display_order'])) {
            return null;
        }
        if (! key_exists('updated_at', $bookInformation) || ! $this->validateUpdatedAt($bookInformation['updated_at'])) {
            return null;
        }
        if (! key_exists('deleted', $bookInformation) || ! is_bool($bookInformation['deleted'])) {
            return null;
        }

        return [
            'book_id' => strval($bookInformation['book_id']),
            'book_name' => $bookInformation['book_name'],
            'display_order' => is_null($bookInformation['display_order']) ? null : intval($bookInformation['display_order']),
            'updated_at' => is_null($bookInformation['updated_at']) ? null : strval($bookInformation['updated_at']),
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
        if (! key_exists('slip_id', $slip) || ! $this->validateUuid($slip['slip_id'])) {
            return null;
        }
        if (! key_exists('book_id', $slip) || ! $this->validateUuid($slip['book_id'])) {
            return null;
        }
        if (! key_exists('slip_outline', $slip) || ! is_string($slip['slip_outline'])) {
            return null;
        }
        if (! key_exists('slip_memo', $slip) || ! $this->isStringOrNull($slip['slip_memo'])) {
            return null;
        }
        if (! key_exists('date', $slip) || ! $this->validateDateFormat($slip['date'])) {
            return null;
        }
        if (! key_exists('is_draft', $slip) || ! is_int($slip['is_draft'])) {
            return null;
        }
        if (! key_exists('display_order', $slip) || ! $this->isIntOrNull($slip['display_order'])) {
            return null;
        }
        if (! key_exists('updated_at', $slip) || ! $this->validateUpdatedAt($slip['updated_at'])) {
            return null;
        }
        if (! key_exists('deleted', $slip) || ! is_bool($slip['deleted'])) {
            return null;
        }

        return [
            'slip_id' => strval($slip['slip_id']),
            'book_id' => strval($slip['book_id']),
            'slip_outline' => $slip['slip_outline'],
            'slip_memo' => is_null($slip['slip_memo']) ? null : strval($slip['slip_memo']),
            'date' => strval($slip['date']),
            'is_draft' => boolval($slip['is_draft']),
            'display_order' => is_null($slip['display_order']) ? null : intval($slip['display_order']),
            'updated_at' => is_null($slip['updated_at']) ? null : strval($slip['updated_at']),
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
        if (! key_exists('slip_entry_id', $slipEntry) || ! $this->validateUuid($slipEntry['slip_entry_id'])) {
            return null;
        }
        if (! key_exists('slip_id', $slipEntry) || ! $this->validateUuid($slipEntry['slip_id'])) {
            return null;
        }
        if (! key_exists('debit', $slipEntry) || ! $this->validateUuid($slipEntry['debit'])) {
            return null;
        }
        if (! key_exists('credit', $slipEntry) || ! $this->validateUuid($slipEntry['credit'])) {
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
        if (! key_exists('display_order', $slipEntry) || ! $this->isIntOrNull($slipEntry['display_order'])) {
            return null;
        }
        if (! key_exists('updated_at', $slipEntry) || ! $this->validateUpdatedAt($slipEntry['updated_at'])) {
            return null;
        }
        if (! key_exists('deleted', $slipEntry) || ! is_bool($slipEntry['deleted'])) {
            return null;
        }

        return [
            'slip_entry_id' => strval($slipEntry['slip_entry_id']),
            'slip_id' => strval($slipEntry['slip_id']),
            'debit' => strval($slipEntry['debit']),
            'credit' => strval($slipEntry['credit']),
            'amount' => $slipEntry['amount'],
            'client' => $slipEntry['client'],
            'outline' => $slipEntry['outline'],
            'display_order' => is_null($slipEntry['display_order']) ? null : intval($slipEntry['display_order']),
            'updated_at' => is_null($slipEntry['updated_at']) ? null : strval($slipEntry['updated_at']),
            'deleted' => $slipEntry['deleted'],
        ];
    }

    /**
     * Check if the type is int or null.
     *
     * @param  mixed  $value
     * @return bool
     */
    private function isIntOrNull($value)
    {
        return is_int($value) || is_null($value);
    }

    /**
     * Check if the type is string or null.
     *
     * @param  mixed  $value
     * @return bool
     */
    private function isStringOrNull($value)
    {
        return is_string($value) || is_null($value);
    }

    /**
     * Check if the account type is 'asset', 'liability', 'expense' or 'revenue'.
     *
     * @param  mixed  $accountType
     * @return bool
     */
    private function validateAccountType($accountType)
    {
        if (is_string($accountType)) {
            if ($accountType == 'asset' || $accountType == 'liability' || $accountType == 'expense' || $accountType == 'revenue') {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Check if the type is string in Carbon::ATOM or null.
     *
     * @param  mixed  $updatedAt
     * @return bool
     */
    private function validateUpdatedAt($updatedAt)
    {
        if (is_string($updatedAt)) {
            return Carbon::canBeCreatedFromFormat($updatedAt, Carbon::ATOM);
        } else {
            return is_null($updatedAt);
        }
    }

    /**
     * Check if the type is string in Y-m-d format.
     *
     * @param  mixed  $date
     * @return bool
     */
    private function validateDateFormat($date)
    {
        $success = false;

        if (is_string($date)) {
            $parse_result = date_parse_from_format('Y-m-d', $date);
            if ($parse_result['error_count'] == 0) {
                $d = Carbon::createFromFormat('Y-m-d', $date);
                if ($d) {
                    if ($d->format('Y-m-d') == $date) {
                        $success = true;
                    }
                }
            }
        }

        return $success;
    }

    /**
     * Check if the UUID is in valid format.
     *
     * @param  mixed  $uuid
     * @return bool
     */
    public function validateUuid($uuid)
    {
        if (is_string($uuid)) {
            return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) === 1;
        } else {
            return false;
        }
    }
}
