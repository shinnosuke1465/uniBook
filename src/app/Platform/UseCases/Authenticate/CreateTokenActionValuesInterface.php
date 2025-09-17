<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Authenticate;

use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\String\String255;

interface CreateTokenActionValuesInterface
{
    public function getEmail(): MailAddress;

    public function getUserPassword(): String255;
}

