<?php

declare(strict_types=1);

namespace App\Platform\Domains\DealMessage;

use App\Platform\Domains\User\UserId;

readonly class Sender
{
    public function __construct(
        public UserId $userId,
    ) {
    }
}