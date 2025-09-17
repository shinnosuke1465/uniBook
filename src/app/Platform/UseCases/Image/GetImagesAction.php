<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Image;

use AppLog;
use App\Platform\Domains\Image\ImageRepositoryInterface;
use App\Platform\UseCases\Image\Dtos\ImageDto;

readonly class GetImagesAction
{
    public function __construct(
        private ImageRepositoryInterface $imageRepository,
    ) {}

    /**
     * @return ImageDto[]
     */
    public function __invoke(
        GetImagesActionValuesInterface $actionValues,
    ): array {
        AppLog::start(__METHOD__);

        $imageIdList = $actionValues->getImageIdList();

        $requestParams = [
            'image_id_list' => $imageIdList->toArray(),
        ];

        try {
            AppLog::info(__METHOD__, [
                'request' => $requestParams,
            ]);

            $images = $this->imageRepository->findByIds($imageIdList);

            return collect($images)->map(
                fn($image) => ImageDto::create($image)
            )->all();
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}

