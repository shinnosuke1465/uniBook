<?php

declare(strict_types=1);

namespace App\Platform\UseCases\User\Dtos;

use App\Platform\Domains\User\User;

readonly class UserDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $postCode,
        public string $address,
        public string $mailAddress,
        public ?int $imageId,
        public string $facultyId,
        public string $universityId,
    ) {
    }

    /**
     * Userドメインを配列化（パスワードは入れない）
     */
    public function create(User $user): self
    {
        return new self(
            $user->id->value,
            $user->name->name,
            $user->postCode->postCode->value,
            $user->address->address->value,
            $user->mailAddress->mailAddress->value,
            $user->imageId?->value,
            $user->facultyId->value,
            $user->universityId->value,
        );
    }
}
