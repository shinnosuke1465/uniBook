<?php

declare(strict_types=1);

namespace App\Platform\UseCases\University;

use AppLog;
use App\Platform\Domains\University\UniversityRepositoryInterface;
use App\Platform\UseCases\Shared\Transaction\TransactionInterface;
use App\Platform\UseCases\University\Dtos\UniversityDto;

readonly class GetUniversitiesAction
{
    public function __construct(
        private UniversityRepositoryInterface $universityRepository,
    ) {
    }

    /**
     * @return UniversityDto[]
     */
    public function __invoke(
        GetUniversitiesActionValuesInterface $actionValues,
    ): array {
        AppLog::start(__METHOD__);

        try {
            $universities = $this->universityRepository->findAll();

            return collect($universities)->map(
                fn($university) => UniversityDto::create($university)
            )->all();
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}

