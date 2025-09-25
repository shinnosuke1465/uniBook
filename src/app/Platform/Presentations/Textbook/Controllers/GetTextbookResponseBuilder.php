<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Textbook\Controllers;

use App\Platform\UseCases\Textbook\Dtos\TextbookWithRelationsDto;

readonly class GetTextbookResponseBuilder
{
    public static function toArray(TextbookWithRelationsDto $dto): array
    {
        return [
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
        ];
    }
}