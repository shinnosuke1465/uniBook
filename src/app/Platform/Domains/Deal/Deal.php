<?php

declare(strict_types=1);

namespace App\Platform\Domains\Deal;

use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\User\UserId;

readonly class Deal
{
    public function __construct(
        public DealId $id,
        public UserId $sellerId,
        public UserId $buyerId,
        public TextbookId $textbookId,
        public DealStatus $dealStatus,
    ) {
    }

    public static function create(
        UserId $sellerId,
        UserId $buyerId,
        TextbookId $textbookId,
        DealStatus $dealStatus,
    ): self {
        return new self(
            new DealId(),
            $sellerId,
            $buyerId,
            $textbookId,
            $dealStatus,
        );
    }
}