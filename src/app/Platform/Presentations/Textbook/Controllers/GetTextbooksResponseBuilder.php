<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Textbook\Controllers;

use App\Platform\UseCases\Textbook\Dtos\TextbookWithRelationsDto;

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
                fn (TextbookWithRelationsDto $dto) => [
                    'id' => $dto->id,
                    'name' => $dto->name,
                    'price' => $dto->price,
                    'description' => $dto->description,
                    'condition_type' => $dto->conditionType,
                    'university_name' => $dto->universityName,
                    'faculty_name' => $dto->facultyName,
                    'image_ids' => $dto->imageIds,
                    'deal' => $dto->deal,
                    'comments' => $dto->comments,
                    'is_liked' => $dto->isLiked,
                ]
            )->values()->all(),
        ];
    }
}