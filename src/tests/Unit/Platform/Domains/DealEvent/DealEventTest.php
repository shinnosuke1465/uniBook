<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\DealEvent;

use App\Platform\Domains\Deal\DealId;
use App\Platform\Domains\DealEvent\ActorType;
use App\Platform\Domains\DealEvent\DealEvent;
use App\Platform\Domains\DealEvent\DealEventId;
use App\Platform\Domains\DealEvent\EventType;
use App\Platform\Domains\User\UserId;
use Tests\TestCase;

class DealEventTest extends TestCase
{
    public function test_インスタンスが生成できること(): void
    {
        //given
        $expected = new DealEventId();
        $expectUserId = new UserId();
        $expectDealId = new DealId();
        $expectActorType = ActorType::Seller;
        $expectEventType = EventType::Listing;

        //when
        $actualDealEvent = new DealEvent(
            id: $expected,
            userId: $expectUserId,
            dealId: $expectDealId,
            actorType: $expectActorType,
            eventType: $expectEventType,
        );

        //then
        $this->assertEquals($expectUserId, $actualDealEvent->userId);
        $this->assertEquals($expectDealId, $actualDealEvent->dealId);
        $this->assertEquals($expectActorType, $actualDealEvent->actorType);
        $this->assertEquals($expectEventType, $actualDealEvent->eventType);
    }
    public function test_staticで作成できること(): void
    {
        //given
        $expectUserId = new UserId();
        $expectDealId = new DealId();
        $expectActorType = ActorType::Seller;
        $expectEventType = EventType::Listing;

        //when
        $actualDealEvent = DealEvent::create(
            userId: $expectUserId,
            dealId: $expectDealId,
            actorType: $expectActorType,
            eventType: $expectEventType,
        );

        //then
        $this->assertEquals($expectUserId, $actualDealEvent->userId);
        $this->assertEquals($expectDealId, $actualDealEvent->dealId);
        $this->assertEquals($expectActorType, $actualDealEvent->actorType);
        $this->assertEquals($expectEventType, $actualDealEvent->eventType);
    }
}
