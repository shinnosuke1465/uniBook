<?php

declare(strict_types=1);

namespace App\Platform\UseCases\TextbookDeal;

use App\Platform\Domains\Deal\DealRepositoryInterface;
use App\Platform\Domains\Deal\DealStatus;
use App\Platform\Domains\Deal\DealDomainService;
use App\Platform\Domains\DealEvent\DealEventRepositoryInterface;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use App\Platform\UseCases\Shared\Transaction\TransactionInterface;
use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use AppLog;
use Illuminate\Auth\Access\AuthorizationException;

readonly class CancelAction
{
    public function __construct(
        private TransactionInterface $transaction,
        private DealRepositoryInterface $dealRepository,
        private DealEventRepositoryInterface $dealEventRepository,
        private DealDomainService $dealDomainService,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @throws DomainException
     * @throws NotFoundException
     */
    public function __invoke(
        CancelActionValuesInterface $values,
        string $textbookId,
    ): void
    {
        AppLog::start(__METHOD__);
        $textbookId = new TextbookId($textbookId);

        $requestParams = [
            'textbook_id' => $textbookId->value,
        ];

        try {
            AppLog::info(__METHOD__, [
                'request' => $requestParams,
            ]);

            //認証されたユーザーを取得
            $authenticatedUser = $this->userRepository->getAuthenticatedUser();
            if ($authenticatedUser === null) {
                throw new DomainException('認証済みユーザー情報が取得できませんでした。');
            }

            //取引情報を取得
            $deal = $this->dealRepository->findByTextbookId($textbookId);
            if ($deal === null) {
                throw new NotFoundException('取引が見つかりません。');
            }

            //取引情報を更新（キャンセル処理）+ DealEventを作成
            [$updatedDeal, $dealEvent] = $this->dealDomainService->cancel($deal);

            $this->transaction->begin();
            $this->dealRepository->update($updatedDeal);
            $this->dealEventRepository->insert($dealEvent);
            $this->transaction->commit();

        } catch (NotFoundException $e) {
            HandleUseCaseLogs::execMessage(__METHOD__, $e->getMessage(), $requestParams);
            throw $e;
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}
