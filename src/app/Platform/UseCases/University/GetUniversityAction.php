<?php

declare(strict_types=1);

namespace App\Platform\UseCases\University;

use App\Platform\Domains\University\UniversityRepositoryInterface;

readonly class GetUniversityAction
{
    public function __construct(
        private UniversityRepositoryInterface $universityRepository,
    ) {
    }

    public function __invoke(
        GetUniversityActionValuesInterface $actionValues,
    ): array {
        return $this->universityRepository->findById($actionValues);
    }
}

