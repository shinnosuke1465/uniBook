<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\DealEvent;

use App\Platform\Domains\Deal\Buyer;
use App\Platform\Domains\Deal\DealId;
use App\Platform\Domains\Deal\Seller;
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

    public function test_出品イベントを作成できること(): void
    {
        // given
        $seller = new Seller(new UserId('11111111-1111-1111-1111-111111111111'));
        $dealId = new DealId('22222222-2222-2222-2222-222222222222');

        // when
        $dealEvent = DealEvent::listed($seller, $dealId);

        // then
        $this->assertEquals($seller->userId, $dealEvent->userId);
        $this->assertEquals($dealId, $dealEvent->dealId);
        $this->assertEquals(ActorType::Seller, $dealEvent->actorType);
        $this->assertEquals(EventType::Listing, $dealEvent->eventType);
    }

    public function test_購入イベントを作成できること(): void
    {
        // given
        $buyer = new Buyer(new UserId('11111111-1111-1111-1111-111111111111'));
        $dealId = new DealId('22222222-2222-2222-2222-222222222222');

        // when
        $dealEvent = DealEvent::purchased($buyer, $dealId);

        // then
        $this->assertEquals($buyer->userId, $dealEvent->userId);
        $this->assertEquals($dealId, $dealEvent->dealId);
        $this->assertEquals(ActorType::Buyer, $dealEvent->actorType);
        $this->assertEquals(EventType::Purchase, $dealEvent->eventType);
    }

    public function test_配送報告イベントを作成できること(): void
    {
        // given
        $seller = new Seller(new UserId('11111111-1111-1111-1111-111111111111'));
        $dealId = new DealId('22222222-2222-2222-2222-222222222222');

        // when
        $dealEvent = DealEvent::delivered($seller, $dealId);

        // then
        $this->assertEquals($seller->userId, $dealEvent->userId);
        $this->assertEquals($dealId, $dealEvent->dealId);
        $this->assertEquals(ActorType::Seller, $dealEvent->actorType);
        $this->assertEquals(EventType::ReportDelivery, $dealEvent->eventType);
    }

    public function test_受取報告イベントを作成できること(): void
    {
        // given
        $buyer = new Buyer(new UserId('11111111-1111-1111-1111-111111111111'));
        $dealId = new DealId('22222222-2222-2222-2222-222222222222');

        // when
        $dealEvent = DealEvent::received($buyer, $dealId);

        // then
        $this->assertEquals($buyer->userId, $dealEvent->userId);
        $this->assertEquals($dealId, $dealEvent->dealId);
        $this->assertEquals(ActorType::Buyer, $dealEvent->actorType);
        $this->assertEquals(EventType::ReportReceipt, $dealEvent->eventType);
    }

    public function test_キャンセルイベントを作成できること(): void
    {
        // given
        $seller = new Seller(new UserId('11111111-1111-1111-1111-111111111111'));
        $dealId = new DealId('22222222-2222-2222-2222-222222222222');

        // when
        $dealEvent = DealEvent::cancelled($seller, $dealId);

        // then
        $this->assertEquals($seller->userId, $dealEvent->userId);
        $this->assertEquals($dealId, $dealEvent->dealId);
        $this->assertEquals(ActorType::Seller, $dealEvent->actorType);
        $this->assertEquals(EventType::Cancel, $dealEvent->eventType);
    }
}
