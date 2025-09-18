<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Image\Dtos;

use App\Platform\Domains\Image\Image;
use App\Platform\Domains\Image\ImageList;

readonly class ImageDto
{
    public function __construct(
        public string $id,
        public string $path,
        public string $type,
    ) {}

    public static function create(Image $image): self
    {
        return new self(
            $image->id->value,
            $image->path->value,
            $image->type->value,
        );
    }

    /**
     * @return ImageDto[]
     */
    public static function createFromList(ImageList $imageList): array
    {
        return collect($imageList->toArray())->map(
            fn (Image $image) => new self(
                $image->id->value,
                $image->path->value,
                $image->type->value,
            )
        )->all();
    }
}

