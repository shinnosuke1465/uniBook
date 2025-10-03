<?php

declare(strict_types=1);

namespace App\Platform\Presentations\User\Me\Controllers;

use App\Platform\UseCases\User\Me\Dtos\PurchasedProductDto;

readonly class GetPurchasedProductsResponseBuilder
{
    /**
     * PurchasedProductDtoの配列をAPIレスポンス形式に変換
     *
     * @param PurchasedProductDto[] $purchasedProducts
     * @return array<string, array<array<string, mixed>>>
     */
    public static function toArray(array $purchasedProducts): array
    {
        return [
            'products' => array_map(function (PurchasedProductDto $dto) {
                return [
                    'id' => $dto->id,
                    'name' => $dto->name,
                    'description' => $dto->description,
                    'image_urls' => $dto->imageUrls,
                    'price' => $dto->price,
                    'deal' => $dto->deal,
                ];
            }, $purchasedProducts),
        ];
    }
}
