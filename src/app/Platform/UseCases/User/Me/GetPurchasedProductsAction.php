<?php

declare(strict_types=1);

namespace App\Platform\UseCases\User\Me;

use App\Exceptions\DomainException;
use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\Infrastructures\QueryServices\Textbook\GetPurchasedTextbooksDtoFactory;
use App\Platform\Infrastructures\QueryServices\Textbook\GetPurchasedTextbooksQueryService;
use App\Platform\UseCases\User\Me\Dtos\PurchasedProductDto;
use AppLog;

readonly class GetPurchasedProductsAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private GetPurchasedTextbooksQueryService $queryService,
    ) {
    }

    /**
     * 認証済みユーザーの購入教科書一覧を取得
     *
     * @param GetPurchasedProductsActionValuesInterface $actionValues
     * @return PurchasedProductDto[]
     * @throws DomainException
     */
    public function __invoke(
        GetPurchasedProductsActionValuesInterface $actionValues
    ): array{
        AppLog::start(__METHOD__);

        try {
            // 認証済みユーザーを取得
            $authenticatedUser = $this->userRepository->getAuthenticatedUser();
            if ($authenticatedUser === null) {
                throw new DomainException('認証済みユーザー情報が取得できませんでした。');
            }

            // 購入教科書一覧を取得
            $deals = $this->queryService->getPurchasedTextbooksByUser($authenticatedUser->getUserId());

            // DTOに変換して返す
            return GetPurchasedTextbooksDtoFactory::createFromDeals($deals);

        } finally {
            AppLog::end(__METHOD__);
        }
    }
}
