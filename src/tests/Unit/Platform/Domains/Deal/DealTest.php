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
}
