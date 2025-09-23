<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\Comment;

use App\Exceptions\DuplicateKeyException;
use App\Models\Comment as CommentDB;
use App\Platform\Domains\Comment\Comment;
use App\Platform\Domains\Comment\CommentId;
use App\Platform\Domains\Comment\CommentRepositoryInterface;

readonly class CommentRepository implements CommentRepositoryInterface
{
    /**
     * @throws DuplicateKeyException
     */
    public function insert(Comment $comment): void
    {
        if ($this->hasDuplicate($comment->id)) {
            throw new DuplicateKeyException('コメントが重複しています。');
        }
        CommentDB::create([
            'id' => $comment->id->value,
            'text' => $comment->text->value,
            'user_id' => $comment->userId->value,
            'textbook_id' => $comment->textbookId->value,
        ]);
    }

    private function hasDuplicate(CommentId $commentId): bool
    {
        return CommentDB::find($commentId->value) !== null;
    }
}