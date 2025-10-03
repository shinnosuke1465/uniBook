<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\QueryServices\Textbook;

use App\Models\Deal;
use App\Platform\UseCases\User\Me\Dtos\ListedTextbookDto;

readonly class GetListedTextbookDealDtoFactory
{
    /**
     * DealモデルからListedTextbookDtoを作成（単体詳細用）
     *
     * @param Deal $deal
     * @return ListedTextbookDto
     */
    public static function createFromDeal(Deal $deal): ListedTextbookDto
    {
        $textbook = $deal->textbook;

        // 画像URLの構築（getImagePath()メソッドを使用）
        $imageUrls = $textbook->imageIds
            ->map(fn($textbookImage) => $textbookImage->image?->getImagePath())
            ->filter() // nullを除外
            ->values()
            ->all();

        // buyer_shipping_infoの構築（出品中の場合はnull）
        $buyerShippingInfo = null;
        if ($deal->deal_status === 'Completed' && $deal->buyer) {
            $buyerShippingInfo = [
                'id' => $deal->buyer->id,
                'name' => $deal->buyer->name,
                'postal_code' => $deal->buyer->postal_code ?? '',
                'address' => $deal->buyer->address ?? '',
                'nickname' => $deal->buyer->name,
                'profile_image_url' => $deal->buyer->image_id,
            ];
        }

        // Deal情報の構築
        $dealInfo = [
            'id' => $deal->id,
            'is_purchasable' => $deal->deal_status === 'Listing',
            'seller_info' => [
                'id' => $deal->seller->id,
                'nickname' => $deal->seller->name,
                'profile_image_url' => $deal->seller->image_id,
                'university_name' => $deal->seller->university->name ?? '',
                'faculty_name' => $deal->seller->faculty->name ?? '',
            ],
            'buyer_shipping_info' => $buyerShippingInfo,
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

        return new ListedTextbookDto(
            id: $textbook->id,
            name: $textbook->name,
            description: $textbook->description ?? '',
            imageUrls: $imageUrls,
            price: $textbook->price,
            deal: $dealInfo,
        );
    }
}
