<?php

declare(strict_types=1);

namespace App\Platform\Presentations\DealRoom\Controllers;

use App\Platform\UseCases\DealRoom\Dtos\DealRoomDetailDto;

readonly class GetDealRoomResponseBuilder
{
    /**
     * @param DealRoomDetailDto $dto
     * @return array<string, mixed>
     */
    public static function toArray(DealRoomDetailDto $dto): array
    {
        return [
            'deal_room' => [
                'id' => $dto->id,
                'deal' => [
                    'id' => $dto->deal->id,
                    'status' => $dto->deal->status,
                    'textbook' => [
                        'id' => $dto->deal->textbook->id,
                        'name' => $dto->deal->textbook->name,
                        'description' => $dto->deal->textbook->description,
                        'price' => $dto->deal->textbook->price,
                        'image_url' => $dto->deal->textbook->imageUrl,
                        'image_urls' => $dto->deal->textbook->imageUrls,
                    ],
                    'seller_info' => [
                        'id' => $dto->deal->sellerInfo->id,
                        'name' => $dto->deal->sellerInfo->name,
                        'profile_image_url' => $dto->deal->sellerInfo->profileImageUrl,
                    ],
                    'buyer_info' => [
                        'id' => $dto->deal->buyerInfo->id,
                        'name' => $dto->deal->buyerInfo->name,
                        'postal_code' => $dto->deal->buyerInfo->postalCode,
                        'address' => $dto->deal->buyerInfo->address,
                        'profile_image_url' => $dto->deal->buyerInfo->profileImageUrl,
                    ],
                    'deal_events' => array_map(
                        fn($event) => [
                            'id' => $event->id,
                            'actor_type' => $event->actorType,
                            'event_type' => $event->eventType,
                            'created_at' => $event->createdAt,
                        ],
                        $dto->deal->dealEvents
                    ),
                ],
                'messages' => array_map(
                    fn($message) => [
                        'id' => $message->id,
                        'message' => $message->message,
                        'created_at' => $message->createdAt,
                        'user' => [
                            'id' => $message->user->id,
                            'name' => $message->user->name,
                            'profile_image_url' => $message->user->profileImageUrl,
                        ],
                    ],
                    $dto->messages
                ),
            ],
        ];
    }
}