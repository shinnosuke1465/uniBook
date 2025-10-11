<?php

declare(strict_types=1);

namespace App\Platform\UseCases\University;

use App\Exceptions\DomainException;
use App\Platform\Domains\University\UniversityRepositoryInterface;
use App\Platform\Domains\University\University;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use App\Platform\UseCases\Shared\Transaction\TransactionInterface;
use AppLog;
use Exception;

readonly class CreateUniversityAction
{
    public function __construct(
        private TransactionInterface $transaction,
        private UniversityRepositoryInterface $universityRepository,
    ) {
    }

    /**
     * @throws DomainException
     * @throws Exception
     */
    public function __invoke(
        CreateUniversityActionValuesInterface $actionValues,
    ): void {
        AppLog::start(__METHOD__);
        $requestParams = [];

        try {
            $name = $actionValues->getName();

            $requestParams = [
                'name' => $name->value,
            ];

            //ログ出力
            AppLog::info(__METHOD__, [
                'request' => $requestParams,
            ]);

            $university = University::create(
                $name,
            );

            $this->transaction->begin();
            $this->universityRepository->insert($university);
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

