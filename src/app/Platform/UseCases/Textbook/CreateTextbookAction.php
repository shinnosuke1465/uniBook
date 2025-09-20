<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Textbook;

use App\Exceptions\DomainException;
use App\Platform\Domains\Textbook\TextbookRepositoryInterface;
use App\Platform\Domains\Textbook\Textbook;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use App\Platform\UseCases\Shared\Transaction\TransactionInterface;
use AppLog;
use Exception;

readonly class CreateTextbookAction
{
    public function __construct(
        private TransactionInterface $transaction,
        private TextbookRepositoryInterface $textbookRepository,
    ) {
    }

    /**
     * @throws DomainException
     * @throws Exception
     */
    public function __invoke(
        CreateTextbookActionValuesInterface $actionValues,
    ): void {
        AppLog::start(__METHOD__);
        $requestParams = [];

        try {
            $name = $actionValues->getName();
            $price = $actionValues->getPrice();
            $description = $actionValues->getDescription();
            $imageIds = $actionValues->getImageIdList();
            $universityId = $actionValues->getUniversityId();
            $facultyId = $actionValues->getFacultyId();
            $conditionType = $actionValues->getConditionType();

            $requestParams = [
                'name' => $name->value,
                'price' => $price->value,
                'description' => $description->value,
                'image_ids' => $imageIds->toArray(),
                'university_id' => $universityId->value,
                'faculty_id' => $facultyId->value,
                'condition_type' => $conditionType->value,
            ];

            AppLog::info(__METHOD__, [
                'request' => $requestParams,
            ]);

            $textbook = Textbook::create(
                $name,
                $price,
                $description,
                $imageIds,
                $universityId,
                $facultyId,
                $conditionType,
            );

            $this->transaction->begin();
            $this->textbookRepository->insert(
                $textbook
            );
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
