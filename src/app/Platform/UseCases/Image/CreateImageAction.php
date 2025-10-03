<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Image;

use App\Exceptions\DomainException;
use App\Platform\Domains\Image\ImageRepositoryInterface;
use App\Platform\Domains\Image\ImageStorageServiceInterface;
use App\Platform\Domains\Image\Image;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\UseCases\Image\Dtos\ImageDto;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use App\Platform\UseCases\Shared\Transaction\TransactionInterface;
use AppLog;
use Exception;

readonly class CreateImageAction
{
    public function __construct(
        private TransactionInterface $transaction,
        private ImageRepositoryInterface $imageRepository,
        private ImageStorageServiceInterface $imageStorageService,
    ) {}

    /**
     * @throws DomainException
     * @throws Exception
     */
    public function __invoke(
        CreateImageActionValuesInterface $actionValues,
    ): ImageDto {
        AppLog::start(__METHOD__);
        $requestParams = [];

        try {
            // アップロードされたファイルを取得
            $imageFile = $actionValues->getImageFile();

            $requestParams = [
                'original_name' => $imageFile->getClientOriginalName(),
                'mime_type' => $imageFile->getClientMimeType(),
            ];

            AppLog::info(__METHOD__, [
                'request' => $requestParams,
            ]);

            // 環境に応じたストレージに保存（Local: 相対パス, S3: フルURL）
            $storedPath = $this->imageStorageService->store($imageFile);

            // DBに保存するための値オブジェクトを作成
            $path = new String255($storedPath);
            $type = new String255($imageFile->getClientMimeType());

            $image = Image::create($path, $type);

            $this->transaction->begin();
            $this->imageRepository->insert($image);
            $this->transaction->commit();

            return ImageDto::create($image);
        } catch (Exception $e) {
            HandleUseCaseLogs::execMessage(__METHOD__, $e->getMessage(), $requestParams);
            $this->transaction->rollback();
            throw $e;
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}

