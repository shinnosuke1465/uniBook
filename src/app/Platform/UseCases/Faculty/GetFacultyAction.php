<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Faculty;

use App\Platform\Domains\Faculty\FacultyRepositoryInterface;

readonly class GetFacultyAction
{
    public function __construct(
        private FacultyRepositoryInterface $facultyRepository,
    ) {
    }

    public function __invoke(
        GetFacultyActionValuesInterface $actionValues,
    ): array {
        return $this->facultyRepository->findById($actionValues);
    }
}
