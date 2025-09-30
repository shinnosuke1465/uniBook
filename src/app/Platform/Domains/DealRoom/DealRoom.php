<?php

declare(strict_types=1);

namespace App\Platform\Domains\DealRoom;

use App\Exceptions\DomainException;
use App\Platform\Domains\Deal\DealId;
use App\Platform\Domains\Deal\DealStatus;
use App\Platform\Domains\User\UserIdList;

readonly class DealRoom
{
    public function __construct(
        public DealRoomId $id,
        public DealId $dealId,
        public UserIdList $userIds,
    ) {
    }

    /**
     * 取引ルームを作成
     *
     * @param DealId $dealId
     * @param UserIdList $userIds
     * @param DealStatus $textbookDealStatus
     * @return self
     * @throws DomainException
     */
    public static function create(
        DealId $dealId,
        UserIdList $userIds,
        DealStatus $textbookDealStatus,
    ): self {
        // ビジネスルール1: textbookが出品中（Listing or Cancelled or Purchased）でないとDealRoom作成不可
        if (!self::isTextbookAvailableForDealRoom($textbookDealStatus)) {
            throw new DomainException('教科書が購入済みでないため、取引ルームを作成できません。');
        }

        // ビジネスルール2: チャットルームにはユーザーが二人必要
        if (!self::hasRequiredUsers($userIds)) {
            throw new DomainException('取引ルームには2人のユーザーが必要です。');
        }

        return new self(
            new DealRoomId(),
            $dealId,
            $userIds,
        );
    }

    /**
     * ユーザーIDの配列を取得
     *
     * @return array<string>
     */
    public function getUserIds(): array
    {
        return $this->userIds->toStringArray();
    }

    /**
     * 教科書が取引ルーム作成可能な状態かチェック
     *
     * @param DealStatus $dealStatus
     * @return bool
     */
    private static function isTextbookAvailableForDealRoom(DealStatus $dealStatus): bool
    {
        return $dealStatus === DealStatus::Purchased;
    }

    /**
     * 必要なユーザー数がいるかチェック
     *
     * @param UserIdList $userIds
     * @return bool
     */
    private static function hasRequiredUsers(UserIdList $userIds): bool
    {
        return count($userIds->toStringArray()) === 2;
    }
}
