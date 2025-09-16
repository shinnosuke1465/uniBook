<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Authenticate\Controllers;

use App\Exceptions\IllegalUserException;
use App\Exceptions\InvalidValueException;
use App\Platform\Presentations\Authenticate\Requests\LoginRequest;
use App\Platform\Presentations\Authenticate\Requests\LogoutRequest;
use App\Platform\UseCases\Authenticate\LoginAction;
use App\Platform\UseCases\Authenticate\LogoutAction;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Auth\AuthenticationException;
use Throwable;

readonly class AuthenticateController
{
    /**
     * @throws InvalidValueException
     * @throws AuthenticationException
     * @throws Throwable
     */
    public function login(
        LoginRequest $request,
        LoginAction $action,
    ): HttpResponse {
        $action($request);

        return Response::noContent();
    }

    /**
     * @throws InvalidValueException
     * @throws IllegalUserException
     * @throws Throwable
     */
    public function logout(
        LogoutRequest $request,
        LogoutAction $action,
    ): HttpResponse {
        $action($request);

        return Response::noContent();
    }
}
