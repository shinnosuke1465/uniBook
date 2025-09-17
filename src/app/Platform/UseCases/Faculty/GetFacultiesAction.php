<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Faculty;

use AppLog;
use App\Platform\Domains\Faculty\FacultyRepositoryInterface;
use App\Platform\UseCases\Faculty\Dtos\FacultyDto;

readonly class GetFacultiesAction
{
    public function __construct(
        private FacultyRepositoryInterface $facultyRepository,
    ) {
    }

    /**
     * @return FacultyDto[]
     */
    public function __invoke(
        GetFacultiesActionValuesInterface $actionValues,
    ): array {
        AppLog::start(__METHOD__);

        $universityId = $actionValues->getUniversityId();

        $requestParams = [
            'university_id' => $universityId,
        ];

        try {
            //ログ出力
            AppLog::info(__METHOD__, [
                'request' => $requestParams,
            ]);

            $faculties = $this->facultyRepository->findByUniversityId($universityId);

            return collect($faculties)->map(
                fn($faculty) => FacultyDto::create($faculty)
            )->all();
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}
