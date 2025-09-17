<?php

declare(strict_types=1);

namespace App\Platform\UseCases\University;

use App\Exceptions\NotFoundException;
use App\Platform\Domains\University\UniversityId;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use AppLog;
use App\Platform\Domains\University\UniversityRepositoryInterface;
use App\Platform\UseCases\University\Dtos\UniversityDto;

readonly class GetUniversityAction
{
    public function __construct(
        private UniversityRepositoryInterface $universityRepository,
    ) {
    }

    /**
     * @throws NotFoundException
     */
    public function __invoke(
        GetUniversityActionValuesInterface $actionValues,
        string $universityIdString
    ): UniversityDto {
        AppLog::start(__METHOD__);

        $universityId = new UniversityId($universityIdString);

        $requestParams = [
            'university_id' => $universityId->value,
        ];

        try {
            //ログ出力
            AppLog::info(__METHOD__, [
                'request' => $requestParams,
            ]);

            $university = $this->universityRepository->findById($universityId);

            if ($university === null) {
                throw new NotFoundException('受給者が見つかりません。');
            }

            return UniversityDto::create($university);
        } catch (NotFoundException $e) {
            HandleUseCaseLogs::execMessage(__METHOD__, $e->getMessage(), $requestParams);
            throw $e;
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}

