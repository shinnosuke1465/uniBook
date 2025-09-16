<?php

namespace App\Platform\UseCases\Authenticate;

use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\String\String255;

interface LoginActionValuesInterface
{
    public function getMailAddress(): MailAddress;

    public function getLoginPassword(): String255;
}
