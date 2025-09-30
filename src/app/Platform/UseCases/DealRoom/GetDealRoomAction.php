<?php

declare(strict_types=1);

namespace App\Platform\UseCases\DealRoom;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use App\Platform\Domains\DealRoom\DealRoomId;
use App\Platform\Domains\DealRoom\DealRoomRepositoryInterface;
use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\Infrastructures\DealRoom\DealRoomRepository;
use App\Platform\UseCases\DealRoom\Dtos\DealRoomDetailDto;
use AppLog;

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

            // ログインユーザー情報取得
            $authenticatedUser = $this->userRepository->getAuthenticatedUser();
            if (!$authenticatedUser) {
                throw new DomainException('認証済みユーザー情報が取得できませんでした。');
            }

            $userId = $authenticatedUser->getUserId();

            // Repositoryを通じて取引ルームを取得
            if ($this->dealRoomRepository instanceof DealRoomRepository) {
                $dealRoomModel = $this->dealRoomRepository->findByIdWithRelations($dealRoomId);

                if (!$dealRoomModel) {
                    throw new NotFoundException('指定された取引ルームが存在しません。');
                }

                // ユーザーが取引ルームに参加しているか確認
                $userIds = $dealRoomModel->users->pluck('id')->toArray();
                if (!in_array($userId->value, $userIds, true)) {
                    throw new DomainException('この取引ルームにアクセスする権限がありません。');
                }

                return DealRoomDetailDto::fromEloquentModel($dealRoomModel);
            }

            throw new DomainException('取引ルームの取得に失敗しました。');
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}