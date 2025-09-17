<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Image;

use App\Exceptions\NotFoundException;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use AppLog;
use App\Platform\Domains\Image\ImageRepositoryInterface;
use App\Platform\UseCases\Image\Dtos\ImageDto;

readonly class GetImageAction
{
    public function __construct(
        private ImageRepositoryInterface $imageRepository,
    ) {}

    /**
     * @throws NotFoundException
     */
    public function __invoke(
        GetImageActionValuesInterface $actionValues,
    ): ImageDto {
        AppLog::start(__METHOD__);

        $imageId = $actionValues->getImageId();

        $requestParams = [
            'image_id' => $imageId->value,
        ];

        try {
            AppLog::info(__METHOD__, [
                'request' => $requestParams,
            ]);

            $image = $this->imageRepository->findById($imageId);

            if ($image === null) {
                throw new NotFoundException('画像が見つかりません。');
            }

            return ImageDto::create($image);
        } catch (NotFoundException $e) {
            HandleUseCaseLogs::execMessage(__METHOD__, $e->getMessage(), $requestParams);
            throw $e;
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}

