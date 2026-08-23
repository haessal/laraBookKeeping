<?php

namespace App\Http\Requests\page\v1;

use App\Http\Requests\page\v1\BaseFormRequest;

class ShowStatementsRequest extends BaseFormRequest
{
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
     * Get the 'END' parameter from the request.
     *
     * @return string
     */
    public function end_date(): string
    {
        return $this->get_string('END');
    }
}
