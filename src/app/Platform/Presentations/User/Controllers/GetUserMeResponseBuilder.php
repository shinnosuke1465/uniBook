<?php

declare(strict_types=1);

namespace App\Platform\Presentations\User\Controllers;

use App\Platform\UseCases\User\Dtos\UserDto;

readonly class GetUserMeResponseBuilder
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(UserDto $dto): array
    {
        return [
            'id' => $dto->id,
            'name' => $dto->name,
            'mail_address' => $dto->mailAddress,
            'post_code' => $dto->postCode,
            'address' => $dto->address,
            'image_id' => $dto->imageId,
            'university_id' => $dto->universityId,
            'faculty_id' => $dto->facultyId,
        ];
    }
}
