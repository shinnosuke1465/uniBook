<?php

declare(strict_types=1);

namespace App\Platform\Presentations\AuthenticateToken\Controllers;

use App\Platform\Presentations\AuthenticateToken\Requests\CreateTokenRequest;
use App\Platform\UseCases\Authenticate\CreateTokenAction;
use Illuminate\Auth\AuthenticationException;

readonly class AuthenticateTokenController
{
    /**
     * @throws AuthenticationException
     */
    public function create(
        CreateTokenRequest $request,
        CreateTokenAction $action,
    ): array {
        return CreateAuthenticateTokenResponseBuilder::toArray(
            $action($request)
        );
    }
}
