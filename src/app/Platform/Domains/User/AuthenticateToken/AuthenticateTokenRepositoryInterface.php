<?php

namespace App\Platform\Domains\User\AuthenticateToken;

interface AuthenticateTokenRepositoryInterface
{
    public function createToken(): AuthenticateToken;
}
