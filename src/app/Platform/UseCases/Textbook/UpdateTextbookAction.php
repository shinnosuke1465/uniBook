<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Textbook;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use App\Platform\UseCases\Shared\Transaction\TransactionInterface;
use AppLog;
use App\Platform\Domains\Textbook\TextbookRepositoryInterface;
use Exception;

readonly class UpdateTextbookAction
{
    public function __construct(
        private TransactionInterface $transaction,
        private TextbookRepositoryInterface $textbookRepository,
    ) {
    }

    /**
     * @throws NotFoundException
     * @throws DomainException
     * @throws Exception
     */
    public function __invoke(
        UpdateTextbookActionValuesInterface $actionValues,
        string $textbookIdString
    ): void {
        AppLog::start(__METHOD__);

        $textbookId = new TextbookId($textbookIdString);
        $requestParams = [];

        try {
            $name = $actionValues->getName();
            $price = $actionValues->getPrice();
            $description = $actionValues->getDescription();
            $imageIdList = $actionValues->getImageIdList();
            $conditionType = $actionValues->getConditionType();

            $requestParams = [
                'textbook_id' => $textbookId->value,
                'name' => $name->value,
                'price' => $price->value,
                'description' => $description->value,
                'image_ids' => $imageIdList->toArray(),
                'condition_type' => $conditionType->value,
            ];

            AppLog::info(__METHOD__, [
                'request' => $requestParams,
            ]);

            $textbook = $this->textbookRepository->findById($textbookId);

            if ($textbook === null) {
                throw new NotFoundException('教科書が見つかりません。');
            }

            $updatedTextbook = $textbook->update(
                $name,
                $price,
                $description,
                $imageIdList,
                $conditionType,
            );

            $this->transaction->begin();
            $this->textbookRepository->update($updatedTextbook);
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
