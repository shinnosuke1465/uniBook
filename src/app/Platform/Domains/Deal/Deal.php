<?php

declare(strict_types=1);

namespace App\Platform\Domains\Deal;

use App\Platform\Domains\Textbook\TextbookId;

readonly class Deal
{
    public function __construct(
        public DealId $id,
        public Seller $seller,
        public Buyer $buyer,
        public TextbookId $textbookId,
        public DealStatus $dealStatus,
    ) {
    }

    public static function create(
        Seller $seller,
        Buyer $buyer,
        TextbookId $textbookId,
        DealStatus $dealStatus,
    ): self {
        return new self(
            new DealId(),
            $seller,
            $buyer,
            $textbookId,
            $dealStatus,
        );
    }
}