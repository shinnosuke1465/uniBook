<?php

declare(strict_types=1);

namespace App\Platform\UseCases\User\Me;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\Infrastructures\QueryServices\Textbook\GetPurchasedTextbookDealDtoFactory;
use App\Platform\Infrastructures\QueryServices\Textbook\GetPurchasedTextbookDealQueryService;
use App\Platform\UseCases\User\Me\Dtos\PurchasedProductDto;
use AppLog;

readonly class QueryPurchasedTextbookDealAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private GetPurchasedTextbookDealQueryService $queryService,
    ) {
    }

    /**
     * 認証済みユーザーの購入教科書の取引詳細を取得
     *
     * @param QueryPurchasedTextbookDealActionValues $actionValues
     * @return PurchasedProductDto
     * @throws DomainException
     * @throws NotFoundException
     */
    public function __invoke(
        QueryPurchasedTextbookDealActionValues $actionValues,
        string $textbookIdString
    ): PurchasedProductDto {
        AppLog::start(__METHOD__);

        try {
            $textbookId = new textbookId($textbookIdString);

            // 認証済みユーザーを取得
            $authenticatedUser = $this->userRepository->getAuthenticatedUser();

            if ($authenticatedUser === null) {
                throw new DomainException('認証済みユーザー情報が取得できませんでした。');
            }

            // 購入教科書の取引詳細を取得
            $deal = $this->queryService->getListedProductDeal(
                $textbookId->value,
                $authenticatedUser->getUserId()
            );

            if ($deal === null) {
                throw new NotFoundException('指定された購入商品が見つかりません。');
            }

            return GetPurchasedTextbookDealDtoFactory::createFromDeal($deal);

        } finally {
            AppLog::end(__METHOD__);
        }
    }
}
