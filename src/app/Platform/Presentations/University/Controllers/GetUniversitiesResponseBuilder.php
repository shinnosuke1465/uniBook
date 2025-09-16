<?php

declare(strict_types=1);

namespace App\Platform\Presentations\University\Controllers;

// UniversityDtoのuseは後で修正
use App\Platform\UseCases\University\Dtos\UniversityDto;

readonly class GetUniversitiesResponseBuilder
{
    /**
     * @param array $dtos
     * @return array<string, array<array<string, mixed>>>
     */
    public static function toArray(array $dtos): array
    {
        return [
            'universities' => collect($dtos)->map(
                fn (UniversityDto $dto) => [
                    'id' => $dto->id,
                    'name' => $dto->name,
                ]
            )->values()->all(),
        ];
    }
}

