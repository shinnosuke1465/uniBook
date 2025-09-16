<?php

declare(strict_types=1);

namespace App\Platform\UseCases\University;

use App\Platform\Domains\University\UniversityRepositoryInterface;

readonly class CreateUniversityAction
{
    public function __construct(
        private UniversityRepositoryInterface $universityRepository,
    ) {
    }

    public function __invoke(
        CreateUniversityActionValuesInterface $actionValues,
    ): void {
        $this->universityRepository->create($actionValues);
    }
}

