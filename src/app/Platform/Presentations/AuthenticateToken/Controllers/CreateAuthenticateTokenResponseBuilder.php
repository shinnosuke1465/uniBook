<?php

declare(strict_types=1);

namespace App\Platform\Presentations\AuthenticateToken\Controllers;

use App\Platform\UseCases\Authenticate\Dtos\AuthenticateTokenDto;

readonly class CreateAuthenticateTokenResponseBuilder
{
    public static function toArray(
        AuthenticateTokenDto $authenticateTokenDto,
    ): array {
        return [
            'token' => $authenticateTokenDto->token,
        ];
    }
}
