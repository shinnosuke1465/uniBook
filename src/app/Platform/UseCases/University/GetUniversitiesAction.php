<?php

declare(strict_types=1);

namespace App\Platform\UseCases\University;

use App\Platform\Domains\University\UniversityRepositoryInterface;

readonly class GetUniversitiesAction
{
    public function __construct(
        private UniversityRepositoryInterface $universityRepository,
    ) {
    }

    public function __invoke(
        GetUniversitiesActionValuesInterface $actionValues,
    ): array {
        return $this->universityRepository->findById($actionValues);
    }
}

