<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Faculty;

use App\Platform\Domains\Faculty\FacultyRepositoryInterface;

readonly class CreateFacultyAction
{
    public function __construct(
        private FacultyRepositoryInterface $facultyRepository,
    ) {
    }

    public function __invoke(
        CreateFacultyValuesInterface $actionValues,
    ): void {
        $this->facultyRepository->create($actionValues);
    }
}
