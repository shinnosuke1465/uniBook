<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\Comment;

use App\Exceptions\DomainException;
use App\Models\Comment as CommentDB;
use App\Platform\Domains\Comment\Comment;
use App\Platform\Domains\Comment\CommentId;
use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\User\UserId;

class CommentFactory
{
    /**
     * @throws DomainException
     */
    public static function create(
        CommentDB $commentDB
    ): Comment {
        return new Comment(
            new CommentId($commentDB->id),
            new Text($commentDB->text),
            new UserId($commentDB->user_id),
            new TextbookId($commentDB->textbook_id),
        );
    }
}