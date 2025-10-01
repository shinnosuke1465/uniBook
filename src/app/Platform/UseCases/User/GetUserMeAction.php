<?php

declare(strict_types=1);

namespace App\Platform\UseCases\User;

use App\Exceptions\NotFoundException;
use App\Platform\Domains\Faculty\FacultyRepositoryInterface;
use App\Platform\Domains\University\UniversityRepositoryInterface;
use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\UseCases\User\Dtos\UserDto;
use AppLog;

readonly class GetUserMeAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UniversityRepositoryInterface $universityRepository,
        private FacultyRepositoryInterface $facultyRepository,
    ){
    }

    /**
     * @throws NotFoundException
     */
    public function __invoke(
        GetUserMeActionValuesInterface $actionValues,
    ): UserDto {
        AppLog::start(__METHOD__);

        $requestParams = [];

        //ログ出力
        AppLog::info(__METHOD__, [
            'request' => $requestParams,
        ]);
        try {
            //ログイン中のユーザーを取得する
            $user = $this->userRepository->getAuthenticatedUser();

            //大学名を取得
            $university = $this->universityRepository->findById($user->universityId);
            if ($university === null) {
                throw new NotFoundException('大学が見つかりません。');
            }

            //学部名を取得
            $faculty = $this->facultyRepository->findById($user->facultyId);
            if ($faculty === null) {
                throw new NotFoundException('学部が見つかりません。');
            }

            return UserDto::create($user, $university->name->value, $faculty->name->value);
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}
