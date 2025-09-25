<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\DealEvent;

use App\Platform\Domains\Deal\DealId;
use App\Platform\Domains\DealEvent\ActorType;
use App\Platform\Domains\DealEvent\DealEvent;
use App\Platform\Domains\DealEvent\DealEventId;
use App\Platform\Domains\DealEvent\EventType;
use App\Platform\Domains\User\UserId;

class TestDealEventFactory
{
    public static function create(
        DealEventId $id = new DealEventId(),
        UserId $userId = new UserId(),
        DealId $dealId = new DealId(),
        ActorType $actorType = ActorType::Seller,
        EventType $eventType = EventType::Listing,
    ): DealEvent {
        return new DealEvent(
            $id,
            $userId,
            $dealId,
            $actorType,
            $eventType,
        );
    }
}