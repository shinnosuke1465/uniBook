<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Image\Dtos;

readonly class ImageDto
{
    public function __construct(
        public string $id,
        public string $path,
        public string $type,
    ) {}
}

