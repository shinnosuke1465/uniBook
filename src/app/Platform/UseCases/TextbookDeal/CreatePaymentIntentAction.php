<?php

declare(strict_types=1);

namespace App\Platform\UseCases\TextbookDeal;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use App\Platform\Domains\Deal\Buyer;
use App\Platform\Domains\Deal\DealRepositoryInterface;
use App\Platform\Domains\Deal\DealStatus;
use App\Platform\Domains\Deal\Seller;
use App\Platform\Domains\PaymentIntent\PaymentIntentRepositoryInterface;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\Textbook\TextbookRepositoryInterface;
use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use App\Platform\UseCases\Textbook\Dtos\TextbookWithRelationsDto;
use App\Platform\UseCases\TextbookDeal\Dtos\PaymentIntentDto;
use AppLog;
use Illuminate\Auth\Access\AuthorizationException;

readonly class CreatePaymentIntentAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private DealRepositoryInterface $dealRepository,
        private PaymentIntentRepositoryInterface $paymentIntentRepository,
        private TextbookRepositoryInterface $textbookRepository,
    ) {
    }
    /**
     * @throws DomainException
     * @throws NotFoundException
     * @throws AuthorizationException
     */
    public function __invoke(
        CreatePaymentIntentActionValuesInterface $values,
        string $textbookId,
    ): PaymentIntentDto {
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

            //購入者の氏名、郵便番号、住所が未登録の場合は購入できない
            if (empty($buyer->name) || empty($buyer->postCode) || empty($buyer->address)) {
                throw new AuthorizationException();
            }

            //出品者が購入しようとした場合は認可エラー
            if ($deal->seller->userId->value === $buyer->id->value) {
                throw new AuthorizationException();
            }

            //取引ステータスが出品中以外の場合は認可エラー
            if (!in_array($deal->dealStatus, [DealStatus::Listing])) {
                throw new AuthorizationException();
            }

            //教科書を取得
            $textbook = $this->textbookRepository->findById($textbookId);
            if ($textbook === null) {
                throw new NotFoundException('教科書が見つかりません。');
            }

            $paymentIntent = $this->paymentIntentRepository->createPaymentIntent($textbook, $buyer);

            return PaymentIntentDto::create($paymentIntent);

        } catch (NotFoundException $e) {
            HandleUseCaseLogs::execMessage(__METHOD__, $e->getMessage(), $requestParams);
            throw $e;
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}
