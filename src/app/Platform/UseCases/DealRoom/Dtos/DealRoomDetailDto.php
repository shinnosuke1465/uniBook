<?php

declare(strict_types=1);

namespace App\Platform\UseCases\DealRoom\Dtos;

use App\Models\DealRoom as DealRoomDB;

readonly class DealRoomDetailDto
{
    public function __construct(
        public string $id,
        public DealDetailDto $deal,
        /** @var DealMessageDto[] */
        public array $messages,
    ) {
    }

    public static function fromEloquentModel(DealRoomDB $dealRoomModel): self
    {
        // Deal情報の構築
        $deal = new DealDetailDto(
            id: $dealRoomModel->deal->id,
            status: $dealRoomModel->deal->deal_status,
            textbook: new TextbookDetailDto(
                id: $dealRoomModel->deal->textbook->id,
                name: $dealRoomModel->deal->textbook->name,
                description: $dealRoomModel->deal->textbook->description,
                price: $dealRoomModel->deal->textbook->price,
                imageUrl: self::getTextbookImageUrl($dealRoomModel),
                imageUrls: self::getTextbookImageUrls($dealRoomModel),
            ),
            sellerInfo: new UserInfoDto(
                id: $dealRoomModel->deal->seller->id,
                name: $dealRoomModel->deal->seller->name,
                profileImageUrl: $dealRoomModel->deal->seller->image_id
                    ? "/api/images/{$dealRoomModel->deal->seller->image_id}"
                    : null
            ),
            buyerInfo: new BuyerInfoDto(
                id: $dealRoomModel->deal->buyer->id,
                name: $dealRoomModel->deal->buyer->name,
                postalCode: $dealRoomModel->deal->buyer->post_code,
                address: $dealRoomModel->deal->buyer->address,
                profileImageUrl: $dealRoomModel->deal->buyer->image_id
                    ? "/api/images/{$dealRoomModel->deal->buyer->image_id}"
                    : null
            ),
            dealEvents: array_map(
                fn($event) => new DealEventDto(
                    id: $event->id,
                    actorType: $event->actor_type,
                    eventType: $event->event_type,
                    createdAt: $event->created_at->toISOString(),
                ),
                $dealRoomModel->deal->dealEvents->all()
            )
        );

        // メッセージ情報の構築
        $messages = array_map(
            fn($message) => new DealMessageDto(
                id: $message->id,
                message: $message->message,
                createdAt: $message->created_at->toISOString(),
                user: new UserInfoDto(
                    id: $message->user->id,
                    name: $message->user->name,
                    profileImageUrl: $message->user->image_id
                        ? "/api/images/{$message->user->image_id}"
                        : null
                )
            ),
            $dealRoomModel->dealMessages->all()
        );

        return new self(
            $dealRoomModel->id,
            $deal,
            $messages,
        );
    }

    private static function getTextbookImageUrl(DealRoomDB $dealRoomModel): ?string
    {
        if (!$dealRoomModel->deal->textbook) {
            return null;
        }

        $firstImageId = $dealRoomModel->deal->textbook->imageIds->first();
        return $firstImageId ? "/api/images/{$firstImageId->image_id}" : null;
    }

    /**
     * @return string[]
     */
    private static function getTextbookImageUrls(DealRoomDB $dealRoomModel): array
    {
        if (!$dealRoomModel->deal->textbook) {
            return [];
        }

        return $dealRoomModel->deal->textbook->imageIds
            ->map(fn($imageId) => "/api/images/{$imageId->image_id}")
            ->toArray();
    }
}

readonly class DealDetailDto
{
    public function __construct(
        public string $id,
        public string $status,
        public TextbookDetailDto $textbook,
        public UserInfoDto $sellerInfo,
        public BuyerInfoDto $buyerInfo,
        /** @var DealEventDto[] */
        public array $dealEvents,
    ) {
    }
}

readonly class TextbookDetailDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public int $price,
        public ?string $imageUrl,
        /** @var string[] */
        public array $imageUrls,
    ) {
    }
}

readonly class UserInfoDto
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $profileImageUrl,
    ) {
    }
}

readonly class BuyerInfoDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $postalCode,
        public string $address,
        public ?string $profileImageUrl,
    ) {
    }
}

readonly class DealEventDto
{
    public function __construct(
        public string $id,
        public string $actorType,
        public string $eventType,
        public string $createdAt,
    ) {
    }
}

readonly class DealMessageDto
{
    public function __construct(
        public string $id,
        public string $message,
        public string $createdAt,
        public UserInfoDto $user,
    ) {
    }
}
