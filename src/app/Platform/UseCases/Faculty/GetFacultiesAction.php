<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Faculty;

readonly class GetFacultiesAction
{
    public function __construct(
        private FacultyRepositoryInterface $facultyRepository,
    ) {
    }

    public function __invoke(
        GetFacultiesValuesInterface $actionValues,
    ): array {
        return $this->facultyRepository->findById($actionValues);
    }
}
