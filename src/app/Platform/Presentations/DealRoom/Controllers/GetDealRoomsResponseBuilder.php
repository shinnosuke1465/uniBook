<?php

declare(strict_types=1);

namespace App\Platform\Presentations\DealRoom\Controllers;

use App\Platform\UseCases\DealRoom\Dtos\DealRoomWithRelationsDto;

readonly class GetDealRoomsResponseBuilder
{
    /**
     * @param array $dtos
     * @return array<string, array<array<string, mixed>>>
     */
    public static function toArray(array $dtos): array
    {
        return [
            'deal_rooms' => collect($dtos)->map(
                fn (DealRoomWithRelationsDto $dto) => [
                    'id' => $dto->id,
                    'deal' => [
                        'id' => $dto->deal->id,
                        'seller_info' => [
                            'id' => $dto->deal->sellerInfo->id,
                            'nickname' => $dto->deal->sellerInfo->nickname,
                            'profile_image_url' => $dto->deal->sellerInfo->profileImageUrl,
                        ],
                        'textbook' => [
                            'name' => $dto->deal->textbook->name,
                            'image_url' => $dto->deal->textbook->imageUrl,
                        ],
                    ],
                    'created_at' => $dto->createdAt,
                ]
            )->values()->all(),
        ];
    }
}
