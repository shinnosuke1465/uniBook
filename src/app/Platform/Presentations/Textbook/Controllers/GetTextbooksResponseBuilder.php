<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Textbook\Controllers;

use App\Platform\UseCases\Textbook\Dtos\TextbookDto;

readonly class GetTextbooksResponseBuilder
{
    /**
     * @param array $dtos
     * @return array<string, array<array<string, mixed>>>
     */
    public static function toArray(array $dtos): array
    {
        return [
            'textbooks' => collect($dtos)->map(
                fn (TextbookDto $dto) => [
                    'id' => $dto->id,
                    'name' => $dto->name,
                    'price' => $dto->price,
                    'description' => $dto->description,
                    'condition_type' => $dto->conditionType,
                    'university_id' => $dto->universityId,
                    'faculty_id' => $dto->facultyId,
                    'image_ids' => $dto->imageIds,
                ]
            )->values()->all(),
        ];
    }
}