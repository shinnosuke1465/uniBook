<?php

declare(strict_types=1);

namespace App\Platform\Domains\Deal;

use App\Platform\Domains\User\UserId;

readonly class Buyer
{
    public function __construct(
        public UserId $userId,
    ) {
    }
}