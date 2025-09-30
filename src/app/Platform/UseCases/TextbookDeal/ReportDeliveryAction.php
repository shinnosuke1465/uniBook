<?php

declare(strict_types=1);

namespace App\Platform\UseCases\TextbookDeal;

use App\Platform\Domains\Deal\Buyer;
use App\Platform\Domains\Deal\DealRepositoryInterface;
use App\Platform\Domains\Deal\DealStatus;
use App\Platform\Domains\DealEvent\ActorType;
use App\Platform\Domains\DealEvent\DealEvent;
use App\Platform\Domains\DealEvent\DealEventRepositoryInterface;
use App\Platform\Domains\DealEvent\EventType;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use App\Platform\UseCases\Shared\Transaction\TransactionInterface;
use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use AppLog;
use Illuminate\Auth\Access\AuthorizationException;

readonly class ReportDeliveryAction
{
    public function __construct(
        private TransactionInterface $transaction,
        private DealRepositoryInterface $dealRepository,
        private DealEventRepositoryInterface $dealEventRepository,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @throws DomainException
     * @throws NotFoundException
     * @throws AuthorizationException
     */
    public function __invoke(
        ReportDeliveryActionValuesInterface $values,
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

            //取引ステータスが購入済み以外の場合は認可エラー
            if (!in_array($deal->dealStatus, [DealStatus::Purchased])) {
                throw new AuthorizationException('取引ステータスが購入済みではありません。');
            }

            $updateDeal = $deal->update(
                new Buyer($deal->buyer->userId),
                DealStatus::create('Shipping'),
            );

            $dealEvent = DealEvent::create(
                $authenticatedUser->getUserId(),
                $deal->id,
                ActorType::create('seller'),
                EventType::create('ReportDelivery')
            );

            $this->transaction->begin();
            //取引のstatusが購入済みから配送中に変更
            $this->dealRepository->update($updateDeal);

            //DealEventにsellerが配送報告した履歴を追加
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