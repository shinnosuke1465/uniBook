<?php

declare(strict_types=1);

namespace App\Platform\Domains\Comment;

use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\User\UserId;

readonly class Comment
{
    public function __construct(
        public CommentId $id,
        public Text $text,
        public UserId $userId,
        public TextbookId $textbookId,
    ) {
    }

    public static function create(
        Text $text,
        UserId $userId,
        TextbookId $textbookId,
    ): self {
        return new self(
            new CommentId(),
            $text,
            $userId,
            $textbookId,
        );
    }
}