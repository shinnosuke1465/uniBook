<?php

declare(strict_types=1);

namespace App\Platform\UseCases\DealRoom;

use App\Exceptions\DomainException;
use AppLog;
use App\Platform\Domains\DealRoom\DealRoomRepositoryInterface;
use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\Infrastructures\DealRoom\DealRoomRepository;
use App\Platform\UseCases\DealRoom\Dtos\DealRoomWithRelationsDto;

readonly class GetDealRoomsAction
{
    public function __construct(
        private DealRoomRepositoryInterface $dealRoomRepository,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @return DealRoomWithRelationsDto[]
     */
    public function __invoke(
        GetDealRoomsActionValuesInterface $actionValues,
    ): DealRoomWithRelationsDto[] {
        AppLog::start(__METHOD__);

        try {
            // ログインユーザー情報取得
            $authenticatedUser = $this->userRepository->getAuthenticatedUser();
            if (!$authenticatedUser) {
                throw new DomainException('認証済みユーザー情報が取得できませんでした。');
            }

            $userId = $authenticatedUser->getUserId();

            // Repositoryを通じて取引ルームを取得
            if ($this->dealRoomRepository instanceof DealRoomRepository) {
                // リレーション付きで取得するメソッドを使用
                $dealRoomModels = $this->dealRoomRepository->findByUserIdWithRelations($userId);

                return array_map(function($dealRoomModel) {
                    return DealRoomWithRelationsDto::fromEloquentModel($dealRoomModel);
                }, $dealRoomModels);
            }

        } finally {
            AppLog::end(__METHOD__);
        }
    }
}
