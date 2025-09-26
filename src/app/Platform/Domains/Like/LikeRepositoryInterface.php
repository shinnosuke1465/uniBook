<?php

namespace App\Platform\Domains\Like;

use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\User\UserId;

interface LikeRepositoryInterface
{
    public function insert(Like $like): void;
    public function delete(UserId $userId, TextbookId $textbookId): void;
    public function findByUserIdAndTextbookId(UserId $userId, TextbookId $textbookId): ?Like;
}