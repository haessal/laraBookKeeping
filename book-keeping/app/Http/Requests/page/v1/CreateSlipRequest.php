<?php

namespace App\Http\Requests\page\v1;

use App\Http\Requests\page\v1\BaseFormRequest;

class CreateSlipRequest extends BaseFormRequest
{
    /**
     * Get the 'amount' parameter from the request.
     *
     * @return int
     */
    public function amount(): int
    {
        $amount = $this->input('amount');
        if (is_int($amount)) {
            return $amount;
        }
        if (is_string($amount) && ctype_digit($amount)) {
            return (int) $amount;
        }

        return 0;
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
     * Get the 'client' parameter from the request.
     *
     * @return string
     */
    public function client(): string
    {
        return $this->get_string('client');
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
     * Get the 'outline' parameter from the request.
     *
     * @return string
     */
    public function outline(): string
    {
        return $this->get_string('outline');
    }

    /**
     * Get the 'date' parameter from the request.
     *
     * @return string
     */
    public function slip_date(): string
    {
        return $this->get_string('date');
    }

    /**
     * Get the 'modify_no' parameter from the request.
     *
     * @return string
     */
    public function slip_entry_id(): string
    {
        return $this->get_string('modify_no');
    }
}
