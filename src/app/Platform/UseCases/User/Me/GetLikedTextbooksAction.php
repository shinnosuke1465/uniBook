<?php

declare(strict_types=1);

namespace App\Platform\UseCases\User\Me;

use App\Exceptions\DomainException;
use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\Infrastructures\QueryServices\User\GetLikedTextbooksDtoFactory;
use App\Platform\Infrastructures\QueryServices\User\GetLikedTextbooksQueryService;
use App\Platform\UseCases\User\Me\Dtos\LikedTextbookDto;
use AppLog;

readonly class GetLikedTextbooksAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private GetLikedTextbooksQueryService $queryService,
    ) {
    }

    /**
     * 認証済みユーザーがいいねした教科書一覧を取得
     *
     * @param GetLikedTextbooksActionValuesInterface $actionValues
     * @return LikedTextbookDto[]
     * @throws DomainException
     */
    public function __invoke(
        GetLikedTextbooksActionValuesInterface $actionValues
    ): array {
        AppLog::start(__METHOD__);

        try {
            // 認証済みユーザーを取得
            $authenticatedUser = $this->userRepository->getAuthenticatedUser();
            if ($authenticatedUser === null) {
                throw new DomainException('認証済みユーザー情報が取得できませんでした。');
            }

            // いいねした教科書一覧を取得
            $likes = $this->queryService->getLikedTextbooksByUser($authenticatedUser->getUserId());

            // DTOに変換して返す
            return GetLikedTextbooksDtoFactory::createFromLikes($likes);

        } finally {
            AppLog::end(__METHOD__);
        }
    }
}