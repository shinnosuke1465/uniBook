<?php

declare(strict_types=1);

namespace App\Platform\Domains\User\AuthenticateToken;

readonly class AuthenticateToken
{
    public function __construct(
        public string $token,
    ) {
    }
}
