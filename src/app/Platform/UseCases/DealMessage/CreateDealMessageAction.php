<?php

declare(strict_types=1);

namespace App\Platform\UseCases\DealMessage;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use App\Platform\Domains\DealMessage\DealMessage;
use App\Platform\Domains\DealMessage\DealMessageDomainService;
use App\Platform\Domains\DealMessage\DealMessageRepositoryInterface;
use App\Platform\Domains\DealRoom\DealRoomId;
use App\Platform\Domains\DealRoom\DealRoomRepositoryInterface;
use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use App\Platform\UseCases\Shared\Transaction\TransactionInterface;
use App\Platform\Domains\DealMessage\Sender;
use AppLog;
use Exception;

readonly class CreateDealMessageAction
{
    public function __construct(
        private TransactionInterface $transaction,
        private DealMessageRepositoryInterface $dealMessageRepository,
        private DealRoomRepositoryInterface $dealRoomRepository,
        private DealMessageDomainService $dealMessageDomainService,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @throws DomainException
     * @throws NotFoundException
     * @throws Exception
     */
    public function __invoke(
        CreateDealMessageActionValuesInterface $actionValues,
        string $dealRoomIdString,
    ): void {
        AppLog::start(__METHOD__);
        $dealRoomId = new DealRoomId($dealRoomIdString);
        $requestParams = [];

        try {
            $message = $actionValues->getMessage();

            $requestParams = [
                'message' => $message->value,
                'deal_room_id' => $dealRoomId->value,
            ];

            AppLog::info(__METHOD__, [
                'request' => $requestParams,
            ]);

            //取引ルーム確認
            $dealRoom = $this->dealRoomRepository->findById($dealRoomId);
            if ($dealRoom === null) {
                throw new NotFoundException('指定された取引ルームが存在しません。');
            }

            //認証されたユーザーを取得
            $authenticatedUser = $this->userRepository->getAuthenticatedUser();
            if ($authenticatedUser === null) {
                throw new DomainException('認証済みユーザー情報が取得できませんでした。');
            }

            // ドメインサービスでメッセージ作成
            $dealMessage = $this->dealMessageDomainService->createMessage(
                dealRoom: $dealRoom,
                senderId: $authenticatedUser->getUserId(),
                message: $message
            );

            $this->transaction->begin();
            $this->dealMessageRepository->insert($dealMessage);
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
