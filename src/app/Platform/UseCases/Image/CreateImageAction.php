<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Image;

use App\Platform\Domains\Image\ImageRepositoryInterface;

readonly class CreateImageAction
{
    public function __construct(
        private ImageRepositoryInterface $imageRepository,
    ) {}

    public function __invoke(
        CreateImageActionValuesInterface $actionValues,
    ): void {
        $this->imageRepository->create($actionValues);
    }
}

