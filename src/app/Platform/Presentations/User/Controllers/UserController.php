<?php

declare(strict_types=1);

namespace App\Platform\Presentations\User\Controllers;

use App\Platform\Presentations\AuthenticateToken\Controllers\CreateAuthenticateTokenResponseBuilder;
use App\Platform\Presentations\User\Requests\CreateUserRequest;
use App\Platform\Presentations\User\Requests\GetUserMeRequest;
use App\Platform\UseCases\User\CreateUserAction;
use App\Platform\UseCases\User\GetUserMeAction;
use Illuminate\Http\Response;
use Exception;
use Throwable;

readonly class UserController
{
    /**
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function me(
        GetUserMeRequest $request,
        GetUserMeAction $action,
    ): array {
        $dto = $action($request);

        return GetUserMeResponseBuilder::toArray($dto);
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function create(
        CreateUserRequest $request,
        CreateUserAction $action,
    ): array {
        return CreateAuthenticateTokenResponseBuilder::toArray(
            $action($request)
        );
    }
}
