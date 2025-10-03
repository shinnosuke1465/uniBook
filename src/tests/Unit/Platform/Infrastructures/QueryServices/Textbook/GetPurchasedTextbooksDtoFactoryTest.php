<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Infrastructures\QueryServices\Textbook;

use App\Models\Deal;
use App\Models\DealEvent;
use App\Models\Faculty;
use App\Models\Image;
use App\Models\Textbook;
use App\Models\TextbookImage;
use App\Models\University;
use App\Models\User;
use App\Platform\Infrastructures\QueryServices\Textbook\GetPurchasedTextbooksDtoFactory;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class GetPurchasedTextbooksDtoFactoryTest extends TestCase
{
    public function test_createFromDealsでDealからPurchasedProductDtoを正しく作成できること(): void
    {
        // given
        $university = new University([
            'id' => 'university-id',
            'name' => 'テスト大学',
        ]);

        $faculty = new Faculty([
            'id' => 'faculty-id',
            'name' => 'テスト学部',
            'university_id' => 'university-id',
        ]);

        $textbook = new Textbook([
            'id' => 'textbook-id',
            'name' => 'テスト教科書',
            'description' => 'テストの説明',
            'price' => 1500,
            'condition_type' => 'new',
            'university_id' => 'university-id',
            'faculty_id' => 'faculty-id',
        ]);
        $textbook->setRelation('university', $university);
        $textbook->setRelation('faculty', $faculty);

        $image1 = new Image(['id' => 'image-1']);
        $image2 = new Image(['id' => 'image-2']);

        $textbookImage1 = new TextbookImage(['image_id' => 'image-1']);
        $textbookImage1->setRelation('image', $image1);

        $textbookImage2 = new TextbookImage(['image_id' => 'image-2']);
        $textbookImage2->setRelation('image', $image2);

        $textbook->setRelation('imageIds', new Collection([
            $textbookImage1,
            $textbookImage2,
        ]));

        $seller = new User([
            'id' => 'seller-id',
            'name' => '販売者',
            'image_id' => 'seller-image-id',
        ]);

        $buyer = new User([
            'id' => 'buyer-id',
            'name' => '購入者',
            'image_id' => 'buyer-image-id',
        ]);

        $dealEvent = new DealEvent([
            'id' => 'deal-event-id',
            'actor_type' => 'SELLER',
            'event_type' => 'LISTING',
        ]);

        $deal = new Deal([
            'id' => 'deal-id',
            'seller_id' => 'seller-id',
            'buyer_id' => 'buyer-id',
            'textbook_id' => 'textbook-id',
            'deal_status' => 'Completed',
        ]);
        $deal->setRelation('textbook', $textbook);
        $deal->setRelation('seller', $seller);
        $deal->setRelation('buyer', $buyer);
        $deal->setRelation('dealEvents', new Collection([$dealEvent]));

        $deals = new Collection([$deal]);

        // when
        $result = GetPurchasedTextbooksDtoFactory::createFromDeals($deals);

        // then
        $this->assertCount(1, $result);
        $purchasedProduct = $result[0];

        $this->assertEquals('textbook-id', $purchasedProduct->id);
        $this->assertEquals('テスト教科書', $purchasedProduct->name);
        $this->assertEquals('テストの説明', $purchasedProduct->description);
        $this->assertEquals(1500, $purchasedProduct->price);
        $this->assertIsArray($purchasedProduct->imageUrls);

        // Deal情報の検証
        $dealInfo = $purchasedProduct->deal;
        $this->assertEquals('deal-id', $dealInfo['id']);
        $this->assertFalse($dealInfo['is_purchasable']);
        $this->assertEquals('completed', $dealInfo['status']);

        // Seller情報の検証
        $sellerInfo = $dealInfo['seller_info'];
        $this->assertEquals('seller-id', $sellerInfo['id']);
        $this->assertEquals('販売者', $sellerInfo['nickname']);
        $this->assertEquals('seller-image-id', $sellerInfo['profile_image_url']);

        // Buyer情報の検証
        $buyerInfo = $dealInfo['buyer_shipping_info'];
        $this->assertEquals('buyer-id', $buyerInfo['id']);
        $this->assertEquals('購入者', $buyerInfo['name']);
        $this->assertEquals('購入者', $buyerInfo['nickname']);
        $this->assertEquals('buyer-image-id', $buyerInfo['profile_image_url']);

        // DealEvents情報の検証
        $dealEvents = $dealInfo['deal_events'];
        $this->assertCount(1, $dealEvents);
        $this->assertEquals('deal-event-id', $dealEvents[0]['id']);
        $this->assertEquals('seller', $dealEvents[0]['actor_type']);
        $this->assertEquals('listing', $dealEvents[0]['event_type']);
    }

    public function test_createFromDealsで空のコレクションの場合は空の配列を返すこと(): void
    {
        // given
        $deals = new Collection([]);

        // when
        $result = GetPurchasedTextbooksDtoFactory::createFromDeals($deals);

        // then
        $this->assertCount(0, $result);
        $this->assertIsArray($result);
    }

    public function test_createFromDealsで画像がない場合はnullと空配列を返すこと(): void
    {
        // given
        $university = new University([
            'id' => 'university-id',
            'name' => 'テスト大学',
        ]);

        $faculty = new Faculty([
            'id' => 'faculty-id',
            'name' => 'テスト学部',
        ]);

        $textbook = new Textbook([
            'id' => 'textbook-id',
            'name' => 'テスト教科書',
            'description' => 'テストの説明',
            'price' => 1500,
            'condition_type' => 'new',
        ]);
        $textbook->setRelation('university', $university);
        $textbook->setRelation('faculty', $faculty);
        $textbook->setRelation('imageIds', new Collection([])); // 空の画像コレクション

        $seller = new User(['id' => 'seller-id', 'name' => '販売者']);
        $buyer = new User(['id' => 'buyer-id', 'name' => '購入者']);

        $deal = new Deal([
            'id' => 'deal-id',
            'deal_status' => 'Completed',
        ]);
        $deal->setRelation('textbook', $textbook);
        $deal->setRelation('seller', $seller);
        $deal->setRelation('buyer', $buyer);
        $deal->setRelation('dealEvents', new Collection([]));

        $deals = new Collection([$deal]);

        // when
        $result = GetPurchasedTextbooksDtoFactory::createFromDeals($deals);

        // then
        $this->assertCount(1, $result);
        $purchasedProduct = $result[0];
        $this->assertEmpty($purchasedProduct->imageUrls);
    }
}