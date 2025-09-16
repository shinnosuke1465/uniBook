<?php

declare(strict_types=1);

namespace App\Platform\UseCases\User;

use App\Platform\Domains\User\UserRepositoryInterface;

readonly class CreateUserAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ){
    }

    public function __invoke(
        CreateUserValuesInterface $actionValues,
    ): void
    {
        $user = $this->userRepository->findById($actionValues->getUserId());
    }
}
