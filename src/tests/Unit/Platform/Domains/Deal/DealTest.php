<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Deal;

use App\Platform\Domains\Deal\Buyer;
use App\Platform\Domains\Deal\Deal;
use App\Platform\Domains\Deal\DealId;
use App\Platform\Domains\Deal\DealStatus;
use App\Platform\Domains\Deal\Seller;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\User\UserId;
use Tests\TestCase;

class DealTest extends TestCase
{
    public function test_インスタンスが生成できること(): void
    {
        //given
        $expected = new DealId();
        $expectSeller = new Seller(new UserId());
        $expectBuyer = new Buyer(new UserId());
        $expectTextbookId = new TextbookId();
        $expectDealStatus = DealStatus::Listing;

        //when
        $actualDeal = new Deal(
            id: $expected,
            seller: $expectSeller,
            buyer: $expectBuyer,
            textbookId: $expectTextbookId,
            dealStatus: $expectDealStatus,
        );

        //then
        $this->assertEquals($expectSeller, $actualDeal->seller);
        $this->assertEquals($expectBuyer, $actualDeal->buyer);
        $this->assertEquals($expectTextbookId, $actualDeal->textbookId);
        $this->assertEquals($expectDealStatus, $actualDeal->dealStatus);
    }

    public function test_staticで作成できること(): void
    {
        //given
        $expectSeller = new Seller(new UserId());
        $expectBuyer = new Buyer(new UserId());
        $expectTextbookId = new TextbookId();
        $expectDealStatus = DealStatus::Listing;

        //when
        $actualDeal = Deal::create(
            seller: $expectSeller,
            buyer: $expectBuyer,
            textbookId: $expectTextbookId,
            dealStatus: $expectDealStatus,
        );

        //then
        $this->assertEquals($expectSeller, $actualDeal->seller);
        $this->assertEquals($expectBuyer, $actualDeal->buyer);
        $this->assertEquals($expectTextbookId, $actualDeal->textbookId);
        $this->assertEquals($expectDealStatus, $actualDeal->dealStatus);
    }

    public function test_updateできること(): void
    {
        // given
        $seller = new Seller(new UserId('11111111-1111-1111-1111-111111111111'));
        $buyer1 = new Buyer(new UserId('22222222-2222-2222-2222-222222222222'));
        $textbookId = new TextbookId('33333333-3333-3333-3333-333333333333');
        $dealStatus1 = DealStatus::create('Listing');
        $deal = new Deal(
            new DealId('44444444-4444-4444-4444-444444444444'),
            $seller,
            $buyer1,
            $textbookId,
            $dealStatus1
        );

        // when
        $buyer2 = new Buyer(new UserId('55555555-5555-5555-5555-555555555555'));
        $dealStatus2 = DealStatus::create('Purchased');
        $updatedDeal = $deal->update($buyer2, $dealStatus2);

        // then
        $this->assertNotSame($deal, $updatedDeal);
        $this->assertEquals($deal->id, $updatedDeal->id);
        $this->assertEquals($deal->seller, $updatedDeal->seller);
        $this->assertEquals($deal->textbookId, $updatedDeal->textbookId);
        $this->assertEquals($buyer2, $updatedDeal->buyer);
        $this->assertEquals($dealStatus2, $updatedDeal->dealStatus);
    }
}
