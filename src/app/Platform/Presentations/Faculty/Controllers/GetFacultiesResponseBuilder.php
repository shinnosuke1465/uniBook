<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Faculty\Controllers;

use App\Platform\UseCases\Faculty\Dtos\FacultyDto;

readonly class GetFacultiesResponseBuilder
{
    /**
     * @param FacultyDto[] $dtos
     * @return array<string, array<array<string, mixed>>>
     */
    public static function toArray(array $dtos): array
    {
        return [
            'faculties' => collect($dtos)->map(
                fn (FacultyDto $dto) => [
                    'id' => $dto->id,
                    'name' => $dto->name,
                    'universityId' => $dto->universityId,
                ]
            )->values()->all(),
        ];
    }
}
