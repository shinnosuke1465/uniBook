<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Comment;

use App\Exceptions\DomainException;
use App\Platform\Domains\Comment\Comment;
use App\Platform\Domains\Comment\CommentId;
use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\User\UserId;

class TestCommentFactory
{
    /**
     * @throws DomainException
     */
    public static function create(
        CommentId $id = new CommentId(),
        Text $text = new Text('テストコメント'),
        UserId $userId = new UserId(),
        TextbookId $textbookId = new TextbookId(),
    ): Comment {
        return new Comment(
            $id,
            $text,
            $userId,
            $textbookId,
        );
    }
}