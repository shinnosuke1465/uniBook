<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Image\Controllers;

use App\Platform\UseCases\Image\Dtos\ImageDto;

readonly class GetImageResponseBuilder
{
    public static function toArray(ImageDto $dto): array
    {
        return [
            'id' => $dto->id,
            'path' => $dto->path,
            'type' => $dto->type,
        ];
    }
}

