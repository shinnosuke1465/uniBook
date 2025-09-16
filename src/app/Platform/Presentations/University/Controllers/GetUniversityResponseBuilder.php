<?php

declare(strict_types=1);

namespace App\Platform\Presentations\University\Controllers;

// UniversityDtoのuseは後で修正
use App\Platform\UseCases\University\Dtos\UniversityDto;

readonly class GetUniversityResponseBuilder
{
    public static function toArray(UniversityDto $dto): array
    {
        return [
            'id' => $dto->id,
            'name' => $dto->name,
        ];
    }
}

