<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Deal;

use App\Exceptions\DomainException;
use App\Platform\Domains\Deal\Buyer;
use App\Platform\Domains\Deal\Deal;
use App\Platform\Domains\Deal\DealDomainService;
use App\Platform\Domains\Deal\DealId;
use App\Platform\Domains\Deal\DealStatus;
use App\Platform\Domains\Deal\Seller;
use App\Platform\Domains\DealEvent\ActorType;
use App\Platform\Domains\DealEvent\EventType;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\User\UserId;
use Tests\TestCase;

class DealDomainServiceTest extends TestCase
{
    private DealDomainService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DealDomainService();
    }

    public function test_出品処理でDealとDealEventを作成できること(): void
    {
        // given
        $seller = new Seller(new UserId('11111111-1111-1111-1111-111111111111'));
        $textbookId = new TextbookId('22222222-2222-2222-2222-222222222222');

        // when
        [$deal, $dealEvent] = $this->service->createListing($seller, $textbookId);

        // then
        $this->assertEquals($seller, $deal->seller);
        $this->assertEquals($textbookId, $deal->textbookId);
        $this->assertEquals(DealStatus::Listing, $deal->dealStatus);
        $this->assertNull($deal->buyer);

        $this->assertEquals($seller->userId, $dealEvent->userId);
        $this->assertEquals($deal->id, $dealEvent->dealId);
        $this->assertEquals(ActorType::Seller, $dealEvent->actorType);
        $this->assertEquals(EventType::Listing, $dealEvent->eventType);
    }

    public function test_購入処理でDealとDealEventを返すこと(): void
    {
        // given
        $seller = new Seller(new UserId('11111111-1111-1111-1111-111111111111'));
        $buyer = new Buyer(new UserId('33333333-3333-3333-3333-333333333333'));
        $textbookId = new TextbookId('22222222-2222-2222-2222-222222222222');

        $deal = Deal::create($seller, null, $textbookId, DealStatus::create('Listing'));

        // when
        [$updatedDeal, $dealEvent] = $this->service->purchase($deal, $buyer);

        // then
        $this->assertEquals($buyer, $updatedDeal->buyer);
        $this->assertEquals(DealStatus::Purchased, $updatedDeal->dealStatus);

        $this->assertEquals($buyer->userId, $dealEvent->userId);
        $this->assertEquals($deal->id, $dealEvent->dealId);
        $this->assertEquals(ActorType::Buyer, $dealEvent->actorType);
        $this->assertEquals(EventType::Purchase, $dealEvent->eventType);
    }

    public function test_購入処理で出品中以外の場合は例外をスローすること(): void
    {
        // given
        $seller = new Seller(new UserId('11111111-1111-1111-1111-111111111111'));
        $buyer = new Buyer(new UserId('33333333-3333-3333-3333-333333333333'));
        $textbookId = new TextbookId('22222222-2222-2222-2222-222222222222');

        $deal = Deal::create($seller, $buyer, $textbookId, DealStatus::create('Purchased'));

        // then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('購入できるのは出品中の商品のみです。');

        // when
        $this->service->purchase($deal, $buyer);
    }

    public function test_キャンセル処理でDealとDealEventを返すこと(): void
    {
        // given
        $seller = new Seller(new UserId('11111111-1111-1111-1111-111111111111'));
        $textbookId = new TextbookId('22222222-2222-2222-2222-222222222222');

        $deal = Deal::create($seller, null, $textbookId, DealStatus::create('Listing'));

        // when
        [$updatedDeal, $dealEvent] = $this->service->cancel($deal);

        // then
        $this->assertEquals(DealStatus::Cancelled, $updatedDeal->dealStatus);

        $this->assertEquals($seller->userId, $dealEvent->userId);
        $this->assertEquals($deal->id, $dealEvent->dealId);
        $this->assertEquals(ActorType::Seller, $dealEvent->actorType);
        $this->assertEquals(EventType::Cancel, $dealEvent->eventType);
    }

    public function test_キャンセル処理で出品中以外の場合は例外をスローすること(): void
    {
        // given
        $seller = new Seller(new UserId('11111111-1111-1111-1111-111111111111'));
        $buyer = new Buyer(new UserId('33333333-3333-3333-3333-333333333333'));
        $textbookId = new TextbookId('22222222-2222-2222-2222-222222222222');

        $deal = Deal::create($seller, $buyer, $textbookId, DealStatus::create('Purchased'));

        // then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('キャンセルできるのは出品中の商品のみです。');

        // when
        $this->service->cancel($deal);
    }

    public function test_配送報告処理でDealとDealEventを返すこと(): void
    {
        // given
        $seller = new Seller(new UserId('11111111-1111-1111-1111-111111111111'));
        $buyer = new Buyer(new UserId('33333333-3333-3333-3333-333333333333'));
        $textbookId = new TextbookId('22222222-2222-2222-2222-222222222222');

        $deal = Deal::create($seller, $buyer, $textbookId, DealStatus::create('Purchased'));

        // when
        [$updatedDeal, $dealEvent] = $this->service->reportDelivery($deal);

        // then
        $this->assertEquals(DealStatus::Shipping, $updatedDeal->dealStatus);

        $this->assertEquals($seller->userId, $dealEvent->userId);
        $this->assertEquals($deal->id, $dealEvent->dealId);
        $this->assertEquals(ActorType::Seller, $dealEvent->actorType);
        $this->assertEquals(EventType::ReportDelivery, $dealEvent->eventType);
    }

    public function test_配送報告処理で購入済み以外の場合は例外をスローすること(): void
    {
        // given
        $seller = new Seller(new UserId('11111111-1111-1111-1111-111111111111'));
        $textbookId = new TextbookId('22222222-2222-2222-2222-222222222222');

        $deal = Deal::create($seller, null, $textbookId, DealStatus::create('Listing'));

        // then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('配送報告できるのは購入済みの商品のみです。');

        // when
        $this->service->reportDelivery($deal);
    }

    public function test_受取報告処理でDealとDealEventを返すこと(): void
    {
        // given
        $seller = new Seller(new UserId('11111111-1111-1111-1111-111111111111'));
        $buyer = new Buyer(new UserId('33333333-3333-3333-3333-333333333333'));
        $textbookId = new TextbookId('22222222-2222-2222-2222-222222222222');

        $deal = Deal::create($seller, $buyer, $textbookId, DealStatus::create('Shipping'));

        // when
        [$updatedDeal, $dealEvent] = $this->service->reportReceipt($deal);

        // then
        $this->assertEquals(DealStatus::Completed, $updatedDeal->dealStatus);

        $this->assertEquals($buyer->userId, $dealEvent->userId);
        $this->assertEquals($deal->id, $dealEvent->dealId);
        $this->assertEquals(ActorType::Buyer, $dealEvent->actorType);
        $this->assertEquals(EventType::ReportReceipt, $dealEvent->eventType);
    }

    public function test_受取報告処理で配送中以外の場合は例外をスローすること(): void
    {
        // given
        $seller = new Seller(new UserId('11111111-1111-1111-1111-111111111111'));
        $buyer = new Buyer(new UserId('33333333-3333-3333-3333-333333333333'));
        $textbookId = new TextbookId('22222222-2222-2222-2222-222222222222');

        $deal = Deal::create($seller, $buyer, $textbookId, DealStatus::create('Purchased'));

        // then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('受取報告できるのは配送中の商品のみです。');

        // when
        $this->service->reportReceipt($deal);
    }

}
