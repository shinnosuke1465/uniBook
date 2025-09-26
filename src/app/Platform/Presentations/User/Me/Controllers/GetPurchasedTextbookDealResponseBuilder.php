<?php

declare(strict_types=1);

namespace App\Platform\Presentations\User\Me\Controllers;

use App\Platform\UseCases\User\Me\Dtos\PurchasedProductDto;

readonly class GetPurchasedTextbookDealResponseBuilder
{
    /**
     * PurchasedProductDtoを単体のAPIレスポンス形式に変換
     * 購入商品一覧の配列要素と同じ構造で返す
     *
     * @param PurchasedProductDto $dto
     * @return array<string, mixed>
     */
    public static function toArray(PurchasedProductDto $dto): array
    {
        return [
            'id' => $dto->id,
            'name' => $dto->name,
            'description' => $dto->description,
            'image_url' => $dto->imageUrl,
            'image_urls' => $dto->imageUrls,
            'price' => $dto->price,
            'deal' => $dto->deal,
        ];
    }
}