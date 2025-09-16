<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Image;

use App\Platform\Domains\Image\ImageRepositoryInterface;

readonly class GetImagesAction
{
    public function __construct(
        private ImageRepositoryInterface $imageRepository,
    ) {}

    public function __invoke(
        GetImagesActionValuesInterface $actionValues,
    ): array {
        return $this->imageRepository->findByIds($actionValues->getImageIdList());
    }
}

