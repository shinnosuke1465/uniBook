<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\QueryServices\Textbook;

use App\Models\Deal;
use App\Platform\UseCases\User\Me\Dtos\PurchasedProductDto;

readonly class GetPurchasedTextbookDealDtoFactory
{
    /**
     * DealモデルからPurchasedProductDtoを作成（単体詳細用）
     *
     * @param Deal $deal
     * @return PurchasedProductDto
     */
    public static function createFromDeal(Deal $deal): PurchasedProductDto
    {
        $textbook = $deal->textbook;
        $imageIds = $textbook->imageIds->pluck('image_id')->toArray();

        // 画像URLの構築
        $imageUrls = array_map(fn($imageId) => "https://example.com/images/{$imageId}", $imageIds);
        $primaryImageUrl = !empty($imageUrls) ? $imageUrls[0] : null;

        // Deal情報の構築
        $dealInfo = [
            'id' => $deal->id,
            'is_purchasable' => false,
            'seller_info' => [
                'id' => $deal->seller->id,
                'nickname' => $deal->seller->name,
                'profile_image_url' => $deal->seller->image_id,
                'university_name' => $deal->seller->university->name ?? '',
                'faculty_name' => $deal->seller->faculty->name ?? '',
            ],
            'buyer_shipping_info' => [
                'id' => $deal->buyer->id,
                'name' => $deal->buyer->name,
                'postal_code' => $deal->buyer->postal_code ?? '',
                'address' => $deal->buyer->address ?? '',
                'nickname' => $deal->buyer->name,
                'profile_image_url' => $deal->buyer->image_id,
            ],
            'status' => strtolower($deal->deal_status),
            'deal_events' => $deal->dealEvents->map(function ($event) {
                return [
                    'id' => $event->id,
                    'actor_type' => strtolower($event->actor_type),
                    'event_type' => strtolower($event->event_type),
                    'created_at' => $event->created_at
                ];
            })->toArray(),
        ];

        return new PurchasedProductDto(
            id: $textbook->id,
            name: $textbook->name,
            description: $textbook->description ?? '',
            imageUrl: $primaryImageUrl,
            imageUrls: $imageUrls,
            price: $textbook->price,
            deal: $dealInfo,
        );
    }
}
