<?php

namespace App\Http\Requests\page\v1;

use App\Http\Requests\page\v1\BaseFormRequest;

class FindSlipsRequest extends BaseFormRequest
{
    /**
     * Get the 'and_or' parameter from the request.
     *
     * @return string
     */
    public function and_or(): string
    {
        return $this->get_string('and_or');
    }

    /**
     * Get the 'BEGINNING' parameter from the request.
     *
     * @return string
     */
    public function beginning_date(): string
    {
        return $this->get_string('BEGINNING');
    }

    /**
     * Get button action from the request.
     *
     * @return string
     */
    public function button_action(): string
    {
        if (is_array($this->input('buttons'))) {
            $action = strval(key($this->input('buttons')));
        } else {
            $action = '';
        }

        return $action;
    }

    /**
     * Get the 'credit' parameter from the request.
     *
     * @return string
     */
    public function credit(): string
    {
        return $this->get_string('credit');
    }

    /**
     * Get the 'debit' parameter from the request.
     *
     * @return string
     */
    public function debit(): string
    {
        return $this->get_string('debit');
    }

    /**
     * Get the 'END' parameter from the request.
     *
     * @return string
     */
    public function end_date(): string
    {
        return $this->get_string('END');
    }

    /**
     * Get the 'KEYWORD' parameter from the request.
     *
     * @return string
     */
    public function keyword(): string
    {
        return $this->get_string('KEYWORD');
    }

    /**
     * Get the 'modify_no_list' parameter from the request.
     *
     * @return array<int, string>
     */
    public function slip_entry_ids(): array
    {
        $slipEntryIds = [];
        $list = $this->input('modify_no_list', []);
        if (is_array($list)) {
            foreach ($list as $index => $uuid) {
                if (is_string($uuid)) {
                    $slipEntryIds[] = $uuid;
                }
            }
        }

        return $slipEntryIds;
    }
}
