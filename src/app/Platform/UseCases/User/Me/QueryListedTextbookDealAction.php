<?php

declare(strict_types=1);

namespace App\Platform\UseCases\User\Me;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\Infrastructures\QueryServices\Textbook\GetListedTextbookDealDtoFactory;
use App\Platform\Infrastructures\QueryServices\Textbook\GetListedTextbookDealQueryService;
use App\Platform\UseCases\User\Me\Dtos\ListedTextbookDto;
use AppLog;

readonly class QueryListedTextbookDealAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private GetListedTextbookDealQueryService $queryService,
    ) {
    }

    /**
     * 認証済みユーザーの出品教科書の取引詳細を取得
     *
     * @param QueryListedTextbookDealActionValues $actionValues
     * @return ListedTextbookDto
     * @throws DomainException
     * @throws NotFoundException
     */
    public function __invoke(
        QueryListedTextbookDealActionValues $actionValues,
        string $textbookIdString
    ): ListedTextbookDto {
        AppLog::start(__METHOD__);

        try {
            $textbookId = new textbookId($textbookIdString);

            // 認証済みユーザーを取得
            $authenticatedUser = $this->userRepository->getAuthenticatedUser();

            if ($authenticatedUser === null) {
                throw new DomainException('認証済みユーザー情報が取得できませんでした。');
            }

            // 出品教科書の取引詳細を取得
            $deal = $this->queryService->getListedProductDeal(
                $textbookId->value,
                $authenticatedUser->getUserId()
            );

            if ($deal === null) {
                throw new NotFoundException('指定された出品商品が見つかりません。');
            }

            return GetListedTextbookDealDtoFactory::createFromDeal($deal);

        } finally {
            AppLog::end(__METHOD__);
        }
    }
}