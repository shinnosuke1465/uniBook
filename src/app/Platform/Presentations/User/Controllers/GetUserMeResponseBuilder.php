<?php

declare(strict_types=1);

namespace App\Platform\Presentations\User\Controllers;

use App\Platform\UseCases\User\Dtos\UserDto;

readonly class GetUserMeResponseBuilder
{
    public static function toArray(UserDto $dto): array
    {
        return [
            'id' => $dto->id,
            'name' => $dto->name,
            'PostCode' => $dto->postCode,
            'address' => $dto->address,
            'email' => $dto->mailAddress,
            'imageId' => $dto->imageId,
            'facultyId' => $dto->facultyId,
            'universityId' => $dto->universityId,
        ];
    }
}
