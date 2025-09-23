<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Deal;

use App\Platform\Domains\Deal\Deal;
use App\Platform\Domains\Deal\DealId;
use App\Platform\Domains\Deal\DealStatus;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\User\UserId;
use Tests\TestCase;

class DealTest extends TestCase
{
    public function test_インスタンスが生成できること(): void
    {
        //given
        $expected = new DealId();
        $expectSellerId = new UserId();
        $expectBuyerId = new UserId();
        $expectTextbookId = new TextbookId();
        $expectDealStatus = DealStatus::Listing;

        //when
        $actualDeal = new Deal(
            id: $expected,
            sellerId: $expectSellerId,
            buyerId: $expectBuyerId,
            textbookId: $expectTextbookId,
            dealStatus: $expectDealStatus,
        );

        //then
        $this->assertEquals($expectSellerId, $actualDeal->sellerId);
        $this->assertEquals($expectBuyerId, $actualDeal->buyerId);
        $this->assertEquals($expectTextbookId, $actualDeal->textbookId);
        $this->assertEquals($expectDealStatus, $actualDeal->dealStatus);
    }

    public function test_staticで作成できること(): void
    {
        //given
        $expectSellerId = new UserId();
        $expectBuyerId = new UserId();
        $expectTextbookId = new TextbookId();
        $expectDealStatus = DealStatus::Listing;

        //when
        $actualDeal = Deal::create(
            sellerId: $expectSellerId,
            buyerId: $expectBuyerId,
            textbookId: $expectTextbookId,
            dealStatus: $expectDealStatus,
        );

        //then
        $this->assertEquals($expectSellerId, $actualDeal->sellerId);
        $this->assertEquals($expectBuyerId, $actualDeal->buyerId);
        $this->assertEquals($expectTextbookId, $actualDeal->textbookId);
        $this->assertEquals($expectDealStatus, $actualDeal->dealStatus);
    }
}
