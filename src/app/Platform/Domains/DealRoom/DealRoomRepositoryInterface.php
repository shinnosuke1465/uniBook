<?php

declare(strict_types=1);

namespace App\Platform\Domains\DealRoom;

use App\Platform\Domains\Deal\DealId;
use App\Platform\Domains\User\UserId;

interface DealRoomRepositoryInterface
{
    /**
     * 取引ルームを保存
     *
     * @param DealRoom $dealRoom
     * @return void
     */
    public function insert(DealRoom $dealRoom): void;

    /**
     * IDで取引ルームを取得
     *
     * @param DealRoomId $dealRoomId
     * @return DealRoom|null
     */
    public function findById(DealRoomId $dealRoomId): ?DealRoom;

    /**
     * ユーザーが参加している取引ルームを取得
     *
     * @param UserId $userId
     * @return DealRoom[]
     */
    public function findByUserId(UserId $userId): array;

}
