<?php

declare(strict_types=1);

namespace App\Platform\Presentations\User\Controllers;

use App\Platform\Presentations\User\Requests\CreateUserRequest;
use App\Platform\Presentations\User\Requests\GetUserMeRequest;
use Illuminate\Http\Response;

readonly class UserController
{
    public function me(
        GetUserMeRequest $request,
        GetUserMeAction $action,
    ): array {
        $dto = $action($request);

        return GetUserMeResponseBuilder::toArray($dto);
    }

    public function create(
        CreateUserRequest $request,
        CreateUserAction $action,
    ): Response {
        $action($request);

        return response()->noContent();
    }
)
}
