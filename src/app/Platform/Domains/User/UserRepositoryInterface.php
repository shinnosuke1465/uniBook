<?php

namespace App\Platform\Domains\User;

use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\User\AuthenticateToken\AuthenticateToken;

interface UserRepositoryInterface
{
    public function getAuthenticatedUser(): User;

    public function createToken(): AuthenticateToken;

    public function findById(UserId $userId): ?User;

    public function findByLoginId(String255 $loginId): ?User;

    public function insertWithPassword(User $user, String255 $password): User;

    public function update(User $user): void;
}
