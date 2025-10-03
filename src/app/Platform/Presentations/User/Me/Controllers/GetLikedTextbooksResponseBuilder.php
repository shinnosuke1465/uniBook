<?php

declare(strict_types=1);

namespace App\Platform\Presentations\User\Me\Controllers;

use App\Platform\UseCases\User\Me\Dtos\LikedTextbookDto;

readonly class GetLikedTextbooksResponseBuilder
{
    /**
     * LikedTextbookDtoの配列をAPIレスポンス形式に変換
     *
     * @param LikedTextbookDto[] $likedTextbooks
     * @return array<string, array<array<string, mixed>>>
     */
    public static function toArray(array $likedTextbooks): array
    {
        return [
            'textbooks' => array_map(function (LikedTextbookDto $dto) {
                return [
                    'id' => $dto->id,
                    'name' => $dto->name,
                    'price' => $dto->price,
                    'description' => $dto->description,
                    'image_urls' => $dto->imageUrls,
                    'university_name' => $dto->universityName,
                    'faculty_name' => $dto->facultyName,
                    'condition_type' => $dto->conditionType,
                    'deal' => $dto->deal,
                    'comments' => $dto->comments,
                    'is_liked' => $dto->isLiked,
                ];
            }, $likedTextbooks),
        ];
    }
}