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

readonly class DeleteTextbookAction
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
        DeleteTextbookActionValuesInterface $actionValues,
        string $textbookIdString
    ): void {
        AppLog::start(__METHOD__);

        $textbookId = new TextbookId($textbookIdString);

        $requestParams = [
            'textbook_id' => $textbookId->value,
        ];

        try {
            AppLog::info(__METHOD__, [
                'request' => $requestParams,
            ]);

            $textbook = $this->textbookRepository->findById($textbookId);

            if ($textbook === null) {
                throw new NotFoundException('教科書が見つかりません。');
            }

            $this->transaction->begin();
            $this->textbookRepository->delete($textbookId);
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