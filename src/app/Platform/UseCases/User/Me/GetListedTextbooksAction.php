<?php

declare(strict_types=1);

namespace App\Platform\UseCases\User\Me;

use App\Exceptions\DomainException;
use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\Infrastructures\QueryServices\Textbook\GetListedTextbooksDtoFactory;
use App\Platform\Infrastructures\QueryServices\Textbook\GetListedTextbooksQueryService;
use App\Platform\UseCases\User\Me\Dtos\ListedTextbookDto;
use AppLog;

readonly class GetListedTextbooksAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private GetListedTextbooksQueryService $queryService,
    ) {
    }

    /**
     * 認証済みユーザーの出品教科書一覧を取得
     *
     * @param GetListedTextbooksActionValuesInterface $actionValues
     * @return ListedTextbookDto[]
     * @throws DomainException
     */
    public function __invoke(
        GetListedTextbooksActionValuesInterface $actionValues
    ): array {
        AppLog::start(__METHOD__);

        try {
            // 認証済みユーザーを取得
            $authenticatedUser = $this->userRepository->getAuthenticatedUser();
            if ($authenticatedUser === null) {
                throw new DomainException('認証済みユーザー情報が取得できませんでした。');
            }

            // 出品教科書一覧を取得
            $deals = $this->queryService->getListedTextbooksByUser($authenticatedUser->getUserId());

            // DTOに変換して返す
            return GetListedTextbooksDtoFactory::createFromDeals($deals);

        } finally {
            AppLog::end(__METHOD__);
        }
    }
}
