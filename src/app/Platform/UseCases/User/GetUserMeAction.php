<?php

declare(strict_types=1);

namespace App\Platform\UseCases\User;

use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\UseCases\User\Dtos\UserDto;
use AppLog;

readonly class GetUserMeAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ){
    }

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

            return UserDto::create($user);
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}
