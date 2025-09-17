<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Faculty;

use App\Exceptions\DomainException;
use App\Platform\Domains\Faculty\FacultyRepositoryInterface;
use App\Platform\Domains\Faculty\Faculty;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use App\Platform\UseCases\Shared\Transaction\TransactionInterface;
use AppLog;
use Exception;

readonly class CreateFacultyAction
{
    public function __construct(
        private TransactionInterface $transaction,
        private FacultyRepositoryInterface $facultyRepository,
    ) {
    }

    /**
     * @throws DomainException
     * @throws Exception
     */
    public function __invoke(
        CreateFacultyActionValuesInterface $actionValues,
    ): void {
        AppLog::start(__METHOD__);
        $requestParams = [];

        try {
            $name = $actionValues->getName();
            $universityId = $actionValues->getUniversityId();

            $requestParams = [
                'name' => $name->value,
                'university_id' => $universityId->value,
            ];

            AppLog::info(__METHOD__, [
                'request' => $requestParams,
            ]);

            $faculty = Faculty::create(
                $name,
                $universityId
            );

            $this->transaction->begin();
            $this->facultyRepository->insert(
                $faculty
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
