<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\QueryServices\Textbook;

use App\Models\Deal;
use App\Platform\UseCases\User\Me\Dtos\ListedTextbookDto;
use Illuminate\Database\Eloquent\Collection;

readonly class GetListedTextbooksDtoFactory
{
    /**
     * DealモデルのコレクションからListedTextbookDtoの配列を作成
     *
     * @param Collection<Deal> $deals
     * @return ListedTextbookDto[]
     */
    public static function createFromDeals(Collection $deals): array
    {
        return $deals->map(function (Deal $deal) {
            $textbook = $deal->textbook;

            // 画像URLの構築（getImagePath()メソッドを使用）
            $imageUrls = $textbook->imageIds
                ->map(fn($textbookImage) => $textbookImage->image?->getImagePath())
                ->filter() // nullを除外
                ->values()
                ->all();

            // Deal情報の構築
            $dealInfo = [
                'id' => $deal->id,
                'is_purchasable' => $deal->deal_status === 'Listing', // Listingの場合のみ購入可能
                'seller_info' => [
                    'id' => $deal->seller->id,
                    'nickname' => $deal->seller->name,
                    'profile_image_url' => $deal->seller->image_id,
                    'university_name' => $deal->seller->university->name ?? '',
                    'faculty_name' => $deal->seller->faculty->name ?? '',
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

            // buyer_shipping_infoは購入者がいる場合のみ追加
            if ($deal->buyer) {
                $dealInfo['buyer_shipping_info'] = [
                    'id' => $deal->buyer->id,
                    'name' => $deal->buyer->name,
                    'postal_code' => $deal->buyer->postal_code ?? '',
                    'address' => $deal->buyer->address ?? '',
                    'nickname' => $deal->buyer->name,
                    'profile_image_url' => $deal->buyer->image_id,
                ];
            } else {
                // 購入者がいない場合（出品中の場合）
                $dealInfo['buyer_shipping_info'] = null;
            }

            return new ListedTextbookDto(
                id: $textbook->id,
                name: $textbook->name,
                description: $textbook->description ?? '',
                imageUrls: $imageUrls,
                price: $textbook->price,
                deal: $dealInfo,
            );
        })->all();
    }
}
