<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\User\AuthenticateToken;

use App\Platform\Domains\User\AuthenticateToken\AuthenticateToken;
class AuthenticateTokenFactory
{
    public static function create(
        string $token,
    ): AuthenticateToken {
        return new AuthenticateToken(
            $token,
        );
    }
}
