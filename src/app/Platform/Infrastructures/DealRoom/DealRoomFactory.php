<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\DealRoom;

use App\Exceptions\DomainException;
use App\Models\DealRoom as DealRoomDB;
use App\Platform\Domains\Deal\DealId;
use App\Platform\Domains\DealRoom\DealRoom;
use App\Platform\Domains\DealRoom\DealRoomId;
use App\Platform\Domains\User\UserId;
use App\Platform\Domains\User\UserIdList;

readonly class DealRoomFactory
{
    /**
     * @throws DomainException
     */
    public static function create(DealRoomDB $dealRoomDB): DealRoom
    {
        // ユーザーIDリストを作成
        $userIds = $dealRoomDB->users->map(
            fn ($user) => new UserId($user->id)
        )->all();

        return new DealRoom(
            id: new DealRoomId($dealRoomDB->id),
            dealId: new DealId($dealRoomDB->deal_id),
            userIds: new UserIdList($userIds),
        );
    }
}