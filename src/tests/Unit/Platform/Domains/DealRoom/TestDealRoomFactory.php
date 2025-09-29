<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\DealRoom;

use App\Platform\Domains\Deal\DealId;
use App\Platform\Domains\DealRoom\DealRoom;
use App\Platform\Domains\DealRoom\DealRoomId;
use App\Platform\Domains\User\UserId;
use App\Platform\Domains\User\UserIdList;

class TestDealRoomFactory
{
    public static function create(
        ?DealRoomId $id = null,
        ?DealId $dealId = null,
        ?UserIdList $userIds = null,
    ): DealRoom {
        return new DealRoom(
            id: $id ?? new DealRoomId(),
            dealId: $dealId ?? new DealId(),
            userIds: $userIds ?? new UserIdList([new UserId(), new UserId()]),
        );
    }
}
