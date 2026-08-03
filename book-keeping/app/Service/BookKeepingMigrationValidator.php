<?php

namespace App\Service;

use App\Models\CreditCardStatement;
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
     *   account_type: 'asset'|'expense'|'liability'|'revenue',
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
        /** @var string $accountGroupId */
        $accountGroupId = $accountGroup['account_group_id'];
        if (! key_exists('book_id', $accountGroup) || ! $this->validateUuid($accountGroup['book_id'])) {
            return null;
        }
        /** @var string $bookId */
        $bookId = $accountGroup['book_id'];
        if (! key_exists('account_type', $accountGroup) || ! $this->validateAccountType($accountGroup['account_type'])) {
            return null;
        }
        /** @var 'asset'|'expense'|'liability'|'revenue' $accountType */
        $accountType = $accountGroup['account_type'];
        if (! key_exists('account_group_title', $accountGroup) || ! is_string($accountGroup['account_group_title'])) {
            return null;
        }
        /** @var string $accountGroupTitle */
        $accountGroupTitle = $accountGroup['account_group_title'];
        if (! key_exists('bk_uid', $accountGroup) || ! $this->isIntOrNull($accountGroup['bk_uid'])) {
            return null;
        }
        /** @var int|null $bkUid */
        $bkUid = $accountGroup['bk_uid'];
        if (! key_exists('account_group_bk_code', $accountGroup) || ! $this->isIntOrNull($accountGroup['account_group_bk_code'])) {
            return null;
        }
        /** @var int|null $accountGroupBkCode */
        $accountGroupBkCode = $accountGroup['account_group_bk_code'];
        if (! key_exists('is_current', $accountGroup) || ! is_int($accountGroup['is_current'])) {
            return null;
        }
        /** @var bool $isCurrent */
        $isCurrent = boolval($accountGroup['is_current']);
        if (! key_exists('display_order', $accountGroup) || ! $this->isIntOrNull($accountGroup['display_order'])) {
            return null;
        }
        /** @var int|null $displayOrder */
        $displayOrder = $accountGroup['display_order'];
        if (! key_exists('updated_at', $accountGroup) || ! $this->validateUpdatedAt($accountGroup['updated_at'])) {
            return null;
        }
        /** @var string|null $updatedAt */
        $updatedAt = $accountGroup['updated_at'];
        if (! key_exists('deleted', $accountGroup) || ! is_bool($accountGroup['deleted'])) {
            return null;
        }
        /** @var bool $deleted */
        $deleted = $accountGroup['deleted'];

        return [
            'account_group_id' => $accountGroupId,
            'book_id' => $bookId,
            'account_type' => $accountType,
            'account_group_title' => $accountGroupTitle,
            'bk_uid' => $bkUid,
            'account_group_bk_code' => $accountGroupBkCode,
            'is_current' => $isCurrent,
            'display_order' => $displayOrder,
            'updated_at' => $updatedAt,
            'deleted' => $deleted,
        ];
    }

    /**
     * Validate the account item.
     *
     * @param  \App\Service\BookKeepingMigrationVersion  $version
     * @param  array<string, mixed>  $accountItem
     * @return array{
     *   account_id: string,
     *   account_group_id: string,
     *   account_title: string,
     *   description: string,
     *   selectable: bool,
     *   is_credit_card: bool|null,
     *   bk_uid: int|null,
     *   account_bk_code: int|null,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }|null
     */
    public function validateAccountItem(BookKeepingMigrationVersion $version, array $accountItem): ?array
    {
        if (! key_exists('account_id', $accountItem) || ! $this->validateUuid($accountItem['account_id'])) {
            return null;
        }
        /** @var string $accountId */
        $accountId = $accountItem['account_id'];
        if (! key_exists('account_group_id', $accountItem) || ! $this->validateUuid($accountItem['account_group_id'])) {
            return null;
        }
        /** @var string $accountGroupId */
        $accountGroupId = $accountItem['account_group_id'];
        if (! key_exists('account_title', $accountItem) || ! is_string($accountItem['account_title'])) {
            return null;
        }
        /** @var string $accountTitle */
        $accountTitle = $accountItem['account_title'];
        if (! key_exists('description', $accountItem) || ! is_string($accountItem['description'])) {
            return null;
        }
        /** @var string $description */
        $description = $accountItem['description'];
        if (! key_exists('selectable', $accountItem) || ! is_int($accountItem['selectable'])) {
            return null;
        }
        /** @var bool $selectable */
        $selectable = boolval($accountItem['selectable']);
        if ($version->isSupported(BookKeepingMigrationVersion::CREDIT_CARD_STATEMENT)) {
            if (! key_exists('is_credit_card', $accountItem) || ! is_int($accountItem['is_credit_card'])) {
                return null;
            }
            $isCreditCard = boolval($accountItem['is_credit_card']);
        } else {
            $isCreditCard = null;
        }
        if (! key_exists('bk_uid', $accountItem) || ! $this->isIntOrNull($accountItem['bk_uid'])) {
            return null;
        }
        /** @var int|null $bkUid */
        $bkUid = $accountItem['bk_uid'];
        if (! key_exists('account_bk_code', $accountItem) || ! $this->isIntOrNull($accountItem['account_bk_code'])) {
            return null;
        }
        /** @var int|null $accountBkCode */
        $accountBkCode = $accountItem['account_bk_code'];
        if (! key_exists('display_order', $accountItem) || ! $this->isIntOrNull($accountItem['display_order'])) {
            return null;
        }
        /** @var int|null $displayOrder */
        $displayOrder = $accountItem['display_order'];
        if (! key_exists('updated_at', $accountItem) || ! $this->validateUpdatedAt($accountItem['updated_at'])) {
            return null;
        }
        /** @var string|null $updatedAt */
        $updatedAt = $accountItem['updated_at'];
        if (! key_exists('deleted', $accountItem) || ! is_bool($accountItem['deleted'])) {
            return null;
        }
        /** @var bool $deleted */
        $deleted = $accountItem['deleted'];

        return [
            'account_id' => $accountId,
            'account_group_id' => $accountGroupId,
            'account_title' => $accountTitle,
            'description' => $description,
            'selectable' => $selectable,
            'is_credit_card' => $isCreditCard,
            'bk_uid' => $bkUid,
            'account_bk_code' => $accountBkCode,
            'display_order' => $displayOrder,
            'updated_at' => $updatedAt,
            'deleted' => $deleted,
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
        /** @var string $bookId */
        $bookId = $bookInformation['book_id'];
        if (! key_exists('book_name', $bookInformation) || ! is_string($bookInformation['book_name'])) {
            return null;
        }
        /** @var string $bookName */
        $bookName = $bookInformation['book_name'];
        if (! key_exists('display_order', $bookInformation) || ! $this->isIntOrNull($bookInformation['display_order'])) {
            return null;
        }
        /** @var int|null $displayOrder */
        $displayOrder = $bookInformation['display_order'];
        if (! key_exists('updated_at', $bookInformation) || ! $this->validateUpdatedAt($bookInformation['updated_at'])) {
            return null;
        }
        /** @var string|null $updatedAt */
        $updatedAt = $bookInformation['updated_at'];
        if (! key_exists('deleted', $bookInformation) || ! is_bool($bookInformation['deleted'])) {
            return null;
        }
        /** @var bool $deleted */
        $deleted = $bookInformation['deleted'];

        return [
            'book_id' => $bookId,
            'book_name' => $bookName,
            'display_order' => $displayOrder,
            'updated_at' => $updatedAt,
            'deleted' => $deleted,
        ];
    }

    /**
     * Validate the credit card statement.
     *
     * @param  array<string, mixed>  $creditCardStatement
     * @return array{
     *   credit_card_statement_id: string,
     *   book_id: string,
     *   credit_card_statement_outline: string,
     *   credit_card_statement_memo: string|null,
     *   date: string,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }|null
     */
    public function validateCreditCardStatement(array $creditCardStatement): ?array
    {
        if (! key_exists('credit_card_statement_id', $creditCardStatement) || ! $this->validateUuid($creditCardStatement['credit_card_statement_id'])) {
            return null;
        }
        /** @var string $creditCardStatementId */
        $creditCardStatementId = $creditCardStatement['credit_card_statement_id'];
        if (! key_exists('book_id', $creditCardStatement) || ! $this->validateUuid($creditCardStatement['book_id'])) {
            return null;
        }
        /** @var string $bookId */
        $bookId = $creditCardStatement['book_id'];
        if (! key_exists('credit_card_statement_outline', $creditCardStatement) || ! is_string($creditCardStatement['credit_card_statement_outline'])) {
            return null;
        }
        /** @var string $creditCardStatementOutline */
        $creditCardStatementOutline = $creditCardStatement['credit_card_statement_outline'];
        if (! key_exists('credit_card_statement_memo', $creditCardStatement) || ! $this->isStringOrNull($creditCardStatement['credit_card_statement_memo'])) {
            return null;
        }
        /** @var string|null $creditCardStatementMemo */
        $creditCardStatementMemo = $creditCardStatement['credit_card_statement_memo'];
        if (! key_exists('date', $creditCardStatement) || ! $this->validateDateFormat($creditCardStatement['date'])) {
            return null;
        }
        /** @var string $date */
        $date = $creditCardStatement['date'];
        if (! key_exists('display_order', $creditCardStatement) || ! $this->isIntOrNull($creditCardStatement['display_order'])) {
            return null;
        }
        /** @var int|null $displayOrder */
        $displayOrder = $creditCardStatement['display_order'];
        if (! key_exists('updated_at', $creditCardStatement) || ! $this->validateUpdatedAt($creditCardStatement['updated_at'])) {
            return null;
        }
        /** @var string|null $updatedAt */
        $updatedAt = $creditCardStatement['updated_at'];
        if (! key_exists('deleted', $creditCardStatement) || ! is_bool($creditCardStatement['deleted'])) {
            return null;
        }
        /** @var bool $deleted */
        $deleted = $creditCardStatement['deleted'];

        return [
            'credit_card_statement_id' => $creditCardStatementId,
            'book_id' => $bookId,
            'credit_card_statement_outline' => $creditCardStatementOutline,
            'credit_card_statement_memo' => $creditCardStatementMemo,
            'date' => $date,
            'display_order' => $displayOrder,
            'updated_at' => $updatedAt,
            'deleted' => $deleted,
        ];
    }

    /**
     * Validate the slip.
     *
     * @param  array<mixed, mixed>  $slip
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
     * @param  \App\Service\BookKeepingMigrationVersion  $version
     * @param  array<mixed, mixed>  $slipEntry
     * @return array{
     *   slip_entry_id: string,
     *   slip_id: string,
     *   debit: string,
     *   credit: string,
     *   amount: int,
     *   client: string,
     *   outline: string,
     *   credit_card_statement_id: string|null,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }|null
     */
    public function validateSlipEntry($version, array $slipEntry): ?array
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
        if ($version->isSupported(BookKeepingMigrationVersion::CREDIT_CARD_STATEMENT)) {
            if (key_exists('credit_card_statement_id', $slipEntry)) {
                if (is_null($slipEntry['credit_card_statement_id'])) {
                    $creditCardStatementId = null;
                } else {
                    if ($this->validateUuid($slipEntry['credit_card_statement_id'])) {
                        $creditCardStatementId = $slipEntry['credit_card_statement_id'];
                    } else {
                        return null;
                    }
                }
            } else {
                return null;
            }
        } else {
            $creditCardStatementId = null;
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
            'credit_card_statement_id' => is_null($creditCardStatementId) ? null : strval($creditCardStatementId),
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
