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
                imageUrls: self::getTextbookImageUrls($dealRoomModel)
            )
        );

        return new self(
            $dealRoomModel->id,
            $deal,
            $dealRoomModel->created_at->toISOString(),
        );
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
            ->map(fn($textbookImage) => $textbookImage->image?->getImagePath())
            ->filter() // nullを除外
            ->values()
            ->toArray();
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
        /** @var string[] */
        public array $imageUrls,
    ) {
    }
}