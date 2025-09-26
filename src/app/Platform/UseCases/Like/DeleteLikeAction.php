<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Like;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use App\Platform\Domains\Like\LikeRepositoryInterface;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\Textbook\TextbookRepositoryInterface;
use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use App\Platform\UseCases\Shared\Transaction\TransactionInterface;
use AppLog;
use Exception;

readonly class DeleteLikeAction
{
    public function __construct(
        private TransactionInterface $transaction,
        private LikeRepositoryInterface $likeRepository,
        private UserRepositoryInterface $userRepository,
        private TextbookRepositoryInterface $textbookRepository,
    ) {
    }

    /**
     * @throws DomainException
     * @throws NotFoundException
     * @throws Exception
     */
    public function __invoke(
        DeleteLikeActionValuesInterface $actionValues,
        string $textbookIdString,
    ): void {
        AppLog::start(__METHOD__);
        $textbookId = new TextbookId($textbookIdString);
        $requestParams = [
            'textbook_id' => $textbookId->value,
        ];

        try {
            AppLog::info(__METHOD__, [
                'request' => $requestParams,
            ]);

            //教科書確認
            $textbook = $this->textbookRepository->findById($textbookId);
            if ($textbook === null) {
                throw new NotFoundException('指定された教科書が存在しません。');
            }

            //認証されたユーザーを取得
            $authenticatedUser = $this->userRepository->getAuthenticatedUser();
            if ($authenticatedUser === null) {
                throw new DomainException('認証済みユーザー情報が取得できませんでした。');
            }

            // いいねの存在確認
            $existingLike = $this->likeRepository->findByUserIdAndTextbookId(
                $authenticatedUser->getUserId(),
                $textbookId
            );
            if ($existingLike === null) {
                throw new NotFoundException('削除対象のいいねが存在しません。');
            }

            $this->transaction->begin();
            $this->likeRepository->delete($authenticatedUser->getUserId(), $textbookId);
            $this->transaction->commit();
        } catch (Exception $e) {
            HandleUseCaseLogs::execMessage(__METHOD__, $e->getMessage(), $requestParams);
            $this->transaction->rollback();
            throw $e;
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}