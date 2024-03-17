<?php

namespace App\Http\Responder\page\v2;

use App\Http\Responder\AccountsListConverter;

class BaseAccountsViewResponder extends BaseViewResponder
{
    use AccountsListConverter;
}
