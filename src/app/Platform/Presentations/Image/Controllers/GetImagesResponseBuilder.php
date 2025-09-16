<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Image\Controllers;

use App\Platform\UseCases\Image\Dtos\ImageDto;

readonly class GetImagesResponseBuilder
{
    /**
     * @param ImageDto[] $dtos
     * @return array<string, array<array<string, mixed>>>
     */
    public static function toArray(array $dtos): array
    {
        return [
            'images' => collect($dtos)->map(
                fn (ImageDto $dto) => [
                    'id' => $dto->id,
                    'path' => $dto->path,
                    'type' => $dto->type,
                ]
            )->values()->all(),
        ];
    }
}

