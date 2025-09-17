<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Faculty;

use App\Exceptions\NotFoundException;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use AppLog;
use App\Platform\Domains\Faculty\FacultyRepositoryInterface;
use App\Platform\UseCases\Faculty\Dtos\FacultyDto;

readonly class GetFacultyAction
{
    public function __construct(
        private FacultyRepositoryInterface $facultyRepository,
    ) {
    }

    /**
     * @throws NotFoundException
     */
    public function __invoke(
        GetFacultyActionValuesInterface $actionValues,
        string $facultyIdString,
    ): FacultyDto {
        AppLog::start(__METHOD__);

        $facultyId = new FacultyId($facultyIdString);

        $requestParams = [
            'faculty_id' => $facultyId->value,
        ];

        try {
            AppLog::info(__METHOD__, [
                'request' => $requestParams,
            ]);

            $faculty = $this->facultyRepository->findById($facultyId);

            if ($faculty === null) {
                throw new NotFoundException('学部が見つかりません。');
            }

            return FacultyDto::create($faculty);
        } catch (NotFoundException $e) {
            HandleUseCaseLogs::execMessage(__METHOD__, $e->getMessage(), $requestParams);
            throw $e;
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}
