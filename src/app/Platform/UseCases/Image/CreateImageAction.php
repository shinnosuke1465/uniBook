<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Image;

use App\Exceptions\DomainException;
use App\Platform\Domains\Image\ImageRepositoryInterface;
use App\Platform\Domains\Image\Image;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use App\Platform\UseCases\Shared\Transaction\TransactionInterface;
use AppLog;
use Exception;

readonly class CreateImageAction
{
    public function __construct(
        private TransactionInterface $transaction,
        private ImageRepositoryInterface $imageRepository,
    ) {}

    /**
     * @throws DomainException
     * @throws Exception
     */
    public function __invoke(
        CreateImageActionValuesInterface $actionValues,
    ): void {
        AppLog::start(__METHOD__);
        $requestParams = [];

        try {
            $path = $actionValues->getPath();
            $type = $actionValues->getType();

            $requestParams = [
                'path' => $path->value,
                'type' => $type->value,
            ];

            AppLog::info(__METHOD__, [
                'request' => $requestParams,
            ]);

            $image = Image::create($path, $type);

            $this->transaction->begin();
            $this->imageRepository->insert($image);
            $this->transaction->commit();
        } catch (Exception $e) {
            HandleUseCaseLogs::execMessage(__METHOD__, $e->getMessage(), $requestParams);
            $this->transaction->rollback();
            throw $e;
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}

