<?php

namespace App\Platform\Domains\Comment;

interface CommentRepositoryInterface
{
    public function insert(Comment $comment): void;
}