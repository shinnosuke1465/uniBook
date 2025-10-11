<?php

declare(strict_types=1);

namespace App\Platform\UseCases\DealRoom;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use App\Platform\Domains\DealRoom\DealRoomId;
use App\Platform\Domains\DealRoom\DealRoomRepositoryInterface;
use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\UseCases\DealRoom\Dtos\DealRoomDetailDto;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use AppLog;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;

readonly class GetDealRoomAction
{
    public function __construct(
        private DealRoomRepositoryInterface $dealRoomRepository,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @throws DomainException
     * @throws NotFoundException
     */
    public function __invoke(
        GetDealRoomActionValuesInterface $actionValues,
        string $dealRoomIdString,
    ): DealRoomDetailDto {
        AppLog::start(__METHOD__);

        try {
            $dealRoomId = new DealRoomId($dealRoomIdString);

            // 認証ユーザー取得
            $authenticatedUser = $this->userRepository->getAuthenticatedUser();
            if (!$authenticatedUser) {
                throw new DomainException('認証済みユーザー情報が取得できませんでした。');
            }

            // ドメインオブジェクトを取得（認可チェック用）
            $dealRoom = $this->dealRoomRepository->findById($dealRoomId);
            if (!$dealRoom) {
                throw new NotFoundException('指定された取引ルームが存在しません。');
            }

            // 認可チェック（ドメインメソッドを使用）
            if (!$dealRoom->hasUser($authenticatedUser->getUserId())) {
                throw new AuthorizationException(
                    'この取引ルームにアクセスする権限がありません。'
                );
            }

            // 表示用データ取得（リレーション込み）
            $dealRoomModel = $this->dealRoomRepository->findByIdWithRelations($dealRoomId);

            return DealRoomDetailDto::fromEloquentModel($dealRoomModel);

        } catch (Exception $e) {
            HandleUseCaseLogs::execMessage(__METHOD__, $e->getMessage(), []);
            throw $e;
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}
