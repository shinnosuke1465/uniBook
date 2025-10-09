<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Deal;

use App\Exceptions\DomainException;
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

    public function test_出品中の取引を購入できること(): void
    {
        // given
        $seller = new Seller(new UserId('11111111-1111-1111-1111-111111111111'));
        $textbookId = new TextbookId('33333333-3333-3333-3333-333333333333');
        $deal = new Deal(
            new DealId('44444444-4444-4444-4444-444444444444'),
            $seller,
            null,
            $textbookId,
            DealStatus::Listing
        );

        // when
        $buyer = new Buyer(new UserId('22222222-2222-2222-2222-222222222222'));
        $purchasedDeal = $deal->purchase($buyer);

        // then
        $this->assertNotSame($deal, $purchasedDeal);
        $this->assertEquals($deal->id, $purchasedDeal->id);
        $this->assertEquals($deal->seller, $purchasedDeal->seller);
        $this->assertEquals($deal->textbookId, $purchasedDeal->textbookId);
        $this->assertEquals($buyer, $purchasedDeal->buyer);
        $this->assertEquals(DealStatus::Purchased, $purchasedDeal->dealStatus);
    }

    public function test_出品中以外の取引は購入できないこと(): void
    {
        // given
        $seller = new Seller(new UserId('11111111-1111-1111-1111-111111111111'));
        $buyer = new Buyer(new UserId('22222222-2222-2222-2222-222222222222'));
        $textbookId = new TextbookId('33333333-3333-3333-3333-333333333333');
        $deal = new Deal(
            new DealId('44444444-4444-4444-4444-444444444444'),
            $seller,
            $buyer,
            $textbookId,
            DealStatus::Purchased
        );

        // then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('購入できるのは出品中の商品のみです。');

        // when
        $newBuyer = new Buyer(new UserId('33333333-3333-3333-3333-333333333333'));
        $deal->purchase($newBuyer);
    }

    public function test_出品中の取引をキャンセルできること(): void
    {
        // given
        $seller = new Seller(new UserId('11111111-1111-1111-1111-111111111111'));
        $textbookId = new TextbookId('33333333-3333-3333-3333-333333333333');
        $deal = new Deal(
            new DealId('44444444-4444-4444-4444-444444444444'),
            $seller,
            null,
            $textbookId,
            DealStatus::Listing
        );

        // when
        $cancelledDeal = $deal->cancel();

        // then
        $this->assertNotSame($deal, $cancelledDeal);
        $this->assertEquals($deal->id, $cancelledDeal->id);
        $this->assertEquals($deal->seller, $cancelledDeal->seller);
        $this->assertEquals($deal->textbookId, $cancelledDeal->textbookId);
        $this->assertNull($cancelledDeal->buyer);
        $this->assertEquals(DealStatus::Cancelled, $cancelledDeal->dealStatus);
    }

    public function test_出品中以外の取引はキャンセルできないこと(): void
    {
        // given
        $seller = new Seller(new UserId('11111111-1111-1111-1111-111111111111'));
        $buyer = new Buyer(new UserId('22222222-2222-2222-2222-222222222222'));
        $textbookId = new TextbookId('33333333-3333-3333-3333-333333333333');
        $deal = new Deal(
            new DealId('44444444-4444-4444-4444-444444444444'),
            $seller,
            $buyer,
            $textbookId,
            DealStatus::Purchased
        );

        // then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('キャンセルできるのは出品中の商品のみです。');

        // when
        $deal->cancel();
    }

    public function test_購入済みの取引の配送報告ができること(): void
    {
        // given
        $seller = new Seller(new UserId('11111111-1111-1111-1111-111111111111'));
        $buyer = new Buyer(new UserId('22222222-2222-2222-2222-222222222222'));
        $textbookId = new TextbookId('33333333-3333-3333-3333-333333333333');
        $deal = new Deal(
            new DealId('44444444-4444-4444-4444-444444444444'),
            $seller,
            $buyer,
            $textbookId,
            DealStatus::Purchased
        );

        // when
        $shippingDeal = $deal->reportDelivery();

        // then
        $this->assertNotSame($deal, $shippingDeal);
        $this->assertEquals($deal->id, $shippingDeal->id);
        $this->assertEquals($deal->seller, $shippingDeal->seller);
        $this->assertEquals($deal->buyer, $shippingDeal->buyer);
        $this->assertEquals($deal->textbookId, $shippingDeal->textbookId);
        $this->assertEquals(DealStatus::Shipping, $shippingDeal->dealStatus);
    }

    public function test_購入済み以外の取引は配送報告できないこと(): void
    {
        // given
        $seller = new Seller(new UserId('11111111-1111-1111-1111-111111111111'));
        $textbookId = new TextbookId('33333333-3333-3333-3333-333333333333');
        $deal = new Deal(
            new DealId('44444444-4444-4444-4444-444444444444'),
            $seller,
            null,
            $textbookId,
            DealStatus::Listing
        );

        // then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('配送報告できるのは購入済みの商品のみです。');

        // when
        $deal->reportDelivery();
    }

    public function test_配送中の取引の受取報告ができること(): void
    {
        // given
        $seller = new Seller(new UserId('11111111-1111-1111-1111-111111111111'));
        $buyer = new Buyer(new UserId('22222222-2222-2222-2222-222222222222'));
        $textbookId = new TextbookId('33333333-3333-3333-3333-333333333333');
        $deal = new Deal(
            new DealId('44444444-4444-4444-4444-444444444444'),
            $seller,
            $buyer,
            $textbookId,
            DealStatus::Shipping
        );

        // when
        $completedDeal = $deal->reportReceipt();

        // then
        $this->assertNotSame($deal, $completedDeal);
        $this->assertEquals($deal->id, $completedDeal->id);
        $this->assertEquals($deal->seller, $completedDeal->seller);
        $this->assertEquals($deal->buyer, $completedDeal->buyer);
        $this->assertEquals($deal->textbookId, $completedDeal->textbookId);
        $this->assertEquals(DealStatus::Completed, $completedDeal->dealStatus);
    }

    public function test_配送中以外の取引は受取報告できないこと(): void
    {
        // given
        $seller = new Seller(new UserId('11111111-1111-1111-1111-111111111111'));
        $buyer = new Buyer(new UserId('22222222-2222-2222-2222-222222222222'));
        $textbookId = new TextbookId('33333333-3333-3333-3333-333333333333');
        $deal = new Deal(
            new DealId('44444444-4444-4444-4444-444444444444'),
            $seller,
            $buyer,
            $textbookId,
            DealStatus::Purchased
        );

        // then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('受取報告できるのは配送中の商品のみです。');

        // when
        $deal->reportReceipt();
    }
}
