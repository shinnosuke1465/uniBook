<?php

declare(strict_types=1);

namespace App\Platform\Domains\Deal;

use App\Platform\Domains\User\UserId;

readonly class Seller
{
    public function __construct(
        public UserId $userId,
    ) {
    }
}