<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\User;

use App\Exceptions\DomainException;
use App\Platform\Domains\User\AuthenticateToken\AuthenticateToken;
use App\Platform\Domains\User\UserId;
use App\Platform\Domains\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Platform\Domains\User\User;
use App\Platform\Domains\Shared\String\String255;

readonly class UserRepository implements UserRepositoryInterface
{
    /**
     * @throws DomainException
     */
    public function getAuthenticatedUser(): User
    {
        $user = Auth::user();

        return UserFactory::create($user);
    }

    public function createToken(): AuthenticateToken
    {

    }

    public function findById(UserId $userId): ?User
    {

    }

    public function findByLoginId(String255 $loginId): ?User
    {

    }

    public function insertWithPassword(User $user, String255 $password): User
    {

    }

    public function update(User $user): void
    {

    }
}
