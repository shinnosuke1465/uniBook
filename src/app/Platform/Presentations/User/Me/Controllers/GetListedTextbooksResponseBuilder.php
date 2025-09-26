<?php

declare(strict_types=1);

namespace App\Platform\Presentations\User\Me\Controllers;

use App\Platform\UseCases\User\Me\Dtos\ListedTextbookDto;

readonly class GetListedTextbooksResponseBuilder
{
    /**
     * ListedTextbookDtoの配列をAPIレスポンス形式に変換
     *
     * @param ListedTextbookDto[] $listedTextbooks
     * @return array<string, array<array<string, mixed>>>
     */
    public static function toArray(array $listedTextbooks): array
    {
        return [
            'products' => array_map(function (ListedTextbookDto $dto) {
                return [
                    'id' => $dto->id,
                    'name' => $dto->name,
                    'description' => $dto->description,
                    'image_url' => $dto->imageUrl,
                    'image_urls' => $dto->imageUrls,
                    'price' => $dto->price,
                    'deal' => $dto->deal,
                ];
            }, $listedTextbooks),
        ];
    }
}