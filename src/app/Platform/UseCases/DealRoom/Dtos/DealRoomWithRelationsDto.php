<?php

declare(strict_types=1);

namespace App\Platform\UseCases\DealRoom\Dtos;

use App\Models\DealRoom as DealRoomDB;

readonly class DealRoomWithRelationsDto
{
    public function __construct(
        public string $id,
        public DealDto $deal,
        public string $createdAt,
    ) {
    }

    public static function fromEloquentModel(DealRoomDB $dealRoomModel): self
    {
        // Deal情報の構築
        $deal = new DealDto(
            id: $dealRoomModel->deal->id,
            sellerInfo: new SellerInfoDto(
                id: $dealRoomModel->deal->seller->id,
                nickname: $dealRoomModel->deal->seller->name,
                profileImageUrl: $dealRoomModel->deal->seller->image_id
                    ? "/api/images/{$dealRoomModel->deal->seller->image_id}"
                    : null
            ),
            textbook: new TextbookDto(
                name: $dealRoomModel->deal->textbook->name ?? '',
                imageUrl: self::getTextbookImageUrl($dealRoomModel)
            )
        );

        return new self(
            $dealRoomModel->id,
            $deal,
            $dealRoomModel->created_at->toISOString(),
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
}

readonly class DealDto
{
    public function __construct(
        public string $id,
        public SellerInfoDto $sellerInfo,
        public TextbookDto $textbook,
    ) {
    }
}

readonly class SellerInfoDto
{
    public function __construct(
        public string $id,
        public string $nickname,
        public ?string $profileImageUrl,
    ) {
    }
}

readonly class TextbookDto
{
    public function __construct(
        public string $name,
        public ?string $imageUrl,
    ) {
    }
}