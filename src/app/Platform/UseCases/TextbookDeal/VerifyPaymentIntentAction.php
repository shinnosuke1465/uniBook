<?php

declare(strict_types=1);

namespace App\Platform\UseCases\TextbookDeal;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use App\Platform\Domains\Deal\Buyer;
use App\Platform\Domains\Deal\DealRepositoryInterface;
use App\Platform\Domains\Deal\DealStatus;
use App\Platform\Domains\Deal\DealDomainService;
use App\Platform\Domains\DealEvent\DealEventRepositoryInterface;
use App\Platform\Domains\DealRoom\DealRoom;
use App\Platform\Domains\DealRoom\DealRoomRepositoryInterface;
use App\Platform\Domains\PaymentIntent\PaymentIntentRepositoryInterface;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\User\UserIdList;
use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use App\Platform\UseCases\Shared\Transaction\TransactionInterface;
use AppLog;
use Illuminate\Auth\Access\AuthorizationException;

readonly class VerifyPaymentIntentAction
{
    public function __construct(
        private TransactionInterface $transaction,
        private DealRepositoryInterface $dealRepository,
        private DealEventRepositoryInterface $dealEventRepository,
        private DealDomainService $dealDomainService,
        private UserRepositoryInterface $userRepository,
        private DealRoomRepositoryInterface $dealRoomRepository,
        private PaymentIntentRepositoryInterface $paymentIntentRepository,
    ) {
    }

    /**
     * @throws DomainException
     * @throws NotFoundException
     * @throws AuthorizationException
     */
    public function __invoke(
        VerifyPaymentIntentActionValuesInterface $values,
        string $textbookId,
    ): void {
        AppLog::start(__METHOD__);
        $textbookId = new TextbookId($textbookId);
        $paymentIntentId = $values->getPaymentIntentId();

        $requestParams = [
            'textbook_id' => $textbookId->value,
            'payment_intent_id' => $paymentIntentId->value,
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

            //購入者情報を取得
            $buyerId = new Buyer($authenticatedUser->getUserId());

            $buyer = $this->userRepository->findById($buyerId->userId);

            if ($buyer === null) {
                throw new NotFoundException('購入者が見つかりません。');
            }

            //取引情報を取得
            $deal = $this->dealRepository->findByTextbookId($textbookId);
            if ($deal === null) {
                throw new NotFoundException('取引が見つかりません。');
            }

            //認可処理
            $this->validateDealAndBuyer($buyer, $deal);

            //paymentIntentのチェック
            if (!$this->paymentIntentRepository->verifyPaymentIntent($paymentIntentId)) {
                throw new AuthorizationException('PaymentIntentの検証に失敗しました。');
            }

            //取引情報を更新（購入処理）+ DealEventを作成
            [$updatedDeal, $dealEvent] = $this->dealDomainService->purchase(
                $deal,
                new Buyer($buyer->id)
            );

            // 参加ユーザーIDリスト
            $userIds = new UserIdList([
                $deal->seller->userId,
                $buyer->id,
            ]);

            $dealRoom = DealRoom::create(
                $deal->id,
                $userIds,
            );

            $this->transaction->begin();
            $this->dealRepository->update($updatedDeal);
            $this->dealEventRepository->insert($dealEvent);

            //取引ルーム作成
            $this->dealRoomRepository->insert($dealRoom);

            $this->transaction->commit();

        } catch (NotFoundException $e) {
            HandleUseCaseLogs::execMessage(__METHOD__, $e->getMessage(), $requestParams);
            throw $e;
        } finally {
            AppLog::end(__METHOD__);
        }
    }

    private function validateDealAndBuyer($buyer, $deal): void
    {
        //購入者の氏名、郵便番号、住所が未登録の場合は購入できない
        if (empty($buyer->name) || empty($buyer->postCode) || empty($buyer->address)) {
            throw new AuthorizationException();
        }

        //出品者が購入しようとした場合は認可エラー
        if ($deal->seller->userId->value === $buyer->id->value) {
            throw new AuthorizationException();
        }

        //取引ステータスが出品中以外の場合は認可エラー
        if (!$deal->dealStatus->canPurchase()) {
            throw new AuthorizationException();
        }
    }
}
