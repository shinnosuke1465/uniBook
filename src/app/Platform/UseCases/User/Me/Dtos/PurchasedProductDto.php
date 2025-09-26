<?php

declare(strict_types=1);

namespace App\Platform\UseCases\User\Me\Dtos;

readonly class PurchasedProductDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public ?string $imageUrl,
        public array $imageUrls,
        public int $price,
        public array $deal,
    ) {
    }
}