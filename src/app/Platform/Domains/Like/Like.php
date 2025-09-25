<?php

declare(strict_types=1);

namespace App\Platform\Domains\Like;

use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\User\UserId;

readonly class Like
{
    public function __construct(
        public LikeId $id,
        public UserId $userId,
        public TextbookId $textbookId,
    ) {
    }

    public static function create(
        UserId $userId,
        TextbookId $textbookId,
    ): self {
        return new self(
            new LikeId(),
            $userId,
            $textbookId,
        );
    }
}