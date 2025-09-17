<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Authenticate\Dtos;

use App\Platform\Domains\User\AuthenticateToken\AuthenticateToken;

readonly class AuthenticateTokenDto
{
    /**
     * @param string $token
     */
    public function __construct(
        public string $token,
    ){
    }

    public static function create(
        AuthenticateToken $authenticateToken
    ): self {
        return new self($authenticateToken->token);
    }
}
